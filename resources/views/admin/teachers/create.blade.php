@extends('admin.layouts.app')

@section('title', 'Tambah Guru / Staf')
@section('page_title', 'Tambah Guru / Staf')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-chalkboard-teacher text-primary mr-2"></i>Tambah Data Dewan Guru / Staf
            </h4>
            <p class="text-muted small mb-0">
                Tambahkan profil tenaga pendidik atau staf kependidikan baru ke dalam direktori sekolah.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Guru & Staf
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.teachers.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Kolom Kiri: Biodata Guru / Staf -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card shadow-sm mb-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-id-card text-primary mr-2"></i>Biodata Tenaga Pendidik / Staf
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: Drs. H. Ahmad Sulaiman, M.Pd.">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Jabatan / Mata Pelajaran</label>
                            <input type="text" name="position" value="{{ old('position') }}"
                                class="form-control @error('position') is-invalid @enderror"
                                placeholder="Contoh: Kepala Sekolah / Guru Matematika">
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark">Biografi Singkat / Riwayat Pendidikan (Opsional)</label>
                            <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="5"
                                placeholder="Tuliskan profil singkat, bidang keahlian, atau riwayat pendidikan...">{{ old('bio') }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Foto Profil & Pengaturan -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-user-gear text-primary mr-2"></i>Foto & Klasifikasi
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Foto Profil</label>
                            <div id="teacher-preview-wrap" class="mb-2 text-center d-none">
                                <img id="teacher-preview-img" src="" alt="Preview"
                                    class="rounded border shadow-sm" style="width: 140px; height: 140px; object-fit: cover;">
                            </div>
                            <div class="p-3 rounded border bg-light text-center">
                                <i class="fas fa-user-circle fa-2x text-primary mb-2 d-block"></i>
                                <input type="file" name="photo" id="teacher-photo-input"
                                    accept="image/jpeg,image/png,image/jpg,image/webp"
                                    class="form-control-file @error('photo') is-invalid @enderror">
                                <small class="text-muted d-block mt-2">Format: JPG, PNG, WebP (Maks. 2MB).</small>
                            </div>
                            @error('photo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Kategori / Tipe <span class="text-danger">*</span></label>
                            <select name="type" required class="form-control @error('type') is-invalid @enderror">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="guru" {{ old('type', 'guru') === 'guru' ? 'selected' : '' }}>Dewan Guru (Tenaga Pendidik)</option>
                                <option value="staff" {{ old('type') === 'staff' ? 'selected' : '' }}>Staf Kependidikan / TU</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark">Urutan Tampil</label>
                            <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                                class="form-control @error('order') is-invalid @enderror">
                            <small class="text-muted">Angka lebih kecil akan ditampilkan lebih awal.</small>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Data Guru / Staf
                        </button>
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary btn-block mt-2 font-weight-bold">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.getElementById('teacher-photo-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const wrap = document.getElementById('teacher-preview-wrap');
    const img = document.getElementById('teacher-preview-img');
    if (file && wrap && img) {
        img.src = URL.createObjectURL(file);
        wrap.classList.remove('d-none');
    }
});
</script>
@endpush