@extends('admin.layouts.app')

@section('page_title', 'Detail Pesan')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-envelope-open-text text-primary mr-2"></i>Detail Pesan Pengunjung
            </h4>
            <p class="text-muted small mb-0">
                Pesan dikirim oleh <strong>{{ $contact->name }}</strong> pada {{ $contact->created_at->format('d F Y, H:i WIB') }}.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Pesan Masuk
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card shadow-sm mb-0">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-comment-dots text-primary mr-2"></i>Isi Pesan
                    </h3>
                </div>
                <div class="card-body">
                    <div class="p-4 rounded border bg-light" style="white-space: pre-wrap; line-height: 1.7; color: #1e293b;">{{ $contact->message }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-0">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-user text-primary mr-2"></i>Informasi Pengirim
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted font-weight-bold" style="width: 110px;">Nama</td>
                            <td class="font-weight-bold text-dark">{{ $contact->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Email</td>
                            <td><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal</td>
                            <td>{{ $contact->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Status</td>
                            <td>
                                @if($contact->is_read)
                                    <span class="badge badge-success px-2 py-1">Sudah Dibaca</span>
                                @else
                                    <span class="badge badge-primary px-2 py-1">Belum Dibaca</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <a href="mailto:{{ $contact->email }}?subject=Balasan: SMP Tunas Harapan" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                        <i class="fas fa-reply mr-1"></i> Balas via Email
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection