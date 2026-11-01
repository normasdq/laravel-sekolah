@extends('admin.layouts.app')

@section('title', 'Hasil Ujian Seleksi CBT')
@section('page_title', 'Hasil Ujian Seleksi CBT')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-chart-line text-primary mr-2"></i>Hasil Ujian Seleksi Siswa (CBT)
            </h4>
            <p class="text-muted small mb-0">
                Rekapitulasi nilai ujian komputer calon siswa baru yang terintegrasi otomatis ke kriteria C1 pada perhitungan SPK SAW.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <a href="{{ route('admin.spk.scores') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-scale-balanced mr-1"></i> Buka Input Nilai SPK SAW
            </a>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.examp.results') }}" class="row align-items-center">
                <div class="col-md-5 my-1">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control"
                            placeholder="Cari nama calon siswa, no. pendaftaran, atau email..."
                            value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
                @if (request('search'))
                    <div class="col-md-3 my-1">
                        <a href="{{ route('admin.examp.results') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo mr-1"></i> Reset
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
                <i class="fas fa-list-ol text-primary mr-2"></i>Daftar Nilai Peserta Ujian CBT
            </h3>
            <span class="badge badge-light border px-2 py-1">
                Total: <strong>{{ $results->total() }}</strong> Peserta
            </span>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px" class="text-center">No</th>
                        <th style="width: 180px;">No. Pendaftaran</th>
                        <th>Nama Peserta</th>
                        <th class="text-center" style="width: 160px;">Skor Ujian (0 - 100)</th>
                        <th class="text-center" style="width: 150px;">Waktu Mulai</th>
                        <th class="text-center" style="width: 150px;">Waktu Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $res)
                        @php
                            $reg = $res->registration ?? $res->user?->registration;
                            $studentName = $reg->name ?? $res->user->name ?? '-';
                            $regNumber = $reg->registration_number ?? ($registration[$res->user_id] ?? '-');
                        @endphp
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $results->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <code class="px-2 py-1 rounded bg-light border font-weight-bold text-primary">
                                    {{ $regNumber }}
                                </code>
                            </td>
                            <td>
                                <span class="font-weight-bold text-dark d-block">{{ $studentName }}</span>
                                @if ($reg && $res->user && $res->user->name && $res->user->name !== $studentName)
                                    <small class="text-muted font-weight-normal d-block">
                                        <i class="fas fa-user-circle mr-1"></i>Akun: {{ $res->user->name }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary px-3 py-1 font-weight-bold" style="font-size: 0.9rem;">
                                    {{ $res->score !== null ? (int) round($res->score) : '-' }} / 100
                                </span>
                            </td>
                            <td class="text-center text-muted small">
                                {{ $res->start_time ? $res->start_time->format('H:i:s') : '-' }}
                            </td>
                            <td class="text-center text-muted small">
                                @if ($res->end_time)
                                    {{ $res->end_time->format('H:i:s') }}
                                @else
                                    <span class="badge badge-warning">Sedang Mengerjakan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-check fa-2x mb-2 d-block opacity-50"></i>
                                Belum ada peserta yang menyelesaikan ujian CBT.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($results->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Menampilkan {{ $results->firstItem() }} - {{ $results->lastItem() }} dari {{ $results->total() }} peserta
                </div>
                <div>
                    {{ $results->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
