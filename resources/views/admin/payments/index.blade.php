@extends('admin.layouts.app')

@section('page_title', 'Data Pembayaran SPMB')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-credit-card text-primary mr-2"></i>Histori Pembayaran SPMB
            </h4>
            <p class="text-muted small mb-0">
                Pantau transaksi pembayaran daftar ulang calon siswa baru serta unduh laporan rekapitulasi pembayaran.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <button type="submit" form="bulk-delete-form" id="bulk-delete-btn" class="btn btn-danger btn-sm shadow-sm font-weight-bold"
                style="display:none;" onclick="return confirm('Yakin hapus data yang dipilih?')">
                <i class="fas fa-trash mr-1"></i> Hapus Terpilih
            </button>
            <form method="POST" action="{{ route('admin.payments.export') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                    <i class="fas fa-file-csv mr-1"></i> Export CSV
                </button>
            </form>
            <form method="POST" action="{{ route('admin.payments.export-pdf') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm shadow-sm font-weight-bold">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </button>
            </form>
        </div>
    </div>

    <!-- Status KPI Cards -->
    <div class="row mb-1">
        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="admin-kpi-card" style="border-left: 4px solid #334155;">
                <div>
                    <div class="admin-kpi-value" style="color: #0f172a;">{{ $statusCounts['total'] }}</div>
                    <p class="admin-kpi-label">Total Transaksi</p>
                </div>
                <div class="admin-kpi-icon" style="background: #f1f5f9; color: #334155;">
                    <i class="fas fa-credit-card"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="admin-kpi-card" style="border-left: 4px solid #16a34a;">
                <div>
                    <div class="admin-kpi-value" style="color: #16a34a;">{{ $statusCounts['success'] }}</div>
                    <p class="admin-kpi-label">Pembayaran Berhasil</p>
                </div>
                <div class="admin-kpi-icon" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="admin-kpi-card" style="border-left: 4px solid #d97706;">
                <div>
                    <div class="admin-kpi-value" style="color: #d97706;">{{ $statusCounts['pending'] }}</div>
                    <p class="admin-kpi-label">Menunggu Pembayaran</p>
                </div>
                <div class="admin-kpi-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="admin-kpi-card" style="border-left: 4px solid #dc2626;">
                <div>
                    <div class="admin-kpi-value" style="color: #dc2626;">{{ $statusCounts['expired'] + $statusCounts['failed'] }}</div>
                    <p class="admin-kpi-label">Gagal / Kedaluwarsa</p>
                </div>
                <div class="admin-kpi-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="row align-items-center" style="row-gap: 8px;">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari nama siswa, NISN, atau no. pendaftaran...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Status Pembayaran --</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Berhasil</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                    </select>
                </div>
                @if (request('search') || request('status'))
                    <div class="col-md-3">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo mr-1"></i> Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-list-ul mr-2 text-primary"></i>Daftar Transaksi Pembayaran
            </h3>
            <span class="badge badge-light border px-2 py-1">
                Total: <strong>{{ $payments->total() }}</strong> Transaksi
            </span>
        </div>
        <div class="card-body table-responsive p-0">
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.payments.bulk-delete') }}">
                @csrf
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 44px;" class="text-center"><input type="checkbox" id="select-all"></th>
                            <th style="width: 55px;" class="text-center">No</th>
                            <th>Pendaftar</th>
                            <th>No. Pendaftaran</th>
                            <th>NISN</th>
                            <th>Nominal Tagihan</th>
                            <th class="text-center" style="width: 130px;">Status</th>
                            <th>Metode</th>
                            <th class="text-center" style="width: 140px;">Tanggal</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $payment->id }}">
                                </td>
                                <td class="text-center text-muted font-weight-bold">
                                    {{ $payments->firstItem() + $loop->index }}
                                </td>
                                <td class="font-weight-bold text-dark">
                                    {{ Str::limit($payment->registration->name ?? '-', 30) }}
                                </td>
                                <td><code class="px-2 py-1 rounded bg-light border">{{ $payment->registration->registration_number ?? '-' }}</code></td>
                                <td>{{ $payment->registration->nisn ?? '-' }}</td>
                                <td class="text-nowrap font-weight-bold text-dark">{{ 'Rp ' . number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @php
                                        $badgeMap = [
                                            'success' => ['badge-success', 'Berhasil'],
                                            'pending' => ['badge-warning', 'Menunggu'],
                                            'expired' => ['badge-secondary', 'Kedaluwarsa'],
                                            'failed' => ['badge-danger', 'Gagal'],
                                        ];
                                        $badge = $badgeMap[$payment->status] ?? ['badge-secondary', $payment->status];
                                    @endphp
                                    <span class="badge {{ $badge[0] }} px-2 py-1">{{ $badge[1] }}</span>
                                </td>
                                <td><small class="text-muted font-weight-semibold">{{ $payment->method ?? '-' }}</small></td>
                                <td class="text-center text-muted small">
                                    {{ $payment->paid_at?->format('d M Y H:i') ?? $payment->created_at->format('d M Y') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.payments.show', $payment) }}"
                                        class="btn btn-info btn-xs" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus"
                                        onclick="if(confirm('Yakin hapus data pembayaran ini?')) document.getElementById('delete-payment-{{ $payment->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-receipt fa-2x mb-2 d-block opacity-50"></i>
                                    Belum ada data transaksi pembayaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>

            @foreach($payments as $payment)
                <form id="delete-payment-{{ $payment->id }}" method="POST" action="{{ route('admin.payments.destroy', $payment->id) }}" class="d-none">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        </div>
        @if ($payments->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">
                {{ $payments->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
