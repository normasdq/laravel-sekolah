@extends('admin.layouts.app')

@section('title', 'Moderasi Komentar')
@section('page_title', 'Moderasi Komentar')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-comments text-primary mr-2"></i>Moderasi Komentar Berita
            </h4>
            <p class="text-muted small mb-0">
                Tinjau, setujui, atau hapus komentar pembaca pada artikel berita sekolah.
            </p>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.comments.index') }}" class="row align-items-center" style="row-gap: 8px;">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari nama pengirim atau isi komentar...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Cari</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Status Komentar --</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    </select>
                </div>
                @if (request('search') || request('status'))
                    <div class="col-md-3">
                        <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo mr-1"></i> Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabel Komentar -->
    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-list-ul text-primary mr-2"></i>Daftar Komentar Masuk
            </h3>
            <span class="badge badge-light border px-2 py-1">
                Total: <strong>{{ $comments->total() }}</strong> Komentar
            </span>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 55px;" class="text-center">No</th>
                        <th style="min-width: 190px;">Pengirim</th>
                        <th>Isi Komentar</th>
                        <th style="width: 230px;">Artikel Berita</th>
                        <th style="width: 120px;" class="text-center">Status</th>
                        <th style="width: 140px;" class="text-center">Tanggal</th>
                        <th style="width: 110px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $comment)
                        <tr class="{{ !$comment->is_approved ? 'bg-light' : '' }}">
                            <td class="text-center text-muted font-weight-bold">{{ ($comments->currentPage() - 1) * $comments->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <div class="rounded-circle bg-{{ $comment->user_id ? 'primary' : 'secondary' }} text-white d-flex align-items-center justify-content-center"
                                        style="width:34px;height:34px;font-size:13px;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($comment->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $comment->name }}</div>
                                        @if ($comment->user_id)
                                            <small class="text-info d-block"><i class="fas fa-user-check mr-1"></i>Akun Terdaftar</small>
                                        @else
                                            <small class="text-muted d-block">Pengunjung Tamu</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-wrap" style="max-width: 360px;">
                                    <p class="mb-1 text-dark">{{ $comment->body }}</p>
                                    @if ($comment->parent_id)
                                        <small class="text-muted"><i class="fas fa-reply mr-1"></i>Balasan Komentar</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if ($comment->post)
                                    <a href="{{ route('berita.detail', $comment->post->slug) }}" target="_blank"
                                        class="small font-weight-semibold text-primary">
                                        {{ Str::limit($comment->post->title, 45) }}
                                    </a>
                                @else
                                    <small class="text-muted font-italic">(Artikel dihapus)</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($comment->is_approved)
                                    <span class="badge badge-success px-2 py-1">Disetujui</span>
                                @else
                                    <span class="badge badge-warning px-2 py-1">Menunggu</span>
                                @endif
                            </td>
                            <td class="text-center"><small class="text-muted">{{ $comment->created_at->format('d M Y, H:i') }}</small></td>
                            <td class="text-center">
                                @if (!$comment->is_approved)
                                    <form method="POST" action="{{ route('admin.comments.approve', $comment) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-xs" title="Setujui Komentar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}"
                                    class="d-inline" onsubmit="return confirm('Yakin hapus komentar ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="Hapus Komentar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-comment-slash fa-2x mb-2 d-block opacity-50"></i>
                                Belum ada komentar yang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($comments->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">
                {{ $comments->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
