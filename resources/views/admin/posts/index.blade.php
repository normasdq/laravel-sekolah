@extends('admin.layouts.app')

@section('title', 'Artikel Berita')
@section('page_title', 'Artikel Berita')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-newspaper text-primary mr-2"></i>Artikel Berita Sekolah
            </h4>
            <p class="text-muted small mb-0">
                Kelola publikasi berita, pengumuman, dan artikel kegiatan sekolah.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <button type="submit" form="bulk-delete-form" id="bulk-delete-btn" class="btn btn-danger btn-sm shadow-sm font-weight-bold"
                style="display:none;" onclick="return confirm('Yakin hapus berita yang dipilih?')">
                <i class="fas fa-trash mr-1"></i> Hapus Terpilih
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm font-weight-bold">
                <i class="fas fa-tags mr-1"></i> Kelola Kategori
            </a>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-plus mr-1"></i> Tulis Berita Baru
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.posts.index') }}" class="row align-items-center" style="row-gap: 8px;">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari judul artikel berita...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Cari</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Status Publikasi --</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Terbit (Published)</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Konsep (Draft)</option>
                    </select>
                </div>
                @if (request('search') || request('status'))
                    <div class="col-md-3">
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo mr-1"></i> Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Berita -->
    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-list-ul text-primary mr-2"></i>Daftar Artikel Berita
            </h3>
            <span class="badge badge-light border px-2 py-1">
                Total: <strong>{{ $posts->total() }}</strong> Artikel
            </span>
        </div>
        <div class="card-body table-responsive p-0">
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.posts.bulk-delete') }}">
                @csrf
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 44px;" class="text-center"><input type="checkbox" id="select-all"></th>
                            <th style="width: 75px;" class="text-center">Gambar</th>
                            <th>Judul Artikel & Slug</th>
                            <th style="width: 160px;">Kategori</th>
                            <th style="width: 110px;" class="text-center">Status</th>
                            <th style="width: 130px;" class="text-center">Tanggal</th>
                            <th style="width: 110px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td class="text-center"><input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $post->id }}"></td>
                                <td class="text-center">
                                    @if ($post->image)
                                        <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" class="img-thumb">
                                    @else
                                        <div class="img-thumb d-inline-flex align-items-center justify-content-center bg-light text-muted">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('berita.detail', $post->slug) }}" target="_blank"
                                        class="font-weight-bold text-dark d-block">
                                        {{ Str::limit($post->title, 65) }}
                                    </a>
                                    <small class="text-muted"><i class="fas fa-link mr-1"></i>{{ Str::limit($post->slug, 55) }}</small>
                                </td>
                                <td>
                                    @if ($post->category)
                                        <span class="badge badge-info px-2 py-1">
                                            {{ $post->category->name }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">Tanpa Kategori</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($post->is_published)
                                        <span class="badge badge-success px-2 py-1">Terbit</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">Draft</span>
                                    @endif
                                </td>
                                <td class="text-center"><small class="text-muted font-weight-semibold">{{ $post->created_at->format('d M Y') }}</small></td>
                                <td class="text-center">
                                    <a href="{{ route('admin.posts.edit', $post) }}"
                                        class="btn btn-info btn-xs" title="Edit Berita">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus Berita"
                                        onclick="if(confirm('Yakin hapus berita ini?')) document.getElementById('delete-post-{{ $post->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-newspaper fa-2x mb-2 d-block opacity-50"></i>
                                    Tidak ada data artikel berita yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>

            @foreach($posts as $post)
                <form id="delete-post-{{ $post->id }}" method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="d-none">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        </div>
        @if ($posts->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">
                {{ $posts->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
