@extends('admin.layouts.app')

@section('title', 'Edit Artikel Berita')
@section('page_title', 'Edit Artikel Berita')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-edit text-primary mr-2"></i>Edit Artikel Berita
            </h4>
            <p class="text-muted small mb-0">
                Perbarui isi berita, kategori, gambar sampul, atau status publikasi artikel.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <a href="{{ route('berita.detail', $post->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-external-link-alt mr-1"></i> Lihat di Website
            </a>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Berita
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                            <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                                class="form-control @error('title') is-invalid @enderror">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Slug URL</label>
                            <input type="text" name="slug" value="{{ old('slug', $post->slug) }}"
                                class="form-control @error('slug') is-invalid @enderror">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Isi Konten Berita <span class="text-danger">*</span></label>
                            <textarea name="content" required class="form-control tinymce @error('content') is-invalid @enderror" rows="14">{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark">Ringkasan Singkat / Excerpt (Opsional)</label>
                            <textarea name="excerpt" class="form-control" rows="3"
                                placeholder="Ringkasan singkat berita...">{{ old('excerpt', $post->excerpt) }}</textarea>
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
                                        {{ (string) old('category_id', $post->category_id) === (string) $cat->id ? 'selected' : '' }}>
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
                            <div id="post-image-preview-wrap" class="mb-2 {{ $post->image ? '' : 'd-none' }}">
                                <img id="post-image-preview"
                                    src="{{ $post->image ? Storage::url($post->image) : '' }}"
                                    alt="{{ $post->title }}"
                                    class="w-100 rounded border" style="max-height: 190px; object-fit: cover;">
                            </div>
                            <div class="p-3 rounded border bg-light text-center">
                                <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2 d-block"></i>
                                <input type="file" name="image" id="post-image-input"
                                    accept="image/jpeg,image/png,image/jpg,image/webp"
                                    class="form-control-file @error('image') is-invalid @enderror">
                                <small class="text-muted d-block mt-2">Kosongkan jika tidak ingin mengganti gambar sampul.</small>
                            </div>
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0 pt-2 border-top">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_published" value="1" id="is_published"
                                    class="custom-control-input"
                                    {{ old('is_published', $post->is_published) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="is_published">
                                    Publikasikan Artikel
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Nonaktifkan apabila ingin menyimpan sebagai <strong>Draft</strong>.
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
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
