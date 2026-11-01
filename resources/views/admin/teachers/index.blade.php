@extends('admin.layouts.app')

@section('page_title', 'Kelola Guru & Staff')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-chalkboard-teacher text-primary mr-2"></i>Dewan Guru & Staf
            </h4>
            <p class="text-muted small mb-0">
                Kelola direktori tenaga pendidik dan staf kependidikan yang ditampilkan pada profil sekolah.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <button type="submit" form="bulk-delete-form" id="bulk-delete-btn" class="btn btn-danger btn-sm shadow-sm font-weight-bold"
                style="display:none;" onclick="return confirm('Yakin hapus data yang dipilih?')">
                <i class="fas fa-trash mr-1"></i> Hapus Terpilih
            </button>
            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-plus mr-1"></i> Tambah Guru / Staf
            </a>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.teachers.index') }}" class="row align-items-center" style="row-gap: 8px;">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari nama guru atau jabatan...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Tipe --</option>
                        <option value="guru" {{ request('type') === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="staff" {{ request('type') === 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>
                @if (request('search') || request('type'))
                    <div class="col-md-3">
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo mr-1"></i> Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-list-ul mr-2 text-primary"></i>Daftar Guru & Staf
            </h3>
            <span class="badge badge-light border px-2 py-1">
                Total: <strong>{{ $teachers->total() }}</strong> Data
            </span>
        </div>
        <div class="card-body table-responsive p-0">
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.teachers.bulk-delete') }}">
                @csrf
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 44px" class="text-center"><input type="checkbox" id="select-all"></th>
                            <th style="width: 80px" class="text-center">Foto</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan / Mata Pelajaran</th>
                            <th style="width: 110px" class="text-center">Tipe</th>
                            <th style="width: 100px" class="text-center">Urutan</th>
                            <th style="width: 120px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $teacher->id }}">
                                </td>
                                <td class="text-center">
                                    <img src="{{ $teacher->photo ? Storage::url($teacher->photo) : 'https://via.placeholder.com/50x50?text=' . urlencode(substr($teacher->name, 0, 1)) }}"
                                        class="img-thumb" alt="{{ $teacher->name }}">
                                </td>
                                <td class="font-weight-bold text-dark">{{ $teacher->name }}</td>
                                <td>{{ $teacher->position ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-status {{ $teacher->type === 'guru' ? 'badge-success' : 'badge-info' }}">
                                        {{ ucfirst($teacher->type) }}
                                    </span>
                                </td>
                                <td class="text-center font-weight-bold">{{ $teacher->order }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}"
                                        class="btn btn-info btn-xs" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus"
                                        onclick="if(confirm('Yakin hapus data ini?')) document.getElementById('delete-teacher-{{ $teacher->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-chalkboard-teacher fa-2x mb-2 d-block opacity-50"></i>
                                    Tidak ada data guru atau staf.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>

            @foreach($teachers as $teacher)
                <form id="delete-teacher-{{ $teacher->id }}" method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" class="d-none">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        </div>
        @if ($teachers->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">
                {{ $teachers->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
