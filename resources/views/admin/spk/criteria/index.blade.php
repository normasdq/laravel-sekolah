@extends('admin.layouts.app')

@section('title', 'Data Kriteria & Bobot')
@section('page_title', 'Data Kriteria & Bobot')

@section('content')
    <!-- 1. Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center pt-1 mb-4" style="gap: 16px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-sliders text-primary mr-2"></i>Data Kriteria & Bobot
            </h4>
            <p class="text-muted small mb-0">
                Kelola kriteria penilaian, jenis atribut (Benefit/Cost), rentang nilai, serta bobot kepentingan seleksi.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center flex-shrink-0" style="gap: 8px;">
            <a href="{{ route('admin.spk.scores') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold text-nowrap">
                <i class="fas fa-pen-to-square mr-1"></i> Input Nilai Siswa
            </a>
            <a href="{{ route('admin.spk.index') }}" class="btn btn-outline-primary btn-sm shadow-sm font-weight-bold text-nowrap">
                <i class="fas fa-trophy mr-1"></i> Perhitungan & Ranking
            </a>
            <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold text-nowrap" data-toggle="modal" data-target="#createCriterionModal">
                <i class="fas fa-plus mr-1"></i> Tambah Kriteria Baru
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong><i class="fas fa-exclamation-triangle mr-1"></i> Terdapat kesalahan input:</strong>
            <ul class="mb-0 mt-1 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2. Info Ringkasan Bobot KPI -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="admin-kpi-card" style="border-left: 4px solid #2563eb;">
                <div>
                    <div class="admin-kpi-value" style="color: #2563eb;">{{ $criteria->where('is_active', true)->count() }} Kriteria</div>
                    <p class="admin-kpi-label">Kriteria Penilaian Aktif</p>
                </div>
                <div class="admin-kpi-icon" style="background: #dbeafe; color: #2563eb;">
                    <i class="fas fa-list-check"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="admin-kpi-card" style="border-left: 4px solid #16a34a;">
                <div>
                    <div class="admin-kpi-value" style="color: #16a34a;">{{ (int) round($totalActiveWeight) }}%</div>
                    <p class="admin-kpi-label">Total Bobot Aktif (&Sigma;W)</p>
                </div>
                <div class="admin-kpi-icon" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-weight-hanging"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-kpi-card" style="border-left: 4px solid #d97706;">
                <div>
                    <div class="admin-kpi-value" style="color: #d97706;">{{ $totalActiveWeight > 0 ? '100' : '0' }}</div>
                    <p class="admin-kpi-label">Total Normalisasi Bobot (Skala 100)</p>
                </div>
                <div class="admin-kpi-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-scale-balanced"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Kriteria -->
    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center" style="gap: 8px;">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-list-ul text-primary mr-2"></i>Daftar Kriteria Penilaian SAW
            </h3>
            <span class="badge badge-light border px-3 py-1 ml-auto">
                Rumus Normalisasi Bobot: <code>W = (Bobot / Total Bobot) &times; 100</code>
            </span>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="text-center">Kode</th>
                        <th style="min-width: 240px;">Nama & Deskripsi Kriteria</th>
                        <th class="text-center" style="width: 115px;">Atribut</th>
                        <th class="text-center" style="width: 155px;">Bobot & Normalisasi</th>
                        <th class="text-center" style="width: 170px;">Sumber & Rentang Nilai</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 105px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($criteria as $criterion)
                        @php
                            $normWeight = ($criterion->is_active && $totalActiveWeight > 0)
                                ? ($criterion->weight / $totalActiveWeight)
                                : 0;
                        @endphp
                        <tr>
                            <td class="text-center">
                                <span class="badge badge-primary px-2 py-1 font-weight-bold">{{ $criterion->code }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark mb-1">{{ $criterion->name }}</div>
                                @if ($criterion->description)
                                    <small class="text-muted d-block" style="line-height: 1.35;">{{ $criterion->description }}</small>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                @if ($criterion->type === 'benefit')
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-arrow-up mr-1"></i>Benefit</span>
                                @else
                                    <span class="badge badge-warning px-2 py-1"><i class="fas fa-arrow-down mr-1"></i>Cost</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge badge-info px-2 py-1 font-weight-bold">
                                    Bobot: {{ (int) round($normWeight * 100) }} ({{ (int) round($criterion->weight) }}%)
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="mb-1">
                                    @if ($criterion->source === 'cbt_score')
                                        <span class="badge badge-primary px-2 py-1"><i class="fas fa-laptop-code mr-1"></i>Otomatis CBT</span>
                                    @else
                                        <span class="badge badge-light border px-2 py-1"><i class="fas fa-keyboard mr-1"></i>Input Panitia</span>
                                    @endif
                                </div>
                                <small class="text-muted font-weight-semibold">
                                    Rentang: {{ (int) round($criterion->min_value) }} – {{ (int) round($criterion->max_value) }}
                                </small>
                            </td>
                            <td class="text-center text-nowrap">
                                @if ($criterion->is_active)
                                    <span class="badge badge-success px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <button type="button" class="btn btn-info btn-xs mr-1"
                                    data-toggle="modal" data-target="#editCriterionModal-{{ $criterion->id }}"
                                    title="Edit Kriteria">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-xs" title="Hapus Kriteria"
                                    onclick="if(confirm('Hapus kriteria {{ $criterion->code }} ({{ $criterion->name }})? Semua nilai siswa pada kriteria ini juga akan terhapus.')) document.getElementById('delete-criterion-{{ $criterion->id }}').submit();">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-sliders fa-2x mb-2 d-block opacity-50"></i>
                                Belum ada kriteria penilaian. Silakan tambahkan kriteria baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form Hapus & Modal Edit Kriteria (Diletakkan di luar tabel agar struktur DOM valid) -->
    @foreach ($criteria as $criterion)
        <form id="delete-criterion-{{ $criterion->id }}" action="{{ route('admin.spk.criteria.destroy', $criterion->id) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>

        <div class="modal fade" id="editCriterionModal-{{ $criterion->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                    <form action="{{ route('admin.spk.criteria.update', $criterion->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-light">
                            <h5 class="modal-title font-weight-bold text-dark">
                                <i class="fas fa-edit text-primary mr-2"></i>Edit Kriteria {{ $criterion->code }} — {{ $criterion->name }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold small text-dark">Kode Kriteria <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control" value="{{ $criterion->code }}" required maxlength="10">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold small text-dark">Nama Kriteria <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ $criterion->name }}" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold small text-dark">Urutan Tampil</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ $criterion->sort_order }}" min="0">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold small text-dark">Jenis Atribut <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="benefit" {{ $criterion->type === 'benefit' ? 'selected' : '' }}>Benefit (Makin Besar Makin Baik)</option>
                                        <option value="cost" {{ $criterion->type === 'cost' ? 'selected' : '' }}>Cost (Makin Kecil Makin Baik)</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold small text-dark">Bobot (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="1" name="weight" class="form-control" value="{{ (int) round($criterion->weight) }}" required min="1">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold small text-dark">Sumber Nilai <span class="text-danger">*</span></label>
                                    <select name="source" class="form-control" required>
                                        <option value="manual" {{ $criterion->source === 'manual' ? 'selected' : '' }}>Input Manual oleh Panitia</option>
                                        <option value="cbt_score" {{ $criterion->source === 'cbt_score' ? 'selected' : '' }}>Otomatis dari Skor Ujian CBT</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold small text-dark">Nilai Minimum <span class="text-danger">*</span></label>
                                    <input type="number" step="1" name="min_value" class="form-control" value="{{ (int) round($criterion->min_value) }}" required min="0">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold small text-dark">Nilai Maksimum <span class="text-danger">*</span></label>
                                    <input type="number" step="1" name="max_value" class="form-control" value="{{ (int) round($criterion->max_value) }}" required min="1">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold small text-dark">Keterangan / Deskripsi</label>
                                <textarea name="description" class="form-control" rows="2">{{ $criterion->description }}</textarea>
                            </div>

                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="isActive-{{ $criterion->id }}" name="is_active" value="1" {{ $criterion->is_active ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="isActive-{{ $criterion->id }}">Kriteria Aktif dalam Perhitungan SAW</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-3">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Tambah Kriteria Baru -->
    <div class="modal fade" id="createCriterionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <form action="{{ route('admin.spk.criteria.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-weight-bold text-dark">
                            <i class="fas fa-plus-circle text-primary mr-2"></i>Tambah Kriteria SAW Baru
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold small text-dark">Kode Kriteria <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" placeholder="C5" value="{{ old('code') }}" required maxlength="10">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small text-dark">Nama Kriteria <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Jarak Rumah ke Sekolah" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold small text-dark">Urutan Tampil</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $criteria->count() + 1) }}" min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold small text-dark">Jenis Atribut <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" required>
                                    <option value="benefit" selected>Benefit (Makin Besar Makin Baik)</option>
                                    <option value="cost">Cost (Makin Kecil Makin Baik)</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold small text-dark">Bobot (%) <span class="text-danger">*</span></label>
                                <input type="number" step="1" name="weight" class="form-control" placeholder="20" value="{{ old('weight', 10) }}" required min="1">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold small text-dark">Sumber Nilai <span class="text-danger">*</span></label>
                                <select name="source" class="form-control" required>
                                    <option value="manual" selected>Input Manual oleh Panitia</option>
                                    <option value="cbt_score">Otomatis dari Skor Ujian CBT</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small text-dark">Nilai Minimum <span class="text-danger">*</span></label>
                                <input type="number" step="1" name="min_value" class="form-control" value="{{ old('min_value', 0) }}" required min="0">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small text-dark">Nilai Maksimum <span class="text-danger">*</span></label>
                                <input type="number" step="1" name="max_value" class="form-control" value="{{ old('max_value', 100) }}" required min="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Keterangan / Deskripsi</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Penjelasan cara penilaian kriteria...">{{ old('description') }}</textarea>
                        </div>

                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="isActiveNew" name="is_active" value="1" checked>
                            <label class="custom-control-label font-weight-bold text-dark" for="isActiveNew">Kriteria Aktif dalam Perhitungan SAW</label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-3">
                            <i class="fas fa-save mr-1"></i> Simpan Kriteria
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

