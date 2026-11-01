@extends('admin.layouts.app')

@section('title', 'Tulis Berita Baru')
@section('page_title', 'Tulis Berita Baru')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-newspaper text-primary mr-2"></i>Tulis Artikel Berita Baru
            </h4>
            <p class="text-muted small mb-0">
                Susun artikel berita, pengumuman, atau liputan kegiatan sekolah untuk dipublikasikan di website.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Berita
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Kolom Kiri: Konten Artikel -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card shadow-sm mb-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-pen-nib text-primary mr-2"></i>Konten Artikel Berita
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Judul Artikel Berita <span class="text-danger">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                class="form-control @error('title') is-invalid @enderror"
                                placeholder="Masukkan judul artikel berita...">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Slug URL (Opsional)</label>
                            <input type="text" name="slug" value="{{ old('slug') }}"
                                class="form-control @error('slug') is-invalid @enderror"
                                placeholder="Kosongkan untuk dibuat otomatis dari judul">
                            <small class="text-muted">Contoh: <code>prestasi-olimpiade-sains-nasional-2026</code></small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Isi Konten Berita <span class="text-danger">*</span></label>
                            <textarea name="content" required class="form-control tinymce @error('content') is-invalid @enderror" rows="14">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark">Ringkasan Singkat / Excerpt (Opsional)</label>
                            <textarea name="excerpt" class="form-control" rows="3"
                                placeholder="Tulis ringkasan singkat (1-2 kalimat) yang akan tampil pada daftar kartu berita...">{{ old('excerpt') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Pengaturan Publikasi & Media -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-sliders text-primary mr-2"></i>Pengaturan & Publikasi
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Kategori Berita</label>
                            <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach (\App\Models\Category::orderBy('name')->get() as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Gambar Sampul (Thumbnail)</label>
                            <div id="post-image-preview-wrap" class="mb-2 d-none">
                                <img id="post-image-preview" src="" alt="Preview"
                                    class="w-100 rounded border" style="max-height: 190px; object-fit: cover;">
                            </div>
                            <div class="p-3 rounded border bg-light text-center">
                                <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2 d-block"></i>
                                <input type="file" name="image" id="post-image-input"
                                    accept="image/jpeg,image/png,image/jpg,image/webp"
                                    class="form-control-file @error('image') is-invalid @enderror">
                                <small class="text-muted d-block mt-2">Format: JPG, PNG, WebP (Maks. 2MB).</small>
                            </div>
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0 pt-2 border-top">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_published" value="1" id="is_published"
                                    class="custom-control-input" {{ old('is_published', true) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="is_published">
                                    Publikasikan Sekarang
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Jika dinonaktifkan, artikel akan disimpan sebagai <strong>Draft</strong>.
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Artikel Berita
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-block mt-2 font-weight-bold">
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
document.getElementById('post-image-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const wrap = document.getElementById('post-image-preview-wrap');
    const img = document.getElementById('post-image-preview');
    if (file && wrap && img) {
        img.src = URL.createObjectURL(file);
        wrap.classList.remove('d-none');
    }
});
</script>
@endpush
