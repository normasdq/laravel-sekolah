@extends('layouts.app')

@section('title', 'Dashboard Pendaftar - SMP Tunas Harapan')

@section('content')
    <div class="min-h-screen bg-white pt-6 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- ==================== GREETING BANNER ==================== -->
            <div class="relative rounded-3xl p-6 sm:p-10 mb-8 bg-gradient-to-r from-dark-950 via-primary-950 to-dark-900 border border-white/10 shadow-2xl text-white overflow-hidden fade-up">
                <!-- Ambient Glow inside banner -->
                <div class="absolute -top-20 -right-20 w-80 h-80 ambient-glow-cyan opacity-40 blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4 sm:gap-5">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-primary-600 to-cyan-400 p-0.5 shadow-xl flex-shrink-0">
                            <div class="w-full h-full bg-dark-900 rounded-[14px] flex items-center justify-center text-primary-300 font-bold text-xl sm:text-2xl">
                                {{ strtoupper(substr($registration->name ?? $user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-white/10 text-primary-300 border border-white/15 mb-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Akun Pendaftar
                            </span>
                            <h1 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight">
                                Halo, {{ $registration->name ?? $user->name }}! 👋
                            </h1>
                            <p class="text-xs sm:text-sm text-dark-300 mt-1">
                                Pantau kelengkapan berkas, jadwal ujian CBT, dan status kelulusan Anda di sini.
                            </p>
                        </div>
                    </div>

                    <!-- Registration Number Badge -->
                    @if ($registration)
                        <div class="px-5 py-3 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md self-start md:self-auto flex items-center gap-3.5 shadow-inner">
                            <div class="w-10 h-10 rounded-xl bg-primary-500/20 text-primary-400 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-id-card-clip"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-dark-400 font-bold uppercase tracking-wider">No. Pendaftaran</p>
                                <p class="font-display font-extrabold text-sm sm:text-base text-white tracking-wide">
                                    {{ $registration->registration_number }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ==================== MAIN DASHBOARD GRID ==================== -->
            <div class="grid lg:grid-cols-12 gap-8 items-start">

                <!-- Left Column (8 Cols): Status, Payment, Quick Actions -->
                <div class="lg:col-span-8 space-y-7">

                    <!-- Status Pendaftaran Card -->
                    <div class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/90 shadow-sm transition-all hover:shadow-md">
                        <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-100">
                            <h2 class="font-display font-bold text-lg text-dark-900 flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                </div>
                                Status Pendaftaran
                            </h2>

                            @if ($registration)
                                @php
                                    $statusPill = match ($registration->status) {
                                        'verified' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'lulus' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'tidak_lulus' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                                    };
                                @endphp
                                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border {{ $statusPill }}">
                                    {{ $registration->status_label }}
                                </span>
                            @endif
                        </div>

                        @if ($registration)
                            <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-gray-50 to-primary-50/30 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <p class="text-xs font-bold text-dark-400 uppercase tracking-wider">Identitas Calon Siswa</p>
                                    <p class="font-display font-extrabold text-lg text-dark-900">{{ $registration->name }}</p>
                                    <p class="text-xs text-dark-500">NISN: <span class="font-semibold text-dark-800">{{ $registration->nisn }}</span> • Asal: <span class="font-semibold text-dark-800">{{ $registration->school_origin }}</span></p>
                                </div>
                                
                                <div class="flex items-center gap-2.5">
                                    <a href="{{ route('pendaftar.download') }}" target="_blank"
                                        class="px-4 py-2.5 bg-white text-dark-700 hover:text-primary-600 font-bold text-xs rounded-xl border border-gray-200 hover:border-primary-300 transition-all flex items-center gap-2 shadow-sm">
                                        <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                        <span>Unduh Bukti</span>
                                    </a>
                                    <a href="{{ route('pendaftar.profile') }}"
                                        class="px-4 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold text-xs rounded-xl shadow-md shadow-primary-500/20 transition-all flex items-center gap-1.5">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>Detail</span>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 px-4 rounded-2xl bg-slate-50/70 border border-dashed border-slate-200">
                                <div class="w-14 h-14 bg-primary-50 text-primary-500 rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl">
                                    <i class="fa-solid fa-file-circle-plus"></i>
                                </div>
                                <h3 class="font-display font-bold text-base text-dark-900">Formulir Pendaftaran Belum Diisi</h3>
                                <p class="text-dark-500 text-xs sm:text-sm max-w-md mx-auto mt-1 mb-5 leading-relaxed">
                                    Silakan melengkapi data pribadi, dokumen Kartu Keluarga, Ijazah/SKL, dan Akte Kelahiran untuk melanjutkan.
                                </p>
                                <a href="{{ route('spmb') }}"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-blue-500 text-white font-bold text-xs sm:text-sm rounded-xl hover:shadow-lg hover:shadow-primary-500/30 transition-all">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Isi Formulir SPMB Sekarang</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Hasil Nilai Akhir Seleksi SPK SAW (Tampil Jika Nilai Lengkap) -->
                    @if ($registration && !empty($sawResult) && ($sawResult['is_complete'] ?? false))
                        <div class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/90 shadow-sm transition-all hover:shadow-md">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 mb-5 border-b border-gray-100">
                                <div>
                                    <span class="text-xs font-extrabold uppercase tracking-wider {{ $sawResult['is_passed'] ? 'text-emerald-700' : 'text-rose-700' }}">
                                        <i class="fa-solid fa-scale-balanced mr-1"></i> Hasil Evaluasi SPK Metode SAW
                                    </span>
                                    <h2 class="font-display font-bold text-lg text-dark-900 mt-0.5">
                                        Hasil Nilai Akhir Seleksi &bull; Peringkat #{{ $sawResult['rank'] }}
                                        <span class="text-xs font-medium text-dark-500">dari {{ $sawResult['total_ranked'] }} peserta</span>
                                    </h2>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-xs text-dark-500 block">Hasil Nilai Akhir (V)</span>
                                    <span class="font-display font-black text-2xl {{ $sawResult['is_passed'] ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ (int) round($sawResult['preference_score'] * 100) }} / 100
                                    </span>
                                    <span class="text-[11px] text-dark-500 block">
                                        Ambang Batas Lulus: <strong>{{ (int) round($sawResult['threshold'] * 100) }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach ($sawResult['criteria'] as $c)
                                    <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200/80 text-center">
                                        <span class="text-[10px] font-bold uppercase text-dark-400 block">{{ $c->code }} — {{ $c->name }}</span>
                                        <span class="font-display font-extrabold text-base text-dark-900 block mt-1">
                                            {{ (int) round($sawResult['raw_scores'][$c->id] ?? 0) }}
                                        </span>
                                        <span class="text-[10px] text-dark-500">
                                            Poin Bobot: {{ (int) round(($sawResult['weighted_scores'][$c->id] ?? 0) * 100) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Pembayaran Daftar Ulang (Tampil Jika Lulus) -->
                    @if ($registration && $registration->status === 'lulus' && $registration->payment_amount > 0)
                        <div class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/90 shadow-sm transition-all hover:shadow-md">
                            <h2 class="font-display font-bold text-lg text-dark-900 mb-6 flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                Pembayaran Daftar Ulang Siswa Baru
                            </h2>

                            <div class="p-6 rounded-2xl border flex flex-col sm:flex-row sm:items-center justify-between gap-5 {{ $registration->payment_status === 'paid' ? 'bg-emerald-50/60 border-emerald-200' : 'bg-amber-50/60 border-amber-200' }}">
                                <div>
                                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider mb-2 {{ $registration->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $registration->payment_status_label }}
                                    </span>
                                    <p class="font-display font-black text-2xl text-dark-900">
                                        {{ $registration->payment_amount_formatted }}
                                    </p>
                                    @if ($registration->paid_at)
                                        <p class="text-xs text-emerald-700 mt-1 font-semibold flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-check"></i>
                                            Terverifikasi pada {{ $registration->paid_at->format('d M Y, H:i') }} WIB
                                        </p>
                                    @else
                                        <p class="text-xs text-amber-700 mt-1">
                                            Selesaikan pembayaran sebelum batas waktu daftar ulang ditutup.
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3">
                                    @if ($registration->payment_status !== 'paid')
                                        <a href="{{ route('payment.index') }}"
                                            class="shimmer-btn px-6 py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-primary-500/25 transition-all flex items-center gap-2">
                                            <span>Bayar Online (Snap)</span>
                                            <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('payment.invoice') }}"
                                            class="px-5 py-3 bg-white hover:bg-emerald-50 text-emerald-700 font-bold text-xs rounded-xl border border-emerald-300 transition-all flex items-center gap-2 shadow-sm">
                                            <i class="fa-solid fa-file-invoice"></i>
                                            <span>Unduh Bukti Lunas (Invoice)</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Bento Quick Action Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        <!-- Card 1: Ujian CBT Online -->
                        <a href="{{ route('pendaftar.ujian.index') }}"
                            class="group p-6 rounded-3xl bg-white border border-slate-200/90 hover:border-primary-400 hover:shadow-xl transition-all hover-lift">
                            <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-xl mb-4 group-hover:bg-primary-500 group-hover:text-white transition-all shadow-sm">
                                <i class="fa-solid fa-laptop-code"></i>
                            </div>
                            <h3 class="font-display font-bold text-base text-dark-900 group-hover:text-primary-600 transition-colors">
                                Ujian Seleksi Online (CBT)
                            </h3>
                            <p class="text-xs text-dark-500 mt-1.5 leading-relaxed">
                                Ikuti ujian seleksi 50 soal pilihan ganda secara mandiri setelah berkas Anda terverifikasi panitia.
                            </p>
                        </a>

                        <!-- Card 2: Cek Kelulusan NISN -->
                        <a href="{{ route('pendaftar.cek-status') }}"
                            class="group p-6 rounded-3xl bg-white border border-slate-200/90 hover:border-emerald-400 hover:shadow-xl transition-all hover-lift">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-sm">
                                <i class="fa-solid fa-magnifying-glass-chart"></i>
                            </div>
                            <h3 class="font-display font-bold text-base text-dark-900 group-hover:text-emerald-600 transition-colors">
                                Pengumuman Kelulusan
                            </h3>
                            <p class="text-xs text-dark-500 mt-1.5 leading-relaxed">
                                Cek status pengumuman hasil seleksi akhir SPMB menggunakan NISN akun Anda.
                            </p>
                        </a>

                        <!-- Card 3: Profil & Dokumen Pendaftar -->
                        <a href="{{ route('pendaftar.profile') }}"
                            class="group p-6 rounded-3xl bg-white border border-slate-200/90 hover:border-blue-400 hover:shadow-xl transition-all hover-lift">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-4 group-hover:bg-blue-500 group-hover:text-white transition-all shadow-sm">
                                <i class="fa-solid fa-address-card"></i>
                            </div>
                            <h3 class="font-display font-bold text-base text-dark-900 group-hover:text-blue-600 transition-colors">
                                Berkas & Dokumen
                            </h3>
                            <p class="text-xs text-dark-500 mt-1.5 leading-relaxed">
                                Periksa kelengkapan file KK, Ijazah/SKL, Akte Kelahiran, dan informasi data orang tua.
                            </p>
                        </a>

                        <!-- Card 4: Kartu Peserta Ujian -->
                        <a href="{{ route('pendaftar.kartu') }}"
                            class="group p-6 rounded-3xl bg-white border border-slate-200/90 hover:border-cyan-400 hover:shadow-xl transition-all hover-lift">
                            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl mb-4 group-hover:bg-cyan-500 group-hover:text-white transition-all shadow-sm">
                                <i class="fa-solid fa-print"></i>
                            </div>
                            <h3 class="font-display font-bold text-base text-dark-900 group-hover:text-cyan-600 transition-colors">
                                Cetak Kartu Peserta
                            </h3>
                            <p class="text-xs text-dark-500 mt-1.5 leading-relaxed">
                                Unduh dan cetak kartu peserta ujian resmi untuk tanda pengenal calon siswa.
                            </p>
                        </a>
                    </div>
                </div>

                <!-- Right Column (4 Cols): Interactive Flow Timeline & Support -->
                <div class="lg:col-span-4 space-y-7">

                    <!-- Help Box WhatsApp -->
                    <div class="rounded-3xl p-7 bg-gradient-to-br from-dark-950 via-dark-900 to-primary-950 border border-white/10 text-white relative overflow-hidden shadow-xl">
                        <div class="relative z-10">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-cyan-300 border border-white/15 mb-3">
                                Layanan Bantuan
                            </span>
                            <h3 class="font-display font-bold text-lg text-white mb-2">Perlu Bantuan Teknis?</h3>
                            <p class="text-xs text-dark-300 mb-5 leading-relaxed">
                                Jika menemukan kendala saat pengisian formulir, upload berkas, atau pengerjaan CBT, hubungi panitia melalui WhatsApp resmi.
                            </p>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->get('school_phone', '6281234567890')) }}" target="_blank"
                                class="w-full py-3 bg-[#25D366] hover:bg-[#1EBE5D] text-white font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-[#25D366]/20">
                                <i class="fab fa-whatsapp text-sm"></i>
                                <span>Hubungi Panitia SPMB</span>
                            </a>
                        </div>
                    </div>

                    <!-- Flow Stepper Calculation -->
                    @php
                        $stepConfig = [];
                        
                        // Step 1: Formulir & Berkas
                        if (!$registration) {
                            $stepConfig[1] = ['state' => 'active', 'desc' => 'Silakan isi formulir pendaftaran SPMB terlebih dahulu.', 'action' => 'Isi Formulir', 'route' => 'spmb'];
                        } elseif ($registration->status === 'rejected') {
                            $stepConfig[1] = ['state' => 'failed', 'desc' => 'Berkas ditolak panitia. Silakan hubungi CS.', 'action' => null, 'route' => null];
                        } elseif (in_array($registration->status, ['verified', 'lulus', 'tidak_lulus'])) {
                            $stepConfig[1] = ['state' => 'completed', 'desc' => 'Berkas telah diverifikasi oleh panitia.', 'action' => null, 'route' => null];
                        } else {
                            $stepConfig[1] = ['state' => 'active', 'desc' => 'Menunggu verifikasi berkas oleh panitia.', 'action' => null, 'route' => null];
                        }

                        // Step 2: Ujian Seleksi Online (CBT)
                        $examFinished = $examResult && $examResult->end_time !== null;
                        $examInProgress = $examResult && $examResult->end_time === null;

                        if (!$registration || $registration->status === 'pending' || $registration->status === 'rejected') {
                            $stepConfig[2] = ['state' => 'pending', 'desc' => 'Tersedia setelah berkas diverifikasi.', 'action' => null, 'route' => null];
                        } elseif ($registration->status === 'verified' && !$examFinished) {
                            $stepConfig[2] = [
                                'state' => 'active',
                                'desc' => $examInProgress ? 'Ujian sedang berlangsung. Segera selesaikan sebelum waktu habis.' : 'Ujian seleksi CBT sudah dapat dikerjakan.',
                                'action' => $examInProgress ? 'Lanjutkan Ujian' : 'Mulai Ujian',
                                'route' => $examInProgress ? 'pendaftar.ujian.start' : 'pendaftar.ujian.index',
                            ];
                        } else {
                            $stepConfig[2] = ['state' => 'completed', 'desc' => 'Ujian seleksi telah selesai dikerjakan.', 'action' => null, 'route' => null];
                        }

                        // Step 3: Pengumuman Kelulusan
                        if (in_array($registration->status ?? '', ['lulus', 'tidak_lulus'])) {
                            if ($registration->status === 'lulus') {
                                $stepConfig[3] = ['state' => 'completed', 'desc' => 'Selamat! Anda dinyatakan LULUS seleksi.', 'action' => 'Lihat Hasil SAW', 'route' => 'pendaftar.cek-status'];
                            } else {
                                $stepConfig[3] = ['state' => 'failed', 'desc' => 'Mohon maaf, Anda belum berhasil pada periode ini.', 'action' => 'Lihat Detail', 'route' => 'pendaftar.cek-status'];
                            }
                        } elseif (($registration->status ?? '') === 'verified' && $examFinished) {
                            $stepConfig[3] = ['state' => 'active', 'desc' => 'Menunggu rekapitulasi nilai SPK SAW dan pengumuman panitia.', 'action' => null, 'route' => null];
                        } else {
                            $stepConfig[3] = ['state' => 'pending', 'desc' => 'Belum dibuka.', 'action' => null, 'route' => null];
                        }

                        // Step 4: Pembayaran Daftar Ulang
                        if ($registration && $registration->status === 'lulus') {
                            if ($registration->payment_status === 'paid') {
                                $stepConfig[4] = ['state' => 'completed', 'desc' => 'Pembayaran lunas. Selamat bergabung di SMP Tunas Harapan!', 'action' => null, 'route' => null];
                            } else {
                                $stepConfig[4] = ['state' => 'active', 'desc' => 'Selesaikan pembayaran biaya daftar ulang.', 'action' => 'Bayar Sekarang', 'route' => 'payment.index'];
                            }
                        } elseif ($registration && $registration->status === 'tidak_lulus') {
                            $stepConfig[4] = ['state' => 'pending', 'desc' => 'Tidak tersedia.', 'action' => null, 'route' => null];
                        } else {
                            $stepConfig[4] = ['state' => 'pending', 'desc' => 'Belum tersedia.', 'action' => null, 'route' => null];
                        }
                    @endphp

                    <!-- Timeline Stepper Card -->
                    <div class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/90 shadow-sm">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                            <h3 class="font-display font-bold text-sm text-dark-900 uppercase tracking-wider">
                                Tahapan Pendaftaran
                            </h3>
                            <span class="text-[10px] font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-full border border-primary-100">
                                TA 2026/2027
                            </span>
                        </div>

                        <div class="space-y-0">
                            @foreach ([1 => 'Verifikasi Berkas', 2 => 'Ujian Seleksi Online (CBT)', 3 => 'Pengumuman Kelulusan', 4 => 'Daftar Ulang'] as $num => $label)
                                @php $s = $stepConfig[$num]; @endphp
                                <div class="flex gap-4 {{ !$loop->last ? 'pb-6' : '' }}">
                                    <!-- Circle indicator -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold
                                            {{ $s['state'] === 'completed' ? 'bg-emerald-500 text-white' : ($s['state'] === 'active' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : ($s['state'] === 'failed' ? 'bg-rose-500 text-white' : 'bg-gray-100 text-gray-400')) }}">
                                            @if ($s['state'] === 'completed')
                                                <i class="fa-solid fa-check text-xs"></i>
                                            @elseif ($s['state'] === 'failed')
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            @elseif ($s['state'] === 'active')
                                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                            @else
                                                {{ $num }}
                                            @endif
                                        </div>

                                        @if (!$loop->last)
                                            <div class="w-px flex-1 min-h-[28px] mt-1 {{ $s['state'] === 'completed' ? 'bg-emerald-300' : 'bg-gray-200' }}"></div>
                                        @endif
                                    </div>

                                    <!-- Step Text -->
                                    <div class="flex-1 min-w-0 {{ $loop->last ? 'pb-0' : '' }}">
                                        <div class="flex items-center justify-between gap-2">
                                            <h4 class="text-xs font-bold text-dark-900">{{ $label }}</h4>
                                            <span class="text-[10px] font-bold uppercase tracking-wider
                                                {{ $s['state'] === 'completed' ? 'text-emerald-600' : ($s['state'] === 'active' ? 'text-primary-600' : ($s['state'] === 'failed' ? 'text-rose-500' : 'text-gray-400')) }}">
                                                {{ $s['state'] === 'completed' ? 'Selesai' : ($s['state'] === 'active' ? 'Berlangsung' : ($s['state'] === 'failed' ? 'Gagal' : 'Menunggu')) }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-dark-500 mt-1 leading-relaxed">{{ $s['desc'] }}</p>

                                        @if (!empty($s['action']) && !empty($s['route']))
                                            <a href="{{ route($s['route']) }}"
                                                class="inline-flex items-center gap-1.5 mt-2.5 px-3.5 py-1.5 text-xs font-bold rounded-lg
                                                {{ $s['state'] === 'active' ? 'bg-primary-600 text-white hover:bg-primary-500 shadow-sm' : 'bg-gray-100 text-dark-700 hover:bg-gray-200' }} transition-all">
                                                <span>{{ $s['action'] }}</span>
                                                <i class="fa-solid fa-arrow-right-long text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
