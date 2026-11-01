@extends('admin.layouts.app')

@section('title', 'Galeri & Fasilitas')
@section('page_title', 'Galeri & Fasilitas')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-images text-primary mr-2"></i>Galeri & Fasilitas Sekolah
            </h4>
            <p class="text-muted small mb-0">
                Kelola dokumentasi foto kegiatan, sarana, dan fasilitas unggulan sekolah.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <button type="submit" form="bulk-delete-form" id="bulk-delete-btn" class="btn btn-danger btn-sm shadow-sm font-weight-bold"
                style="display:none;" onclick="return confirm('Yakin hapus foto yang dipilih?')">
                <i class="fas fa-trash mr-1"></i> Hapus Terpilih
            </button>
            <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-plus mr-1"></i> Tambah Foto Galeri
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center" style="gap: 10px;">
                <input type="checkbox" id="select-all" title="Pilih Semua">
                <h3 class="card-title font-weight-bold mb-0">Daftar Foto Galeri</h3>
            </div>
            <span class="badge badge-light border px-2 py-1">
                Total: <strong>{{ $galleries->total() }}</strong> Foto
            </span>
        </div>
        <div class="card-body">
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.galleries.bulk-delete') }}">
                @csrf
                <div class="row">
                    @forelse($galleries as $gallery)
                        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                            <div class="card h-100 mb-0 shadow-sm position-relative overflow-hidden" style="border-radius: 14px;">
                                <div class="position-relative" style="height: 170px; background: #f1f5f9;">
                                    <div class="position-absolute" style="top: 10px; left: 10px; z-index: 2; background: rgba(255,255,255,0.9); border-radius: 6px; padding: 2px 6px;">
                                        <input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $gallery->id }}">
                                    </div>
                                    <span class="badge badge-secondary position-absolute" style="top: 10px; right: 10px; z-index: 2;">
                                        Urutan #{{ $gallery->order }}
                                    </span>
                                    <img src="{{ Storage::url($gallery->image) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $gallery->title }}">
                                </div>
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <h6 class="font-weight-bold text-dark mb-1 text-truncate" title="{{ $gallery->title ?? 'Tanpa Judul' }}">
                                            {{ $gallery->title ?? 'Tanpa Judul' }}
                                        </h6>
                                        @if ($gallery->description)
                                            <p class="text-muted small mb-2">{{ Str::limit($gallery->description, 60) }}</p>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center pt-2 border-top mt-2" style="gap: 6px;">
                                        <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-info btn-xs">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-danger btn-xs"
                                            onclick="if(confirm('Yakin hapus foto ini?')) document.getElementById('delete-gallery-{{ $gallery->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="fas fa-images fa-2x mb-2 d-block opacity-50"></i>
                            Belum ada foto galeri yang diunggah.
                        </div>
                    @endforelse
                </div>
            </form>

            @foreach($galleries as $gallery)
                <form id="delete-gallery-{{ $gallery->id }}" method="POST" action="{{ route('admin.galleries.destroy', $gallery) }}" class="d-none">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        </div>
        @if ($galleries->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">
                {{ $galleries->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection