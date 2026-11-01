<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Registration;
use App\Services\SettingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    private function configureMidtrans(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index()
    {
        $registration = Registration::where('user_id', Auth::id())->first();

        if (! $registration) {
            return redirect()->route('dashboard')->with('error', 'Anda belum mendaftar.');
        }

        if ($registration->status !== 'lulus') {
            return redirect()->route('dashboard')->with('error', 'Pembayaran daftar ulang hanya dapat dilakukan setelah dinyatakan lulus.');
        }

        if (! $registration->payment_amount || (float) $registration->payment_amount <= 0) {
            $defaultFee = (float) app(SettingService::class)->get('spmb_re_registration_fee', 150000);
            if ($defaultFee > 0) {
                $registration->update([
                    'payment_amount' => $defaultFee,
                    'payment_status' => $registration->payment_status ?: 'unpaid',
                ]);
                $registration->refresh();
            } else {
                return redirect()->route('dashboard')->with('error', 'Biaya daftar ulang belum ditentukan. Hubungi panitia.');
            }
        }

        $payment = Payment::where('registration_id', $registration->id)->latest()->first();

        if ($payment && $payment->status === 'pending' && $payment->transaction_id) {
            try {
                $this->configureMidtrans();
                $transactionStatus = Transaction::status($payment->transaction_id);
                $status = $transactionStatus->transaction_status ?? '';
                $fraud = $transactionStatus->fraud_status ?? '';

                $isPaid = ($status === 'settlement') || ($status === 'capture' && $fraud === 'accept');

                if ($isPaid) {
                    DB::transaction(function () use ($payment, $registration, $transactionStatus) {
                        $payment->update([
                            'status' => 'success',
                            'paid_at' => now(),
                            'raw_response' => json_decode(json_encode($transactionStatus), true),
                        ]);
                        $registration->update([
                            'payment_status' => 'paid',
                            'paid_at' => now(),
                        ]);
                    });
                }
            } catch (\Throwable $e) {
                // Midtrans API tidak tersedia atau transaksi belum dibuat di Midtrans
                Log::warning('Midtrans status check warning: '.$e->getMessage());
            }
        }

        return view('pages.pendaftar.payment.index', compact('registration', 'payment'));
    }

    public function create()
    {
        $registration = Registration::where('user_id', Auth::id())->first();

        if (! $registration || ! $registration->payment_amount) {
            return redirect()->route('dashboard')->with('error', 'Data tidak ditemukan.');
        }

        if ($registration->status !== 'lulus') {
            return response()->json(['error' => 'Pembayaran daftar ulang hanya dapat dilakukan setelah dinyatakan lulus.'], 403);
        }

        if ($registration->payment_status === 'paid') {
            return redirect()->route('dashboard')->with('success', 'Pembayaran sudah lunas.');
        }

        $isSimulator = (bool) config('services.midtrans.simulator', false);

        // Gunakan token yang sudah ada jika masih pending dan belum kedaluwarsa
        $existingPayment = Payment::where('registration_id', $registration->id)
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if ($existingPayment && $existingPayment->snap_token) {
            return response()->json([
                'snap_token' => $existingPayment->snap_token,
                'is_demo' => $isSimulator || str_starts_with($existingPayment->snap_token, 'DEMO-'),
                'order_id' => $existingPayment->transaction_id,
                'amount_formatted' => $registration->payment_amount_formatted,
            ]);
        }

        $orderId = 'REG-'.$registration->registration_number.'-'.time();

        if ($isSimulator) {
            $snapToken = 'DEMO-SNAP-'.bin2hex(random_bytes(8));

            Payment::create([
                'registration_id' => $registration->id,
                'user_id' => Auth::id(),
                'amount' => $registration->payment_amount,
                'status' => 'pending',
                'transaction_id' => $orderId,
                'snap_token' => $snapToken,
                'expired_at' => now()->addHours(24),
            ]);

            $registration->update(['payment_status' => 'pending']);

            return response()->json([
                'snap_token' => $snapToken,
                'is_demo' => true,
                'order_id' => $orderId,
                'amount_formatted' => $registration->payment_amount_formatted,
            ]);
        }

        $this->configureMidtrans();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $registration->payment_amount,
            ],
            'customer_details' => [
                'first_name' => $registration->name,
                'email' => $registration->email ?? auth()->user()->email,
                'phone' => $registration->phone,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            Payment::create([
                'registration_id' => $registration->id,
                'user_id' => Auth::id(),
                'amount' => $registration->payment_amount,
                'status' => 'pending',
                'transaction_id' => $orderId,
                'snap_token' => $snapToken,
                'expired_at' => now()->addHours(24),
            ]);

            $registration->update(['payment_status' => 'pending']);

            return response()->json(['snap_token' => $snapToken, 'is_demo' => false]);
        } catch (\Throwable $e) {
            $msg = 'Gagal memproses pembayaran ke payment gateway. Silakan coba kembali.';
            if (str_contains($e->getMessage(), '401') || str_contains($e->getMessage(), 'Unauthorized')) {
                $msg = 'Midtrans API 401 Unauthorized: Kredensial MIDTRANS_SERVER_KEY di file .env tidak valid atau belum terdaftar di Midtrans.';
            }

            return response()->json(['error' => $msg], 500);
        }
    }

    public function demoPay(Request $request)
    {
        $registration = Registration::where('user_id', Auth::id())->first();

        if (! $registration) {
            return response()->json(['error' => 'Data pendaftaran tidak ditemukan.'], 404);
        }

        $payment = Payment::where('registration_id', $registration->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (! $payment) {
            $orderId = 'REG-'.$registration->registration_number.'-'.time();
            $payment = Payment::create([
                'registration_id' => $registration->id,
                'user_id' => Auth::id(),
                'amount' => $registration->payment_amount,
                'status' => 'pending',
                'transaction_id' => $orderId,
                'snap_token' => 'DEMO-'.bin2hex(random_bytes(8)),
                'expired_at' => now()->addHours(24),
            ]);
        }

        $method = $request->input('method', 'QRIS (Simulasi Midtrans)');

        DB::transaction(function () use ($payment, $registration, $method) {
            $payment->update([
                'status' => 'success',
                'method' => $method,
                'paid_at' => now(),
                'raw_response' => [
                    'mode' => 'simulator',
                    'transaction_status' => 'settlement',
                    'payment_type' => $method,
                    'gross_amount' => (string) $payment->amount,
                    'transaction_time' => now()->toDateTimeString(),
                ],
            ]);

            $registration->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);
        });

        session()->flash('success', 'Pembayaran daftar ulang (Simulasi) berhasil diverifikasi! Anda kini resmi terdaftar.');

        return response()->json([
            'success' => true,
            'redirect_url' => route('dashboard'),
        ]);
    }

    public function callback(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash('sha512', $request->order_id.$request->status_code.$request->gross_amount.$serverKey);

        if (! hash_equals($hashed, (string) $request->signature_key)) {
            Log::warning('Midtrans callback signature mismatch for order: '.$request->order_id);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = Payment::where('transaction_id', $request->order_id)->first();
        if (! $payment) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Verifikasi nominal agar tidak ada manipulasi nilai
        if ((int) round($request->gross_amount) !== (int) round($payment->amount)) {
            Log::warning("Midtrans callback amount mismatch for order {$request->order_id}: expected {$payment->amount}, got {$request->gross_amount}");

            return response()->json(['message' => 'Gross amount mismatch'], 400);
        }

        $registration = $payment->registration;

        DB::transaction(function () use ($payment, $registration, $request) {
            // Hindari memproses ulang transaksi yang sudah lunas
            if ($payment->status === 'success') {
                return;
            }

            $trxStatus = $request->transaction_status;
            $fraudStatus = $request->fraud_status;

            if ($trxStatus === 'settlement' || ($trxStatus === 'capture' && $fraudStatus === 'accept')) {
                $payment->update([
                    'status' => 'success',
                    'method' => $request->payment_type ?? $payment->method,
                    'paid_at' => now(),
                    'raw_response' => $request->all(),
                ]);
                $registration?->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);
            } elseif ($trxStatus === 'expire') {
                $payment->update(['status' => 'expired']);
                $registration?->update(['payment_status' => 'unpaid']);
            } elseif (in_array($trxStatus, ['deny', 'cancel'])) {
                $payment->update(['status' => 'failed']);
                $registration?->update(['payment_status' => 'unpaid']);
            } elseif ($trxStatus === 'pending') {
                $payment->update([
                    'status' => 'pending',
                    'method' => $request->payment_type ?? $payment->method,
                ]);
                $registration?->update(['payment_status' => 'pending']);
            }
        });

        return response()->json(['message' => 'OK']);
    }

    public function success()
    {
        $registration = Registration::where('user_id', Auth::id())->first();

        if ($registration) {
            $payment = Payment::where('registration_id', $registration->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            // Lakukan verifikasi riil ke Midtrans API, jangan langsung menandai paid
            if ($payment && $payment->transaction_id) {
                try {
                    $this->configureMidtrans();
                    $transactionStatus = Transaction::status($payment->transaction_id);
                    $status = $transactionStatus->transaction_status ?? '';
                    $fraud = $transactionStatus->fraud_status ?? '';

                    $isPaid = ($status === 'settlement') || ($status === 'capture' && $fraud === 'accept');

                    if ($isPaid) {
                        DB::transaction(function () use ($payment, $registration, $transactionStatus) {
                            $payment->update([
                                'status' => 'success',
                                'paid_at' => now(),
                                'raw_response' => json_decode(json_encode($transactionStatus), true),
                            ]);
                            $registration->update([
                                'payment_status' => 'paid',
                                'paid_at' => now(),
                            ]);
                        });

                        return redirect()->route('dashboard')->with('success', 'Pembayaran daftar ulang berhasil terverifikasi! Selamat bergabung.');
                    }
                } catch (\Throwable $e) {
                    Log::warning('Midtrans verification error in success page: '.$e->getMessage());
                }
            }
        }

        return redirect()->route('dashboard')->with('info', 'Pembayaran sedang diverifikasi oleh sistem. Status akan diperbarui secara otomatis begitu konfirmasi diterima.');
    }

    public function downloadInvoice()
    {
        $registration = Registration::where('user_id', Auth::id())->first();
        if (! $registration) {
            return redirect()->route('dashboard')->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        $payment = Payment::where('registration_id', $registration->id)
            ->where('status', 'success')
            ->latest()
            ->first();

        if (! $payment) {
            return redirect()->route('payment.index')->with('error', 'Belum ada pembayaran yang lunas.');
        }

        $settings = app(SettingService::class);

        $pdf = Pdf::loadView('pages.pendaftar.pdf.invoice', compact('payment', 'registration', 'settings'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice_'.$payment->transaction_id.'.pdf');
    }

    public function unfinish()
    {
        return redirect()->route('payment.index')->with('error', 'Pembayaran belum selesai. Silakan coba lagi.');
    }

    public function error()
    {
        return redirect()->route('payment.index')->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }
}
