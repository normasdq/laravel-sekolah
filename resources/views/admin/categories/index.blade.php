@extends('admin.layouts.app')

@section('title', 'Kategori Berita')
@section('page_title', 'Kategori Berita')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-tags text-primary mr-2"></i>Kategori Berita
            </h4>
            <p class="text-muted small mb-0">
                Kelola pengelompokan topik dan kategori artikel berita sekolah.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <button type="submit" form="bulk-delete-form" id="bulk-delete-btn" class="btn btn-danger btn-sm shadow-sm font-weight-bold"
                style="display:none;" onclick="return confirm('Yakin hapus kategori yang dipilih?')">
                <i class="fas fa-trash mr-1"></i> Hapus Terpilih
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Form Tambah Kategori -->
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="card shadow-sm mb-0">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-plus-circle text-primary mr-2"></i>Tambah Kategori Baru
                    </h3>
                </div>
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: Prestasi Siswa" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted d-block mt-2">Slug URL akan dibuat secara otomatis dari nama kategori.</small>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Kategori -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-list-ul text-primary mr-2"></i>Daftar Kategori Berita
                    </h3>
                    <span class="badge badge-light border px-2 py-1">
                        Total: <strong>{{ $categories->total() }}</strong> Kategori
                    </span>
                </div>
                <div class="card-body table-responsive p-0">
                    <form id="bulk-delete-form" method="POST" action="{{ route('admin.categories.bulk-delete') }}">
                        @csrf
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 44px;" class="text-center"><input type="checkbox" id="select-all"></th>
                                    <th style="width: 55px;" class="text-center">No</th>
                                    <th>Nama Kategori</th>
                                    <th>Slug URL</th>
                                    <th class="text-center" style="width: 140px;">Jumlah Berita</th>
                                    <th class="text-center" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $cat)
                                    <tr>
                                        <td class="text-center"><input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $cat->id }}"></td>
                                        <td class="text-center text-muted font-weight-bold">{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                                        <td class="font-weight-bold text-dark">{{ $cat->name }}</td>
                                        <td><code class="px-2 py-1 rounded bg-light border">{{ $cat->slug }}</code></td>
                                        <td class="text-center">
                                            <span class="badge badge-info px-2 py-1">{{ $cat->posts_count }} Artikel</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-info btn-xs" title="Edit Kategori"
                                                data-toggle="modal" data-target="#editCategoryModal-{{ $cat->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs" title="Hapus Kategori"
                                                onclick="if(confirm('Hapus kategori ini?')) document.getElementById('delete-cat-{{ $cat->id }}').submit();">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-tags fa-2x mb-2 d-block opacity-50"></i>
                                            Belum ada data kategori berita.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </form>

                    @foreach($categories as $cat)
                        <form id="delete-cat-{{ $cat->id }}" method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="d-none">
                            @csrf @method('DELETE')
                        </form>

                        <!-- Modal Edit Kategori -->
                        <div class="modal fade" id="editCategoryModal-{{ $cat->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title font-weight-bold text-dark">
                                            <i class="fas fa-edit text-primary mr-2"></i>Edit Kategori Berita
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.categories.update', $cat) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="form-group mb-2">
                                                <label class="font-weight-bold small text-dark">Nama Kategori <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
                                            </div>
                                            <small class="text-muted d-block">
                                                Slug URL saat ini: <code>{{ $cat->slug }}</code> (akan diperbarui otomatis mengikuti nama baru).
                                            </small>
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
                </div>
                @if ($categories->hasPages())
                    <div class="card-footer bg-white d-flex justify-content-end">
                        {{ $categories->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
