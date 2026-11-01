@extends('admin.layouts.app')

@section('title', 'Tambah Foto Galeri')
@section('page_title', 'Tambah Foto Galeri')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-images text-primary mr-2"></i>Tambah Foto Galeri & Fasilitas
            </h4>
            <p class="text-muted small mb-0">
                Unggah foto dokumentasi kegiatan siswa atau sarana fasilitas unggulan sekolah.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Galeri
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.galleries.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Kolom Kiri: Informasi Foto -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card shadow-sm mb-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-file-lines text-primary mr-2"></i>Informasi Foto & Fasilitas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Judul Foto / Nama Fasilitas</label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                class="form-control @error('title') is-invalid @enderror"
                                placeholder="Contoh: Laboratorium Komputer Terpadu / Kegiatan Pramuka">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark">Deskripsi Singkat (Opsional)</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5"
                                placeholder="Tuliskan keterangan singkat mengenai foto kegiatan atau fasilitas ini...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: File Foto & Urutan -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-camera text-primary mr-2"></i>Unggah Foto & Urutan
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">File Foto <span class="text-danger">*</span></label>
                            <div id="gallery-preview-wrap" class="mb-2 d-none">
                                <img id="gallery-preview-img" src="" alt="Preview"
                                    class="w-100 rounded border" style="height: 190px; object-fit: cover;">
                            </div>
                            <div class="p-3 rounded border bg-light text-center">
                                <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2 d-block"></i>
                                <input type="file" name="image" id="gallery-image-input"
                                    accept="image/jpeg,image/png,image/jpg,image/webp" required
                                    class="form-control-file @error('image') is-invalid @enderror">
                                <small class="text-muted d-block mt-2">Format: JPG, PNG, WebP (Maks. 5MB).</small>
                            </div>
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark">Urutan Tampil</label>
                            <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                                class="form-control @error('order') is-invalid @enderror">
                            <small class="text-muted">Angka lebih kecil akan ditampilkan lebih awal di galeri.</small>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Foto Galeri
                        </button>
                        <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary btn-block mt-2 font-weight-bold">
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
document.getElementById('gallery-image-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const wrap = document.getElementById('gallery-preview-wrap');
    const img = document.getElementById('gallery-preview-img');
    if (file && wrap && img) {
        img.src = URL.createObjectURL(file);
        wrap.classList.remove('d-none');
    }
});
</script>
@endpush