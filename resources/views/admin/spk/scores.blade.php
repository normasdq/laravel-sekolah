@extends('admin.layouts.app')

@section('title', 'Input Nilai Siswa')
@section('page_title', 'Input Nilai Siswa')

@push('styles')
<style>
    .scores-table th,
    .scores-table td {
        padding: 14px 14px !important;
        vertical-align: middle !important;
    }
    .scores-table thead th {
        background-color: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
</style>
@endpush

@section('content')
    <!-- 1. Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center pt-1 mb-4" style="gap: 16px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-pen-to-square text-primary mr-2"></i>Input Nilai Siswa
            </h4>
            <p class="text-muted small mb-0">
                Masukkan atau verifikasi nilai kriteria untuk pendaftar yang berkasnya telah diverifikasi.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center flex-shrink-0" style="gap: 8px;">
            <a href="{{ route('admin.spk.criteria.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold text-nowrap">
                <i class="fas fa-sliders mr-1"></i> Kriteria & Bobot
            </a>
            <a href="{{ route('admin.spk.index') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold text-nowrap">
                <i class="fas fa-trophy mr-1"></i> Lihat Perhitungan & Ranking
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong><i class="fas fa-exclamation-circle mr-1"></i> Gagal menyimpan nilai:</strong>
            <ul class="mb-0 mt-1 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $completeCount = ($rankingByRegId ?? collect())->count();
        $incompleteCount = max(0, $registrations->count() - $completeCount);
    @endphp

    <!-- 2. Ringkasan Status Input Nilai KPI -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="admin-kpi-card" style="border-left: 4px solid #334155;">
                <div>
                    <div class="admin-kpi-value" style="color: #0f172a;">{{ $registrations->count() }}</div>
                    <p class="admin-kpi-label">Total Peserta Terverifikasi</p>
                </div>
                <div class="admin-kpi-icon" style="background: #f1f5f9; color: #334155;">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="admin-kpi-card" style="border-left: 4px solid #16a34a;">
                <div>
                    <div class="admin-kpi-value" style="color: #16a34a;">{{ $completeCount }}</div>
                    <p class="admin-kpi-label">Data Nilai Sudah Lengkap</p>
                </div>
                <div class="admin-kpi-icon" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-kpi-card" style="border-left: 4px solid #d97706;">
                <div>
                    <div class="admin-kpi-value" style="color: #d97706;">{{ $incompleteCount }}</div>
                    <p class="admin-kpi-label">Data Nilai Belum Lengkap</p>
                </div>
                <div class="admin-kpi-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Card Utama: Filter Pencarian & Tabel Input Nilai Massal -->
    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-table-list text-primary mr-2"></i>Matriks Input Nilai Alternatif
            </h3>
            <div class="d-flex align-items-center ml-auto" style="gap: 10px;">
                <span class="badge badge-light border px-3 py-1 text-muted font-weight-semibold d-none d-sm-inline-block">
                    Ambang Kelulusan: <strong class="text-success">&ge; {{ (int) round(($threshold ?? 0.70) * 100) }} / 100</strong>
                </span>
                @if ($registrations->isNotEmpty() && $criteria->where('source', 'manual')->isNotEmpty())
                    <button type="submit" form="bulkScoresForm" class="btn btn-success btn-sm font-weight-bold shadow-sm text-nowrap">
                        <i class="fas fa-save mr-1"></i> Simpan Semua Nilai
                    </button>
                @endif
            </div>
        </div>

        <!-- Toolbar Pencarian Terintegrasi -->
        <div class="px-4 py-3 bg-light border-bottom">
            <form action="{{ route('admin.spk.scores') }}" method="GET" class="row align-items-center" style="row-gap: 8px;">
                <div class="col-md-6 col-lg-5">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control"
                            placeholder="Cari nama calon siswa, NISN, atau No. Pendaftaran..."
                            value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary font-weight-bold px-3">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
                @if (request()->filled('search'))
                    <div class="col-md-auto">
                        <a href="{{ route('admin.spk.scores') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo mr-1"></i> Reset Pencarian
                        </a>
                    </div>
                @endif
                <div class="col-md text-md-right small text-muted">
                    Menampilkan <strong>{{ $registrations->count() }}</strong> calon siswa terverifikasi
                </div>
            </form>
        </div>

        <!-- Form Tabel Input Nilai -->
        <form id="bulkScoresForm" action="{{ route('admin.spk.scores.save') }}" method="POST">
            @csrf
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped scores-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th style="min-width: 210px;">Identitas Calon Siswa</th>
                            @foreach ($criteria as $criterion)
                                @php
                                    $hasScale = ! empty($criterion->scale_options) && is_array($criterion->scale_options);
                                    $colWidth = $hasScale ? 'width: 210px; min-width: 195px;' : ($criterion->source === 'cbt_score' ? 'width: 130px;' : 'width: 145px;');
                                @endphp
                                <th class="text-center" style="{{ $colWidth }}">
                                    <div class="mb-1">
                                        <span class="badge badge-primary px-2 py-1">{{ $criterion->code }}</span>
                                        <span class="badge badge-light border text-dark">{{ (int) round($criterion->weight) }}%</span>
                                    </div>
                                    <div class="font-weight-bold text-dark" style="font-size: 11.5px; text-transform: none; letter-spacing: 0; line-height: 1.3;">
                                        {{ $criterion->name }}
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 10px; text-transform: none;">
                                        @if ($criterion->source === 'cbt_score')
                                            Otomatis CBT (0–100)
                                        @else
                                            Rentang: {{ (int) round($criterion->min_value) }} – {{ (int) round($criterion->max_value) }}
                                        @endif
                                    </small>
                                </th>
                            @endforeach
                            <th class="text-center" style="width: 140px;">
                                <div class="font-weight-bold text-primary" style="font-size: 11.5px; text-transform: none; letter-spacing: 0;">
                                    Hasil Nilai Akhir (V)
                                </div>
                                <small class="d-block text-muted mt-1" style="font-size: 10px; text-transform: none;">
                                    Lulus &ge; {{ (int) round(($threshold ?? 0.70) * 100) }} / 100
                                </small>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registrations as $index => $reg)
                            @php
                                $scoresMap = $reg->sawScores->keyBy('saw_criterion_id');
                                $rankRow = ($rankingByRegId ?? collect())->get($reg->id);
                            @endphp
                            <tr>
                                <td class="text-center text-muted font-weight-bold">{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.registrations.show', $reg) }}" class="font-weight-bold text-dark d-block mb-1">
                                        {{ $reg->name }}
                                    </a>
                                    <div class="small text-muted mb-1">
                                        <span class="font-weight-semibold text-secondary">{{ $reg->registration_number }}</span>
                                        <span class="mx-1">&bull;</span>
                                        <span>NISN: {{ $reg->nisn }}</span>
                                    </div>
                                    <span class="badge badge-status badge-{{ $reg->status_color }}" style="font-size: 10px; padding: 2px 8px;">
                                        {{ $reg->status_label }}
                                    </span>
                                </td>
                                @foreach ($criteria as $criterion)
                                    <td class="text-center align-middle">
                                        @if ($criterion->source === 'cbt_score')
                                            @if ($reg->examResult && $reg->examResult->score !== null)
                                                <span class="badge badge-info px-3 py-1 font-weight-bold text-nowrap" style="font-size: 0.85rem;">
                                                    <i class="fas fa-laptop-code mr-1"></i> {{ (int) round($reg->examResult->score) }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning px-2 py-1 text-nowrap">
                                                    <i class="fas fa-clock mr-1"></i> Belum CBT
                                                </span>
                                            @endif
                                        @else
                                            @php
                                                $existingScore = $scoresMap->get($criterion->id)?->value;
                                                $fieldValue = old("scores.{$reg->id}.{$criterion->id}", $existingScore !== null ? (int) round((float) $existingScore) : null);
                                            @endphp

                                            @if (! empty($criterion->scale_options) && is_array($criterion->scale_options))
                                                <select name="scores[{{ $reg->id }}][{{ $criterion->id }}]"
                                                    class="form-control form-control-sm font-weight-semibold {{ $fieldValue === null ? 'border-warning bg-light' : '' }}"
                                                    style="min-width: 180px;">
                                                    <option value="">-- Pilih Skala --</option>
                                                    @foreach ($criterion->scale_options as $opt)
                                                        <option value="{{ $opt['value'] }}" {{ (string) $fieldValue !== '' && (float) $fieldValue == (float) $opt['value'] ? 'selected' : '' }}>
                                                            {{ $opt['label'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="number" step="1"
                                                    min="{{ (int) round($criterion->min_value) }}"
                                                    max="{{ (int) round($criterion->max_value) }}"
                                                    name="scores[{{ $reg->id }}][{{ $criterion->id }}]"
                                                    value="{{ $fieldValue }}"
                                                    placeholder="{{ (int) round($criterion->min_value) }}–{{ (int) round($criterion->max_value) }}"
                                                    class="form-control form-control-sm text-center font-weight-bold mx-auto {{ $fieldValue === null ? 'border-warning' : '' }}"
                                                    style="max-width: 100px;">
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center align-middle text-nowrap">
                                    @if ($rankRow)
                                        <span class="badge badge-{{ $rankRow['is_passed'] ? 'primary' : 'secondary' }} px-3 py-1 font-weight-bold mb-1 d-inline-block" style="font-size: 0.85rem;">
                                            {{ (int) round($rankRow['preference_score'] * 100) }} / 100
                                        </span>
                                        <div class="small font-weight-bold {{ $rankRow['is_passed'] ? 'text-success' : 'text-danger' }}">
                                            {{ $rankRow['is_passed'] ? 'Lulus' : 'Tidak Lulus' }} (#{{ $rankRow['rank'] }})
                                        </div>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">Belum Lengkap</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + $criteria->count() }}" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-check fa-2x mb-2 d-block opacity-50"></i>
                                    Belum ada pendaftar berstatus <strong>Terverifikasi</strong>. Silakan verifikasi berkas pendaftar terlebih dahulu pada menu <a href="{{ route('admin.registrations.index') }}">Data Pendaftar</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($registrations->isNotEmpty() && $criteria->where('source', 'manual')->isNotEmpty())
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Pastikan seluruh kolom nilai kriteria telah terisi sebelum menekan tombol simpan.
                    </small>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold px-4 shadow-sm ml-auto">
                        <i class="fas fa-save mr-1"></i> Simpan Semua Nilai Kriteria
                    </button>
                </div>
            @endif
        </form>
    </div>
@endsection
