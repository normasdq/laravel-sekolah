@extends('layouts.app')

@section('title', 'Pembayaran - SMP Tunas Harapan')

@section('content')
<div class="min-h-screen bg-white pt-28 pb-12">
    <div class="max-w-2xl mx-auto px-6 lg:px-8">

        <div class="text-center mb-8 fade-up visible">
            @if (config('services.midtrans.simulator'))
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 border border-amber-200 text-amber-800 mb-3 shadow-sm">
                <i class="fa-solid fa-flask text-amber-600"></i>
                <span>Mode Simulasi Pembayaran (Demo) Aktif</span>
            </div>
            @endif
            <h1 class="font-display text-3xl font-bold text-dark-900">Pembayaran Daftar Ulang</h1>
            <p class="text-dark-500 mt-1">Selesaikan pembayaran daftar ulang setelah dinyatakan lulus seleksi.</p>
        </div>

        @if (session('error'))
        <div class="p-4 mb-6 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm">
            {{ session('error') }}
        </div>
        @endif

        @if ($payment && $payment->status === 'success')
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm text-center">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-check text-2xl text-emerald-500"></i>
            </div>
            <h3 class="font-display font-bold text-xl text-dark-900 mb-2">Pembayaran Lunas!</h3>
            <p class="text-dark-500 mb-4">Terima kasih, pembayaran Anda telah dikonfirmasi.</p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary-500 text-white font-bold rounded-xl hover:bg-primary-600 transition-all">
                    Ke Dashboard <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="{{ route('payment.invoice') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-white text-dark-700 font-bold rounded-xl border border-gray-200 hover:bg-gray-50 transition-all">
                    <i class="fa-solid fa-download"></i> Unduh Invoice (PDF)
                </a>
            </div>
        </div>
        @elseif ($registration->payment_status === 'pending')
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm text-center">
            <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-clock text-2xl text-amber-500"></i>
            </div>
            <h3 class="font-display font-bold text-xl text-dark-900 mb-2">Menunggu Pembayaran</h3>
            <p class="text-dark-500 mb-6">Pembayaran sedang diproses. Klik tombol di bawah untuk melanjutkan pembayaran.</p>
            <button id="pay-button" onclick="payNow()"
                class="inline-flex items-center gap-2 px-8 py-3.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-500 shadow-lg shadow-primary-500/25 transition-all">
                <i class="fa-solid fa-credit-card"></i>
                Lanjutkan Pembayaran
            </button>
        </div>
        @else
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8">
                <h3 class="font-display font-bold text-lg text-dark-900 mb-6">Detail Pembayaran</h3>

                <div class="flex justify-between items-center py-4 border-b border-gray-100">
                    <span class="text-dark-500">Nomor Pendaftaran</span>
                    <span class="font-bold text-dark-900">{{ $registration->registration_number }}</span>
                </div>
                <div class="flex justify-between items-center py-4 border-b border-gray-100">
                    <span class="text-dark-500">Nama</span>
                    <span class="font-bold text-dark-900">{{ $registration->name }}</span>
                </div>
                <div class="flex justify-between items-center py-4 border-b border-gray-100">
                    <span class="text-dark-500">Biaya Daftar Ulang</span>
                    <span class="font-bold text-2xl text-primary-600">{{ $registration->payment_amount_formatted }}</span>
                </div>
                <div class="flex justify-between items-center py-4">
                    <span class="text-dark-500">Status</span>
                    <span
                        class="px-4 py-1.5 rounded-full text-xs font-bold uppercase bg-{{ $registration->payment_status_color }}-100 text-{{ $registration->payment_status_color }}-700">
                        {{ $registration->payment_status_label }}
                    </span>
                </div>
            </div>

            <div class="px-8 pb-8">
                <button id="pay-button" onclick="payNow()"
                    class="w-full py-4 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-500 shadow-lg shadow-primary-500/25 transition-all flex items-center justify-center gap-2 text-lg">
                    <i class="fa-solid fa-credit-card"></i>
                    Bayar Sekarang
                </button>
                <p class="text-[11px] text-dark-400 text-center mt-3">
                    @if (config('services.midtrans.simulator'))
                        Simulasi pembayaran aktif untuk kemudahan pengujian tanpa kartu kredit/VA asli.
                    @else
                        Pembayaran diproses melalui Midtrans. Didukung QRIS, Virtual Account, dan metode lainnya.
                    @endif
                </p>
            </div>
        </div>
        @endif

        <div class="text-center mt-6">
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Modal Simulasi Midtrans (Demo Mode) -->
<div id="demo-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-dark-950/70 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-gray-100 relative animate-fade-in">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-flask"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-base text-dark-900">Simulasi Midtrans Pay</h3>
                    <p class="text-[11px] text-dark-500">Sandbox Testing Environment</p>
                </div>
            </div>
            <button type="button" onclick="closeDemoModal()" class="w-8 h-8 rounded-lg text-dark-400 hover:text-dark-700 hover:bg-gray-100 flex items-center justify-center transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="bg-gray-50 rounded-2xl p-4 mb-5 border border-gray-200/80 space-y-2">
            <div class="flex justify-between text-xs">
                <span class="text-dark-500">Order ID:</span>
                <span id="demo-order-id" class="font-mono font-semibold text-dark-800">-</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-dark-500">Pendaftar:</span>
                <span class="font-semibold text-dark-900">{{ $registration->name }}</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                <span class="text-xs font-bold text-dark-700">Total Tagihan:</span>
                <span id="demo-amount" class="font-display font-extrabold text-lg text-primary-600">{{ $registration->payment_amount_formatted }}</span>
            </div>
        </div>

        <div class="space-y-3 mb-6">
            <label class="block text-xs font-bold text-dark-600 uppercase tracking-wider">Pilih Metode Simulasi</label>
            <div class="grid grid-cols-1 gap-2.5 text-xs">
                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 bg-white hover:border-primary-400 cursor-pointer transition-all">
                    <input type="radio" name="demo_method" value="QRIS (Gopay/ShopeePay)" checked class="text-primary-600 focus:ring-primary-500">
                    <div class="flex-1 flex items-center justify-between">
                        <span class="font-bold text-dark-800">QRIS (GoPay / ShopeePay / Dana)</span>
                        <i class="fa-solid fa-qrcode text-primary-500"></i>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 bg-white hover:border-primary-400 cursor-pointer transition-all">
                    <input type="radio" name="demo_method" value="BCA Virtual Account" class="text-primary-600 focus:ring-primary-500">
                    <div class="flex-1 flex items-center justify-between">
                        <span class="font-bold text-dark-800">BCA Virtual Account</span>
                        <i class="fa-solid fa-building-columns text-blue-600"></i>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 bg-white hover:border-primary-400 cursor-pointer transition-all">
                    <input type="radio" name="demo_method" value="Mandiri Bill Payment" class="text-primary-600 focus:ring-primary-500">
                    <div class="flex-1 flex items-center justify-between">
                        <span class="font-bold text-dark-800">Mandiri Bill Payment</span>
                        <i class="fa-solid fa-money-bill-wave text-amber-600"></i>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex flex-col gap-2.5">
            <button type="button" id="demo-confirm-btn" onclick="submitDemoPayment()"
                class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-600/25 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>Simulasikan Pembayaran Sukses (Lunas)</span>
            </button>
            <button type="button" onclick="closeDemoModal()"
                class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-dark-700 font-semibold text-xs rounded-xl transition-all">
                Tutup / Nanti Saja
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    function payNow() {
        const btn = document.getElementById('pay-button');
        if (!btn) return;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

        fetch('{{ route('payment.create') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-credit-card"></i> Bayar Sekarang';

            if (data.is_demo) {
                // Tampilkan Modal Simulasi Pembayaran
                document.getElementById('demo-order-id').innerText = data.order_id || 'ORDER-DEMO';
                if (data.amount_formatted) {
                    document.getElementById('demo-amount').innerText = data.amount_formatted;
                }
                document.getElementById('demo-modal').classList.remove('hidden');
                return;
            }

            if (data.snap_token) {
                if (typeof snap !== 'undefined' && snap.pay) {
                    snap.pay(data.snap_token, {
                        onSuccess: function() {
                            window.location.href = '{{ route('payment.success') }}';
                        },
                        onPending: function() {
                            window.location.href = '{{ route('payment.unfinish') }}';
                        },
                        onError: function() {
                            window.location.href = '{{ route('payment.error') }}';
                        },
                        onClose: function() {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fa-solid fa-credit-card"></i> Bayar Sekarang';
                        }
                    });
                } else {
                    // Fallback jika library Snap gagal di-load
                    document.getElementById('demo-order-id').innerText = data.order_id || 'ORDER-DEMO';
                    document.getElementById('demo-modal').classList.remove('hidden');
                }
            } else {
                alert(data.error || 'Gagal memproses pembayaran. Silakan coba kembali.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-credit-card"></i> Bayar Sekarang';
            alert('Terjadi kesalahan jaringan atau server. Silakan coba lagi.');
        });
    }

    function closeDemoModal() {
        document.getElementById('demo-modal').classList.add('hidden');
    }

    function submitDemoPayment() {
        const btn = document.getElementById('demo-confirm-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses Pelunasan...';

        const selectedMethod = document.querySelector('input[name="demo_method"]:checked')?.value || 'QRIS (Simulasi)';

        fetch('{{ route('payment.demo.pay') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ method: selectedMethod })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url || '{{ route('dashboard') }}';
            } else {
                alert(data.error || 'Gagal memproses simulasi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Simulasikan Pembayaran Sukses (Lunas)';
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan koneksi.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Simulasikan Pembayaran Sukses (Lunas)';
        });
    }
</script>
@endpush
