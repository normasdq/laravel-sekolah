@extends('admin.layouts.app')

@section('title', 'Perhitungan & Ranking')
@section('page_title', 'Perhitungan & Ranking')

@push('styles')
<style>
    .spk-tabs-bar {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .spk-tabs-bar .nav-item {
        margin: 0;
    }
    .spk-tabs-bar .nav-link {
        border-radius: 10px !important;
        padding: 9px 16px !important;
        font-size: 0.84rem !important;
        font-weight: 700 !important;
        color: #475569 !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.15s ease !important;
        white-space: nowrap;
    }
    .spk-tabs-bar .nav-link:hover {
        color: #2563eb !important;
        border-color: #bfdbfe !important;
        background: #eff6ff !important;
    }
    .spk-tabs-bar .nav-link.active {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.25) !important;
    }
    .spk-tabs-bar .nav-link.active i {
        color: #ffffff !important;
    }
    .spk-table th,
    .spk-table td {
        padding: 14px 16px !important;
        vertical-align: middle !important;
    }
</style>
@endpush

@section('content')
    <!-- 1. Top Page Header -->
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center pt-1 mb-4" style="gap: 16px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-scale-balanced text-primary mr-2"></i>Perhitungan & Ranking
            </h4>
            <p class="text-muted small mb-0">
                Perhitungan <em>Simple Additive Weighting</em> (SAW) beserta matriks keputusan, normalisasi, dan rekomendasi kelulusan.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center flex-shrink-0" style="gap: 8px;">
            <a href="{{ route('admin.spk.criteria.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold text-nowrap">
                <i class="fas fa-sliders mr-1"></i> Kriteria & Bobot
            </a>
            <a href="{{ route('admin.spk.scores') }}" class="btn btn-info btn-sm shadow-sm font-weight-bold text-nowrap">
                <i class="fas fa-pen-to-square mr-1"></i> Input Nilai Siswa
            </a>
            <a href="{{ route('admin.spk.export-pdf') }}" class="btn btn-danger btn-sm shadow-sm font-weight-bold text-nowrap">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
            @if (auth()->user()->role === 'admin' && count($ranking) > 0)
                <button type="button" class="btn btn-success btn-sm shadow-sm font-weight-bold text-nowrap" data-toggle="modal" data-target="#applySawModal">
                    <i class="fas fa-check-double mr-1"></i> Terapkan Kelulusan
                </button>
            @endif
        </div>
    </div>

    <!-- 2. Ringkasan Statistik KPI SPK -->
    <div class="row mb-4">
        <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
            <div class="admin-kpi-card" style="border-left: 4px solid #0284c7;">
                <div>
                    <div class="admin-kpi-value" style="color: #0284c7;">{{ $summary['total_complete'] }}</div>
                    <p class="admin-kpi-label">Siswa Nilai Lengkap</p>
                </div>
                <div class="admin-kpi-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
            <div class="admin-kpi-card" style="border-left: 4px solid #16a34a;">
                <div>
                    <div class="admin-kpi-value" style="color: #16a34a;">{{ $summary['passed_count'] }}</div>
                    <p class="admin-kpi-label">Memenuhi Kriteria (&ge; {{ (int) round($threshold * 100) }})</p>
                </div>
                <div class="admin-kpi-icon" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3 mb-sm-0">
            <div class="admin-kpi-card" style="border-left: 4px solid #dc2626;">
                <div>
                    <div class="admin-kpi-value" style="color: #dc2626;">{{ $summary['failed_count'] }}</div>
                    <p class="admin-kpi-label">Belum Memenuhi (&lt; {{ (int) round($threshold * 100) }})</p>
                </div>
                <div class="admin-kpi-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-user-xmark"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="admin-kpi-card" style="border-left: 4px solid #d97706;">
                <div>
                    <div class="admin-kpi-value" style="color: #d97706;">{{ $summary['total_incomplete'] }}</div>
                    <p class="admin-kpi-label">Nilai Belum Lengkap</p>
                </div>
                <div class="admin-kpi-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Pengaturan Bobot Kriteria & Ambang Batas Kelulusan -->
    <div class="row mb-4">
        <!-- Kolom Kiri: Tabel Ringkas Bobot Kriteria Aktif -->
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card shadow-sm h-100 mb-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-weight-hanging mr-2 text-primary"></i>Bobot Kriteria Aktif (Total Normalisasi = 100)
                    </h3>
                    <a href="{{ route('admin.spk.criteria.index') }}" class="btn btn-outline-primary btn-xs font-weight-bold ml-auto">
                        <i class="fas fa-sliders mr-1"></i> Kelola Kriteria
                    </a>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 75px;" class="text-center">Kode</th>
                                <th>Nama Kriteria Penilaian</th>
                                <th style="width: 110px;" class="text-center">Atribut</th>
                                <th style="width: 120px;" class="text-center">Nilai Maks</th>
                                <th style="width: 150px;" class="text-center">Bobot Normalisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($criteria as $c)
                                @php
                                    $wInt = (int) round(($weights[$c->id] ?? 0) * 100);
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <span class="badge badge-primary px-2 py-1">{{ $c->code }}</span>
                                    </td>
                                    <td class="font-weight-bold text-dark">{{ $c->name }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $c->type === 'benefit' ? 'success' : 'warning' }} px-2 py-1">
                                            {{ ucfirst($c->type) }}
                                        </span>
                                    </td>
                                    <td class="text-center font-weight-bold text-muted">
                                        {{ (int) round($c->max_value) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info px-3 py-1 font-weight-bold">
                                            {{ $wInt }} ({{ $wInt }}%)
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Ambang Batas Kelulusan -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100 mb-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-bullseye mr-2 text-primary"></i>Ambang Batas Lulus
                    </h3>
                    <span class="badge badge-success px-2 py-1 ml-auto">Skor &ge; {{ (int) round($threshold * 100) }}</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="p-3 rounded border bg-light mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small text-muted font-weight-bold">Skor Minimal Kelulusan:</span>
                            <span class="h4 font-weight-bold text-success mb-0">{{ (int) round($threshold * 100) }} / 100</span>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            Calon siswa dengan Hasil Nilai Akhir (<strong>V</strong>) minimal <strong>{{ (int) round($threshold * 100) }}</strong> direkomendasikan <strong>Lulus Seleksi</strong>.
                        </p>
                    </div>

                    @if (auth()->user()->role === 'admin')
                        <form action="{{ route('admin.spk.threshold') }}" method="POST" class="mb-0">
                            @csrf
                            @method('PUT')
                            <label class="small font-weight-bold text-dark mb-2 d-block">Ubah Skor Minimal Lulus (Skala 1 – 100)</label>
                            <div class="input-group">
                                <input type="number" step="1" min="1" max="100" name="threshold"
                                    value="{{ (int) round($threshold * 100) }}"
                                    class="form-control font-weight-bold text-center" required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary font-weight-bold px-3">
                                        <i class="fas fa-save mr-1"></i> Simpan Ambang
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="p-2 rounded bg-light border text-center small text-muted">
                            Hanya Administrator yang dapat mengubah ambang batas kelulusan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Kartu Utama Tabel & Tahapan Perhitungan SAW -->
    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-ranking-star mr-2 text-primary"></i>Tahapan Matriks & Hasil Perankingan SAW
            </h3>
            <span class="badge badge-light border px-3 py-1 ml-auto">
                Skala Nilai Akhir: <strong>0 – 100</strong> (Bilangan Bulat)
            </span>
        </div>

        <!-- Navigasi Tab Tanpa Scrollbar Vertikal -->
        <ul class="nav nav-pills spk-tabs-bar" id="sawTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-ranking-link" data-toggle="pill" href="#tab-ranking" role="tab">
                    <i class="fas fa-trophy text-warning mr-1"></i> 1. Hasil Perankingan Akhir (V)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-matrix-link" data-toggle="pill" href="#tab-matrix" role="tab">
                    <i class="fas fa-table text-primary mr-1"></i> 2. Matriks Keputusan (X)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-norm-link" data-toggle="pill" href="#tab-norm" role="tab">
                    <i class="fas fa-divide text-info mr-1"></i> 3. Matriks Normalisasi (R)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-weighted-link" data-toggle="pill" href="#tab-weighted" role="tab">
                    <i class="fas fa-calculator text-success mr-1"></i> 4. Matriks Terbobot (W &times; R)
                </a>
            </li>
            @if (count($incomplete) > 0)
                <li class="nav-item">
                    <a class="nav-link text-danger" id="tab-incomplete-link" data-toggle="pill" href="#tab-incomplete" role="tab">
                        <i class="fas fa-exclamation-circle mr-1"></i> Belum Lengkap ({{ count($incomplete) }})
                    </a>
                </li>
            @endif
        </ul>

        <div class="card-body p-0">
            <div class="tab-content" id="sawTabContent">
                <!-- TAB 1: HASIL RANKING AKHIR -->
                <div class="tab-pane fade show active" id="tab-ranking" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle spk-table mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap" style="width: 90px">Peringkat</th>
                                    <th style="min-width: 240px;">Identitas Calon Siswa</th>
                                    @foreach ($criteria as $c)
                                        <th class="text-center text-nowrap" style="width: 95px;">
                                            <span class="text-primary font-weight-bold">{{ $c->code }}</span>
                                            <small class="d-block text-muted" style="font-size: 9.5px; text-transform: none;">W &times; R</small>
                                        </th>
                                    @endforeach
                                    <th class="text-center text-nowrap" style="width: 150px;">Nilai Akhir (V)</th>
                                    <th class="text-center text-nowrap" style="width: 170px;">Rekomendasi SAW</th>
                                    <th class="text-center text-nowrap" style="width: 150px;">Status Pendaftaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ranking as $row)
                                    @php
                                        $reg = $row['registration'];
                                    @endphp
                                    <tr>
                                        <td class="text-center text-nowrap font-weight-bold">
                                            @if ($row['rank'] === 1)
                                                <span class="badge badge-warning px-3 py-1"><i class="fas fa-crown mr-1"></i>#1</span>
                                            @elseif ($row['rank'] === 2)
                                                <span class="badge badge-secondary px-3 py-1">#2</span>
                                            @elseif ($row['rank'] === 3)
                                                <span class="badge badge-info px-3 py-1">#3</span>
                                            @else
                                                <span class="badge badge-light border px-3 py-1">#{{ $row['rank'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.registrations.show', $reg->id) }}" class="font-weight-bold text-dark d-block" style="font-size: 14px;">
                                                {{ $reg->name }}
                                            </a>
                                            <small class="text-muted text-nowrap">
                                                <code>{{ $reg->registration_number }}</code> &bull; NISN: {{ $reg->nisn }}
                                            </small>
                                        </td>
                                        @foreach ($criteria as $c)
                                            <td class="text-center text-nowrap font-weight-bold text-dark">
                                                {{ (int) round(($row['weighted_scores'][$c->id] ?? 0) * 100) }}
                                            </td>
                                        @endforeach
                                        <td class="text-center text-nowrap">
                                            <span class="badge badge-{{ $row['is_passed'] ? 'primary' : 'secondary' }} px-3 py-1 font-weight-bold" style="font-size: 0.9rem;">
                                                {{ (int) round($row['preference_score'] * 100) }} / 100
                                            </span>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            @if ($row['is_passed'])
                                                <span class="badge badge-success px-3 py-1">
                                                    <i class="fas fa-check-circle mr-1"></i> MEMENUHI (LULUS)
                                                </span>
                                            @else
                                                <span class="badge badge-danger px-3 py-1">
                                                    <i class="fas fa-times-circle mr-1"></i> TIDAK MEMENUHI
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <span class="badge badge-status badge-{{ $reg->status_color }}">{{ $reg->status_label }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 5 + $criteria->count() }}" class="text-center py-5 text-muted">
                                            <i class="fas fa-scale-balanced fa-2x mb-2 d-block opacity-50"></i>
                                            Belum ada data calon siswa dengan nilai kriteria yang lengkap untuk dihitung.
                                            <div class="mt-3">
                                                <a href="{{ route('admin.spk.scores') }}" class="btn btn-sm btn-primary font-weight-bold">
                                                    <i class="fas fa-pen-to-square mr-1"></i> Input Nilai Kriteria Sekarang
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: MATRIKS KEPUTUSAN (X) -->
                <div class="tab-pane fade" id="tab-matrix" role="tabpanel">
                    <div class="px-4 py-3 bg-light border-bottom small text-muted">
                        <i class="fas fa-info-circle text-primary mr-1"></i>
                        <strong>Tahap 1 — Matriks Keputusan (X):</strong> Nilai mentah setiap calon siswa pada masing-masing kriteria beserta batas nilai maksimum dan minimum kriteria.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle spk-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 65px;" class="text-center">No</th>
                                    <th style="min-width: 240px;">Alternatif (Calon Siswa)</th>
                                    @foreach ($criteria as $c)
                                        <th class="text-center text-nowrap">
                                            <span class="text-primary font-weight-bold">{{ $c->code }}</span> — {{ $c->name }}
                                            <small class="d-block text-muted" style="font-size: 10px; text-transform: none;">({{ ucfirst($c->type) }})</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ranking as $row)
                                    <tr>
                                        <td class="text-center text-muted font-weight-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="font-weight-bold text-dark d-block">{{ $row['registration']->name }}</span>
                                            <small class="text-muted"><code>{{ $row['registration']->registration_number }}</code> &bull; NISN: {{ $row['registration']->nisn }}</small>
                                        </td>
                                        @foreach ($criteria as $c)
                                            <td class="text-center font-weight-bold text-dark">{{ (int) round($row['raw_scores'][$c->id] ?? 0) }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 2 + $criteria->count() }}" class="text-center py-5 text-muted">Belum ada data matriks keputusan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (count($ranking) > 0)
                                <tfoot class="bg-light font-weight-bold border-top">
                                    <tr>
                                        <td colspan="2" class="text-right text-success">Batas Nilai Maksimum Kriteria (Max):</td>
                                        @foreach ($criteria as $c)
                                            <td class="text-center text-success">{{ (int) round($column_stats[$c->id]['max'] ?? 0) }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right text-danger">Batas Nilai Minimum Kriteria (Min):</td>
                                        @foreach ($criteria as $c)
                                            <td class="text-center text-danger">{{ (int) round($column_stats[$c->id]['min'] ?? 0) }}</td>
                                        @endforeach
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- TAB 3: MATRIKS NORMALISASI (R) -->
                <div class="tab-pane fade" id="tab-norm" role="tabpanel">
                    <div class="px-4 py-3 bg-light border-bottom small text-muted">
                        <i class="fas fa-info-circle text-info mr-1"></i>
                        <strong>Tahap 2 — Matriks Normalisasi (R):</strong>
                        Benefit: <code>R = (Nilai / Nilai Maksimum) &times; 100</code> &nbsp;|&nbsp; Cost: <code>R = (Nilai Minimum / Nilai) &times; 100</code>.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle spk-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 65px;" class="text-center">No</th>
                                    <th style="min-width: 240px;">Alternatif (Calon Siswa)</th>
                                    @foreach ($criteria as $c)
                                        <th class="text-center text-nowrap">
                                            <span class="text-primary font-weight-bold">{{ $c->code }}</span> (Normalisasi R{{ $loop->iteration }})
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ranking as $row)
                                    <tr>
                                        <td class="text-center text-muted font-weight-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="font-weight-bold text-dark d-block">{{ $row['registration']->name }}</span>
                                            <small class="text-muted"><code>{{ $row['registration']->registration_number }}</code> &bull; NISN: {{ $row['registration']->nisn }}</small>
                                        </td>
                                        @foreach ($criteria as $c)
                                            <td class="text-center font-weight-bold text-dark">{{ (int) round(($row['normalized_scores'][$c->id] ?? 0) * 100) }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 2 + $criteria->count() }}" class="text-center py-5 text-muted">Belum ada data normalisasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 4: MATRIKS TERBOBOT (W * R) -->
                <div class="tab-pane fade" id="tab-weighted" role="tabpanel">
                    <div class="px-4 py-3 bg-light border-bottom small text-muted">
                        <i class="fas fa-info-circle text-success mr-1"></i>
                        <strong>Tahap 3 — Perhitungan Skor Preferensi (V = &Sigma; Bobot &times; Normalisasi):</strong> Perkalian matriks ternormalisasi dengan bobot masing-masing kriteria (Skala 0 – 100).
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle spk-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 65px;" class="text-center">No</th>
                                    <th style="min-width: 240px;">Alternatif (Calon Siswa)</th>
                                    @foreach ($criteria as $c)
                                        <th class="text-center text-nowrap">
                                            <span class="text-primary font-weight-bold">{{ $c->code }}</span> (Bobot {{ (int) round(($weights[$c->id] ?? 0) * 100) }}%)
                                        </th>
                                    @endforeach
                                    <th class="text-center text-nowrap" style="width: 160px;">Hasil Nilai Akhir (V)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ranking as $row)
                                    <tr>
                                        <td class="text-center text-muted font-weight-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="font-weight-bold text-dark d-block">{{ $row['registration']->name }}</span>
                                            <small class="text-muted"><code>{{ $row['registration']->registration_number }}</code> &bull; NISN: {{ $row['registration']->nisn }}</small>
                                        </td>
                                        @foreach ($criteria as $c)
                                            <td class="text-center font-weight-bold text-dark">{{ (int) round(($row['weighted_scores'][$c->id] ?? 0) * 100) }}</td>
                                        @endforeach
                                        <td class="text-center text-nowrap">
                                            <span class="badge badge-primary px-3 py-1 font-weight-bold">
                                                {{ (int) round($row['preference_score'] * 100) }} / 100
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + $criteria->count() }}" class="text-center py-5 text-muted">Belum ada data matriks terbobot.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 5: DATA BELUM LENGKAP -->
                @if (count($incomplete) > 0)
                    <div class="tab-pane fade" id="tab-incomplete" role="tabpanel">
                        <div class="px-4 py-3 bg-light border-bottom small text-dark">
                            <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                            Calon siswa di bawah ini sudah terverifikasi berkasnya, namun belum memiliki nilai lengkap pada seluruh kriteria aktif sehingga belum diikutsertakan dalam perankingan.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle spk-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 65px;" class="text-center">No</th>
                                        <th style="min-width: 240px;">Identitas Calon Siswa</th>
                                        <th>Kriteria yang Belum Terisi</th>
                                        <th class="text-center text-nowrap" style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($incomplete as $inc)
                                        <tr>
                                            <td class="text-center text-muted font-weight-bold">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="font-weight-bold text-dark d-block">{{ $inc['registration']->name }}</span>
                                                <small class="text-muted"><code>{{ $inc['registration']->registration_number }}</code> &bull; NISN: {{ $inc['registration']->nisn }}</small>
                                            </td>
                                            <td>
                                                @foreach ($inc['missing_criteria'] as $mc)
                                                    <span class="badge badge-warning mr-1">{{ $mc }}</span>
                                                @endforeach
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <a href="{{ route('admin.spk.scores', ['search' => $inc['registration']->nisn]) }}" class="btn btn-xs btn-info font-weight-bold">
                                                    <i class="fas fa-pen mr-1"></i> Lengkapi Nilai
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (auth()->user()->role === 'admin' && count($ranking) > 0)
    <!-- Modal Konfirmasi Terapkan Kelulusan -->
    <div class="modal fade" id="applySawModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <form action="{{ route('admin.spk.apply') }}" method="POST">
                    @csrf
                    <input type="hidden" name="threshold" value="{{ $threshold }}">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-check-double mr-1"></i> Konfirmasi Penerapan Kelulusan SPK SAW</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="mb-3">Apakah Anda yakin ingin menerapkan hasil rekomendasi metode SAW ini ke status pendaftaran siswa?</p>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Ambang Batas Kelulusan (Skor Minimal)
                                <span class="badge badge-primary badge-pill">{{ (int) round($threshold * 100) }} / 100</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center text-success font-weight-bold">
                                Dinyatakan LULUS (Skor &ge; {{ (int) round($threshold * 100) }})
                                <span class="badge badge-success badge-pill">{{ $summary['passed_count'] }} Siswa</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center text-danger font-weight-bold">
                                Dinyatakan TIDAK LULUS (Skor &lt; {{ (int) round($threshold * 100) }})
                                <span class="badge badge-danger badge-pill">{{ $summary['failed_count'] }} Siswa</span>
                            </li>
                        </ul>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-dark mb-1">
                                <i class="fas fa-money-bill-wave text-success mr-1"></i> Nominal Biaya Daftar Ulang Default (Bagi Siswa Lulus)
                            </label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Rp</span>
                                </div>
                                <input type="number" name="default_fee" min="0" step="5000"
                                    value="{{ (int) ($default_fee ?? 150000) }}"
                                    class="form-control font-weight-bold" required>
                            </div>
                            <small class="text-muted">Otomatis ditetapkan ke siswa yang dinyatakan <strong>Lulus</strong> (jika biaya daftar ulangnya belum diatur manual) agar mereka dapat langsung membayar daftar ulang.</small>
                        </div>
                        <div class="alert alert-warning small mb-0">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Catatan:</strong> Pendaftar yang masih berstatus <em>Menunggu Verifikasi (Pending)</em> atau yang nilainya belum lengkap ({{ $summary['total_incomplete'] }} siswa) tidak akan diubah statusnya.
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm font-weight-bold px-3">
                            <i class="fas fa-check mr-1"></i> Ya, Terapkan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

