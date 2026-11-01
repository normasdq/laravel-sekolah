@extends('admin.layouts.app')

@section('page_title', 'Pesan Masuk')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-envelope text-primary mr-2"></i>Pesan Masuk Pengunjung
                @if ($unreadCount > 0)
                    <span class="badge badge-warning ml-1">{{ $unreadCount }} Baru</span>
                @endif
            </h4>
            <p class="text-muted small mb-0">
                Daftar pesan dan pertanyaan yang dikirimkan oleh pengunjung melalui formulir kontak website.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <button type="submit" form="bulk-delete-form" id="bulk-delete-btn" class="btn btn-danger btn-sm shadow-sm font-weight-bold"
                style="display:none;" onclick="return confirm('Yakin hapus pesan yang dipilih?')">
                <i class="fas fa-trash mr-1"></i> Hapus Terpilih
            </button>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('admin.contacts.mark-all-read') }}" class="d-inline"
                    onsubmit="return confirm('Tandai semua pesan sebagai sudah dibaca?')">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                        <i class="fas fa-check-double mr-1"></i> Tandai Semua Dibaca
                    </button>
                </form>
            @endif
            @if (request('filter'))
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary btn-sm font-weight-bold">
                    <i class="fas fa-times mr-1"></i> Hapus Filter
                </a>
            @endif
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-inbox mr-2 text-primary"></i>Daftar Pesan Masuk
            </h3>
            <span class="badge badge-light border px-2 py-1">
                Total: <strong>{{ $contacts->total() }}</strong> Pesan
            </span>
        </div>
        <div class="card-body table-responsive p-0">
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.contacts.bulk-delete') }}">
                @csrf
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 44px" class="text-center"><input type="checkbox" id="select-all"></th>
                            <th style="width: 55px" class="text-center">No</th>
                            <th style="width: 200px;">Pengirim</th>
                            <th style="width: 220px;">Email</th>
                            <th>Ringkasan Pesan</th>
                            <th style="width: 120px" class="text-center">Tanggal</th>
                            <th style="width: 110px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                            <tr class="{{ !$contact->is_read ? 'bg-light' : '' }}">
                                <td class="text-center">
                                    <input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $contact->id }}">
                                </td>
                                <td class="text-center text-muted font-weight-bold">
                                    {{ ($contacts->currentPage() - 1) * $contacts->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark">{{ $contact->name }}</span>
                                    @if (!$contact->is_read)
                                        <span class="badge badge-primary ml-1">Baru</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $contact->email }}</small></td>
                                <td>{{ Str::limit($contact->message, 75) }}</td>
                                <td class="text-center text-muted small">
                                    {{ $contact->created_at->format('d M Y') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.contacts.show', $contact) }}"
                                        class="btn btn-info btn-xs" title="Baca Pesan">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus"
                                        onclick="if(confirm('Hapus pesan ini?')) document.getElementById('delete-contact-{{ $contact->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-envelope-open fa-2x mb-2 d-block opacity-50"></i>
                                    Belum ada pesan masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>

            @foreach($contacts as $contact)
                <form id="delete-contact-{{ $contact->id }}" method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" class="d-none">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        </div>
        @if ($contacts->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">
                {{ $contacts->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
