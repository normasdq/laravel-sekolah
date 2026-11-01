@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')
@section('hide_header', true)

@section('content')

    <!-- Welcome Hero Banner -->
    <div class="card admin-hero-banner border-0 mb-4 overflow-hidden position-relative">
        <!-- Subtle Ambient Background Light -->
        <div class="position-absolute" style="top: -60px; right: -60px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(37, 99, 235, 0.45) 0%, transparent 70%); filter: blur(30px); pointer-events: none;"></div>
        <div class="position-absolute" style="bottom: -60px; left: 30%; width: 180px; height: 180px; background: radial-gradient(circle, rgba(6, 182, 212, 0.35) 0%, transparent 70%); filter: blur(30px); pointer-events: none;"></div>

        <div class="card-body p-4 p-md-5 position-relative z-index-1">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    @php
                        $hour = date('H');
                        $greeting = match(true) {
                            $hour < 11 => 'Selamat Pagi',
                            $hour < 15 => 'Selamat Siang',
                            $hour < 18 => 'Selamat Sore',
                            default => 'Selamat Malam',
                        };
                    @endphp
                    <span class="badge mb-3 font-weight-bold" style="background: rgba(37, 99, 235, 0.4) !important; color: #bfdbfe !important; border: 1px solid rgba(147, 197, 253, 0.4) !important; padding: 6px 14px; font-size: 11px;">
                        <i class="fa-solid fa-sparkles mr-1 text-primary"></i> Workspace Administrator &bull; {{ $greeting }}
                    </span>
                    <h2 class="font-display font-weight-bold mb-2" style="font-size: 1.85rem; letter-spacing: -0.02em; color: #ffffff !important;">
                        {{ $greeting }}, {{ auth()->user()->name }}! 👋
                    </h2>
                    <p class="mb-4 mb-lg-0" style="color: #cbd5e1 !important; font-size: 14.5px; max-width: 620px; line-height: 1.6;">
                        Selamat datang kembali di panel kendali SMP Tunas Harapan. Pantau pendaftaran siswa baru, publikasikan artikel berita, dan kelola operasional sekolah dalam satu sistem terpadu.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-right">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        @if (in_array(auth()->user()->role, ['admin', 'penulis']))
                            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm px-3 py-2 shadow-sm font-weight-bold mr-2 mb-2">
                                <i class="fa-solid fa-plus mr-1"></i> Tulis Berita
                            </a>
                        @endif
                        @if (in_array(auth()->user()->role, ['admin', 'spmb']))
                            <a href="{{ route('admin.registrations.index') }}" class="btn btn-sm px-3 py-2 font-weight-bold mb-2" style="background: rgba(255, 255, 255, 0.18) !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; color: #ffffff !important;">
                                <i class="fa-solid fa-user-graduate mr-1 text-info"></i> Data Pendaftar
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bento KPI Stat Widgets -->
    <div class="row">
        <!-- Card 1: Berita -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid #e2e8f0 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="rounded-xl d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">
                            <i class="fa-solid fa-newspaper text-lg"></i>
                        </div>
                        <span class="badge badge-info" style="font-size: 10px;">
                            {{ $stats['published_posts'] }} Terbit
                        </span>
                    </div>
                    <h3 class="font-display font-weight-extrabold text-dark mb-1" style="font-size: 1.9rem;">
                        {{ $stats['total_posts'] }}
                    </h3>
                    <p class="text-muted font-weight-medium mb-3" style="font-size: 13px;">Total Artikel Berita</p>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <span class="text-xs text-muted">{{ $stats['total_posts'] - $stats['published_posts'] }} Draft Tersimpan</span>
                        <a href="{{ route('admin.posts.index') }}" class="text-xs font-weight-bold text-primary" style="text-decoration: none;">
                            Kelola <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: SPMB -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid #e2e8f0 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="rounded-xl d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);">
                            <i class="fa-solid fa-user-graduate text-lg"></i>
                        </div>
                        <span class="badge badge-success" style="font-size: 10px;">
                            {{ $stats['verified_registrations'] }} Terverifikasi
                        </span>
                    </div>
                    <h3 class="font-display font-weight-extrabold text-dark mb-1" style="font-size: 1.9rem;">
                        {{ $stats['total_registrations'] }}
                    </h3>
                    <p class="text-muted font-weight-medium mb-3" style="font-size: 13px;">Pendaftar SPMB 2026</p>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <span class="text-xs text-warning font-weight-semibold">{{ $stats['pending_registrations'] }} Menunggu Verifikasi</span>
                        <a href="{{ route('admin.registrations.index') }}" class="text-xs font-weight-bold text-primary" style="text-decoration: none;">
                            Lihat <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Guru & Staff -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid #e2e8f0 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="rounded-xl d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                            <i class="fa-solid fa-chalkboard-user text-lg"></i>
                        </div>
                        <span class="badge badge-secondary" style="font-size: 10px;">
                            Aktif
                        </span>
                    </div>
                    <h3 class="font-display font-weight-extrabold text-dark mb-1" style="font-size: 1.9rem;">
                        {{ $stats['total_teachers'] }}
                    </h3>
                    <p class="text-muted font-weight-medium mb-3" style="font-size: 13px;">Dewan Guru &amp; Tenaga Didik</p>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <span class="text-xs text-muted">{{ $stats['total_staff'] }} Staf Administrasi</span>
                        <a href="{{ route('admin.teachers.index') }}" class="text-xs font-weight-bold text-primary" style="text-decoration: none;">
                            Direktori <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Pesan Baru -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 18px; border: 1px solid #e2e8f0 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="rounded-xl d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); box-shadow: 0 4px 12px rgba(244, 63, 94, 0.3);">
                            <i class="fa-solid fa-envelope-open-text text-lg"></i>
                        </div>
                        @if ($stats['unread_messages'] > 0)
                            <span class="badge badge-danger" style="font-size: 10px;">
                                {{ $stats['unread_messages'] }} Belum Dibaca
                            </span>
                        @else
                            <span class="badge badge-success" style="font-size: 10px;">
                                Bersih
                            </span>
                        @endif
                    </div>
                    <h3 class="font-display font-weight-extrabold text-dark mb-1" style="font-size: 1.9rem;">
                        {{ $stats['unread_messages'] }}
                    </h3>
                    <p class="text-muted font-weight-medium mb-3" style="font-size: 13px;">Pesan Masuk Pengunjung</p>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <span class="text-xs text-muted">Dari Formulir Kontak</span>
                        <a href="{{ route('admin.contacts.index') }}" class="text-xs font-weight-bold text-primary" style="text-decoration: none;">
                            Buka <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & Funnel Overview (2 Cols) -->
    <div class="row">
        <!-- SPMB Status Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fa-solid fa-chart-pie mr-2 text-primary"></i>Distribusi Status Pendaftar SPMB
                    </h3>
                    <a href="{{ route('admin.registrations.index') }}" class="text-xs font-weight-semibold text-muted">Selengkapnya &rarr;</a>
                </div>
                <div class="card-body">
                    @php
                        $totalReg = max($stats['total_registrations'], 1);
                        $pctVerified = round(($stats['verified_registrations'] / $totalReg) * 100);
                        $pctPending = round(($stats['pending_registrations'] / $totalReg) * 100);
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1.5 font-weight-semibold text-sm">
                            <span class="text-success"><i class="fa-solid fa-circle-check mr-1.5"></i>Terverifikasi</span>
                            <span class="text-dark font-weight-bold">{{ $stats['verified_registrations'] }} Siswa ({{ $pctVerified }}%)</span>
                        </div>
                        <div class="progress" style="height: 9px; border-radius: 999px; background-color: #f1f5f9;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pctVerified }}%; border-radius: 999px;" aria-valuenow="{{ $pctVerified }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1.5 font-weight-semibold text-sm">
                            <span class="text-warning"><i class="fa-solid fa-clock mr-1.5"></i>Menunggu Verifikasi</span>
                            <span class="text-dark font-weight-bold">{{ $stats['pending_registrations'] }} Siswa ({{ $pctPending }}%)</span>
                        </div>
                        <div class="progress" style="height: 9px; border-radius: 999px; background-color: #f1f5f9;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pctPending }}%; border-radius: 999px;" aria-valuenow="{{ $pctPending }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl d-flex justify-content-around text-center" style="background-color: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px;">
                        <div>
                            <span class="text-xs text-muted d-block uppercase font-weight-bold tracking-wider">Total Terdaftar</span>
                            <span class="text-xl font-display font-weight-bold text-dark">{{ $stats['total_registrations'] }}</span>
                        </div>
                        <div class="border-right border-left px-4">
                            <span class="text-xs text-muted d-block uppercase font-weight-bold tracking-wider">Berkas Lengkap</span>
                            <span class="text-xl font-display font-weight-bold text-success">{{ $stats['verified_registrations'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-muted d-block uppercase font-weight-bold tracking-wider">Pending Audit</span>
                            <span class="text-xl font-display font-weight-bold text-warning">{{ $stats['pending_registrations'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content & System Highlights -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fa-solid fa-shapes mr-2 text-info"></i>Ringkasan Media & Fasilitas
                    </h3>
                    <a href="{{ route('admin.galleries.index') }}" class="text-xs font-weight-semibold text-muted">Selengkapnya &rarr;</a>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <div class="p-3 rounded-xl" style="background-color: #eff6ff; border: 1px solid #dbeafe; border-radius: 14px;">
                                <i class="fa-solid fa-newspaper text-primary text-xl mb-1"></i>
                                <span class="d-block font-display font-weight-extrabold text-xl text-primary">{{ $stats['total_posts'] }}</span>
                                <span class="text-xs text-muted font-weight-semibold">Artikel Berita</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-xl" style="background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 14px;">
                                <i class="fa-solid fa-images text-success text-xl mb-1"></i>
                                <span class="d-block font-display font-weight-extrabold text-xl text-success">{{ $stats['total_galleries'] }}</span>
                                <span class="text-xs text-muted font-weight-semibold">Foto Galeri</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-xl" style="background-color: #fef3c7; border: 1px solid #fde68a; border-radius: 14px;">
                                <i class="fa-solid fa-chalkboard-user text-warning text-xl mb-1"></i>
                                <span class="d-block font-display font-weight-extrabold text-xl text-warning">{{ $stats['total_teachers'] }}</span>
                                <span class="text-xs text-muted font-weight-semibold">Tenaga Pengajar</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-radius: 12px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px; background: #0f172a;">
                                <i class="fa-solid fa-server text-sm"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0 text-sm">Sistem Informasi Aktif</h6>
                                <p class="text-xs text-muted mb-0">Database dan antrian notifikasi berjalan normal.</p>
                            </div>
                        </div>
                        <span class="badge badge-success px-2.5 py-1">Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Tables (2 Cols) -->
    <div class="row">
        <!-- Recent Registrations -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fa-solid fa-user-plus mr-2 text-primary"></i>Pendaftar SPMB Terbaru
                    </h3>
                    <a href="{{ route('admin.registrations.index') }}" class="text-xs font-weight-semibold text-primary">Lihat Semua &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentRegistrations as $reg)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center font-weight-bold text-primary mr-2.5 text-xs shadow-sm"
                                                    style="width: 32px; height: 32px; border: 1px solid #e2e8f0;">
                                                    {{ strtoupper(substr($reg->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="font-weight-bold text-dark d-block text-sm">{{ Str::limit($reg->name, 22) }}</span>
                                                    <small class="text-muted text-xs">{{ $reg->school_origin ?? 'Asal Sekolah' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code class="px-2 py-1 rounded bg-light border text-xs">{{ $reg->nisn }}</code>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($reg->status) {
                                                    'lulus' => 'badge-success',
                                                    'verified' => 'badge-info',
                                                    'tidak_lulus' => 'badge-danger',
                                                    'rejected' => 'badge-danger',
                                                    default => 'badge-warning',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} badge-status">
                                                {{ $reg->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Belum ada pendaftar terbaru</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Contacts -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fa-solid fa-inbox mr-2 text-danger"></i>Pesan Pengunjung Terbaru
                    </h3>
                    <a href="{{ route('admin.contacts.index') }}" class="text-xs font-weight-semibold text-primary">Lihat Semua &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Pengirim</th>
                                    <th>Pesan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentContacts as $msg)
                                    <tr class="{{ !$msg->is_read ? 'bg-light font-weight-bold' : '' }}">
                                        <td>
                                            <span class="text-dark d-block text-sm font-weight-semibold">{{ Str::limit($msg->name, 18) }}</span>
                                            <small class="text-muted text-xs">{{ $msg->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <span class="text-muted text-sm d-inline-block text-truncate" style="max-width: 220px;">
                                                {{ $msg->message }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (!$msg->is_read)
                                                <span class="badge badge-danger font-weight-bold" style="font-size: 10px;">Baru</span>
                                            @else
                                                <span class="badge badge-secondary" style="font-size: 10px;">Dibaca</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Belum ada pesan kontak masuk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
