@extends('admin.layouts.app')

@section('title', 'Program Unggulan')
@section('page_title', 'Program Unggulan')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-award text-primary mr-2"></i>Program Unggulan Sekolah
            </h4>
            <p class="text-muted small mb-0">
                Kelola daftar program akademik, karakter, dan ekstrakurikuler unggulan yang ditampilkan di halaman depan.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <button type="submit" form="bulk-delete-form" id="bulk-delete-btn" class="btn btn-danger btn-sm shadow-sm font-weight-bold"
                style="display:none;" onclick="return confirm('Yakin hapus program yang dipilih?')">
                <i class="fas fa-trash mr-1"></i> Hapus Terpilih
            </button>
            <a href="{{ route('admin.programs.create') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-plus mr-1"></i> Tambah Program Baru
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-list-ul text-primary mr-2"></i>Daftar Program Unggulan
            </h3>
            <span class="badge badge-light border px-2 py-1">
                Total: <strong>{{ $programs->total() }}</strong> Program
            </span>
        </div>
        <div class="card-body table-responsive p-0">
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.programs.bulk-delete') }}">
                @csrf
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 44px;" class="text-center"><input type="checkbox" id="select-all"></th>
                            <th style="width: 70px;" class="text-center">Ikon</th>
                            <th style="min-width: 200px;">Judul Program</th>
                            <th>Deskripsi Singkat</th>
                            <th style="width: 110px;" class="text-center">Status</th>
                            <th style="width: 90px;" class="text-center">Urutan</th>
                            <th style="width: 110px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $program)
                            <tr>
                                <td class="text-center"><input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $program->id }}"></td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-lg" style="width: 38px; height: 38px; background: #fef3c7; color: #d97706; border-radius: 10px;">
                                        <i class="{{ $program->icon ?? 'fas fa-star' }}"></i>
                                    </div>
                                </td>
                                <td class="font-weight-bold text-dark">{{ $program->title }}</td>
                                <td class="text-muted">{{ Str::limit($program->description, 90) }}</td>
                                <td class="text-center">
                                    @if($program->is_active)
                                        <span class="badge badge-success px-2 py-1">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center font-weight-bold text-muted">#{{ $program->order }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-info btn-xs" title="Edit Program">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus Program"
                                        onclick="if(confirm('Yakin hapus program ini?')) document.getElementById('delete-program-{{ $program->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-award fa-2x mb-2 d-block opacity-50"></i>
                                    Belum ada data program unggulan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>

            @foreach($programs as $program)
                <form id="delete-program-{{ $program->id }}" method="POST" action="{{ route('admin.programs.destroy', $program) }}" class="d-none">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        </div>
        @if ($programs->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">
                {{ $programs->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection