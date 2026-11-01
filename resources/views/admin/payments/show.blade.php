@extends('admin.layouts.app')

@section('page_title', 'Detail Pembayaran')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-receipt text-primary mr-2"></i>Detail Transaksi Pembayaran
            </h4>
            <p class="text-muted small mb-0">
                Rincian transaksi pembayaran daftar ulang siswa baru beserta status integrasi pembayaran.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Histori Pembayaran
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-credit-card mr-2 text-primary"></i>Informasi Pembayaran #{{ $payment->transaction_id }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" style="width: 180px;">ID Transaksi</td>
                            <td class="font-weight-bold">{{ $payment->transaction_id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @php
                                    $badgeMap = [
                                        'success' => ['bg-success', 'Berhasil'],
                                        'pending' => ['bg-warning', 'Menunggu'],
                                        'expired' => ['bg-secondary', 'Kedaluwarsa'],
                                        'failed' => ['bg-danger', 'Gagal'],
                                    ];
                                    $badge = $badgeMap[$payment->status] ?? ['bg-secondary', $payment->status];
                                @endphp
                                <span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jumlah</td>
                            <td class="font-weight-bold text-lg">{{ 'Rp ' . number_format($payment->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Metode</td>
                            <td>{{ $payment->method ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu Dibayar</td>
                            <td>{{ $payment->paid_at?->format('d F Y H:i:s') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu Kedaluwarsa</td>
                            <td>{{ $payment->expired_at?->format('d F Y H:i:s') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat</td>
                            <td>{{ $payment->created_at->format('d F Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-user-graduate mr-1"></i>Data Pendaftar</h5>
                        </div>
                        <div class="card-body py-2">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 120px;">Nama</td>
                                    <td class="font-weight-bold">{{ $payment->registration->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NISN</td>
                                    <td>{{ $payment->registration->nisn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. Daftar</td>
                                    <td><code>{{ $payment->registration->registration_number ?? '-' }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Telepon</td>
                                    <td>{{ $payment->registration->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email</td>
                                    <td>{{ $payment->registration->email ?? ($payment->user?->email ?? '-') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if ($payment->raw_response)
                        <div class="card bg-light mt-3">
                            <div class="card-header">
                                <h5 class="card-title"><i class="fas fa-code mr-1"></i>Response Midtrans</h5>
                            </div>
                            <div class="card-body py-2">
                                <pre class="mb-0" style="font-size: 11px; max-height: 200px; overflow-y: auto;">{{ json_encode($payment->raw_response, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
