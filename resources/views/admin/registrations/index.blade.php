@extends('admin.layouts.app')

@section('page_title', 'Data Pendaftar SPMB')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-user-graduate text-primary mr-2"></i>Data Pendaftar SPMB
            </h4>
            <p class="text-muted small mb-0">
                Verifikasi berkas pendaftaran, pantau nilai seleksi SAW, dan kelola status penerimaan calon siswa baru.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <button type="submit" form="bulk-delete-form" id="bulk-delete-btn" class="btn btn-danger btn-sm shadow-sm font-weight-bold"
                style="display:none;" onclick="return confirm('Yakin hapus data yang dipilih?')">
                <i class="fas fa-trash mr-1"></i> Hapus Terpilih
            </button>
            <form method="POST" action="{{ route('admin.registrations.export') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                    <i class="fas fa-file-csv mr-1"></i> Export CSV
                </button>
            </form>
            <form method="POST" action="{{ route('admin.registrations.export-pdf') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm shadow-sm font-weight-bold">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </button>
            </form>
        </div>
    </div>

    <!-- Status KPI Cards (3x2 Balanced Grid) -->
    <div class="row mb-1">
        <div class="col-lg-4 col-sm-6 mb-3">
            <div class="admin-kpi-card" style="border-left: 4px solid #334155;">
                <div>
                    <div class="admin-kpi-value" style="color: #0f172a;">{{ $statusCounts['total'] }}</div>
                    <p class="admin-kpi-label">Total Pendaftar</p>
                </div>
                <div class="admin-kpi-icon" style="background: #f1f5f9; color: #334155;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 mb-3">
            <div class="admin-kpi-card" style="border-left: 4px solid #d97706;">
                <div>
                    <div class="admin-kpi-value" style="color: #d97706;">{{ $statusCounts['pending'] }}</div>
                    <p class="admin-kpi-label">Menunggu Verifikasi</p>
                </div>
                <div class="admin-kpi-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 mb-3">
            <div class="admin-kpi-card" style="border-left: 4px solid #0284c7;">
                <div>
                    <div class="admin-kpi-value" style="color: #0284c7;">{{ $statusCounts['verified'] }}</div>
                    <p class="admin-kpi-label">Terverifikasi Berkas</p>
                </div>
                <div class="admin-kpi-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 mb-3">
            <div class="admin-kpi-card" style="border-left: 4px solid #16a34a;">
                <div>
                    <div class="admin-kpi-value" style="color: #16a34a;">{{ $statusCounts['lulus'] ?? 0 }}</div>
                    <p class="admin-kpi-label">Dinyatakan Lulus</p>
                </div>
                <div class="admin-kpi-icon" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 mb-3">
            <div class="admin-kpi-card" style="border-left: 4px solid #64748b;">
                <div>
                    <div class="admin-kpi-value" style="color: #64748b;">{{ $statusCounts['tidak_lulus'] ?? 0 }}</div>
                    <p class="admin-kpi-label">Tidak Lulus Seleksi</p>
                </div>
                <div class="admin-kpi-icon" style="background: #f1f5f9; color: #64748b;">
                    <i class="fas fa-user-xmark"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 mb-3">
            <div class="admin-kpi-card" style="border-left: 4px solid #dc2626;">
                <div>
                    <div class="admin-kpi-value" style="color: #dc2626;">{{ $statusCounts['rejected'] }}</div>
                    <p class="admin-kpi-label">Berkas Ditolak</p>
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
            <form method="GET" action="{{ route('admin.registrations.index') }}" class="row align-items-center" style="row-gap: 8px;">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari nama pendaftar, NISN, atau asal sekolah...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Status Pendaftaran --</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi Berkas</option>
                        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Dinyatakan Lulus</option>
                        <option value="tidak_lulus" {{ request('status') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Berkas Ditolak</option>
                    </select>
                </div>
                @if (request('search') || request('status'))
                    <div class="col-md-3">
                        <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-secondary btn-sm">
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
                <i class="fas fa-list-ul mr-2 text-primary"></i>Daftar Pendaftar SPMB
            </h3>
            <span class="badge badge-light border px-2 py-1">
                Total: <strong>{{ $registrations->total() }}</strong> Pendaftar
            </span>
        </div>
        <div class="card-body table-responsive p-0">
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.registrations.bulk-delete') }}">
                @csrf
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 44px;" class="text-center"><input type="checkbox" id="select-all"></th>
                            <th style="width: 55px;" class="text-center">No</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Asal Sekolah</th>
                            <th>Telepon</th>
                            <th class="text-center" style="width: 140px;">Nilai Akhir SAW</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th class="text-center" style="width: 120px;">Tanggal</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $reg)
                            @php
                                $sawItem = ($sawMap ?? collect())->get($reg->id);
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $reg->id }}">
                                </td>
                                <td class="text-center text-muted font-weight-bold">
                                    {{ $registrations->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark d-block">{{ Str::limit($reg->name, 32) }}</span>
                                    <small class="text-muted">{{ $reg->registration_number ?? '-' }}</small>
                                </td>
                                <td><code class="px-2 py-1 rounded bg-light border">{{ $reg->nisn }}</code></td>
                                <td>{{ Str::limit($reg->school_origin, 28) }}</td>
                                <td><small class="text-muted font-weight-semibold">{{ $reg->phone }}</small></td>
                                <td class="text-center">
                                    @if ($sawItem)
                                        <span class="badge badge-{{ $sawItem['is_passed'] ? 'primary' : 'secondary' }} px-2 py-1 font-weight-bold">
                                            {{ (int) round($sawItem['preference_score'] * 100) }} / 100
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-status badge-{{ $reg->status_color }}">
                                        {{ $reg->status_label }}
                                    </span>
                                </td>
                                <td class="text-center text-muted small">
                                    {{ $reg->created_at->format('d M Y') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.registrations.show', $reg) }}"
                                        class="btn btn-info btn-xs" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus"
                                        onclick="if(confirm('Yakin hapus data pendaftar ini?')) document.getElementById('delete-reg-{{ $reg->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-graduate fa-2x mb-2 d-block opacity-50"></i>
                                    Tidak ada data pendaftar yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>

            @foreach($registrations as $reg)
                <form id="delete-reg-{{ $reg->id }}" method="POST" action="{{ route('admin.registrations.destroy', $reg) }}" class="d-none">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        </div>
        @if ($registrations->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">
                {{ $registrations->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
