@extends('layouts.app')

@section('title', 'Pengumuman & Cek Status SPMB - ' . $settings->get('school_name'))

@section('content')
    <section class="py-24 bg-white">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-3xl p-8 md:p-10 shadow-lg">

                {{-- 1. FORM PENCARIAN --}}
                @if (!session('registration') && !session('not_found'))
                    <div class="text-center mb-8">
                        <div class="w-24 h-24 bg-primary-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-search text-primary-500 text-4xl"></i>
                        </div>
                        <h2 class="font-display text-2xl font-bold text-dark-900 tracking-tight mb-2">
                            Pengumuman & Status Seleksi SPMB
                        </h2>
                        <p class="text-dark-500">Masukkan 10 digit NISN Anda untuk melihat status verifikasi berkas dan hasil kelulusan seleksi.</p>
                    </div>

                    <form method="POST" action="{{ route('pendaftar.cek-status.check') }}" id="cek-form">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-dark-700 mb-2">NISN (10 Digit) <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="nisn" value="{{ old('nisn', $registration->nisn ?? '') }}"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:bg-white focus:border-primary-400 focus:ring-2 focus:ring-primary-400/20 outline-none transition-all text-base text-center font-bold tracking-wider @error('nisn') border-red-300 @enderror"
                                    placeholder="Contoh: 0041234567" maxlength="10" required>
                                @error('nisn')
                                    <p class="text-red-500 text-sm mt-2 text-center">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                class="w-full py-4 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-500 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-search"></i> Lihat Hasil Seleksi Saya
                            </button>
                        </div>
                    </form>
                @endif

                {{-- 2. HASIL JIKA DATA DITEMUKAN --}}
                @if (session('registration'))
                    @php
                        $reg = $checkedRegistration ?? session('registration');
                        $status = $reg->status;

                        $theme = match ($status) {
                            'lulus' => [
                                'circle_bg' => 'bg-emerald-100',
                                'icon' => 'fa-trophy text-emerald-600',
                                'title_color' => 'text-emerald-600',
                                'title' => 'SELAMAT! ANDA DINYATAKAN LULUS SELEKSI',
                                'subtitle' => 'Selamat bergabung sebagai calon peserta didik baru ' . $settings->get('school_name') . '. Silakan lanjutkan ke tahap Daftar Ulang.',
                                'card_bg' => 'bg-emerald-50/70 border-emerald-200',
                                'badge_color' => 'text-emerald-700 bg-emerald-100',
                            ],
                            'tidak_lulus' => [
                                'circle_bg' => 'bg-rose-100',
                                'icon' => 'fa-circle-xmark text-rose-600',
                                'title_color' => 'text-rose-600',
                                'title' => 'MOHON MAAF, ANDA BELUM LULUS SELEKSI',
                                'subtitle' => 'Berdasarkan hasil perhitungan seleksi akhir (SPK SAW), nilai preferensi Anda belum mencapai ambang batas kelulusan periode ini. Tetap semangat!',
                                'card_bg' => 'bg-rose-50/70 border-rose-200',
                                'badge_color' => 'text-rose-700 bg-rose-100',
                            ],
                            'verified' => [
                                'circle_bg' => 'bg-blue-100',
                                'icon' => 'fa-user-check text-blue-600',
                                'title_color' => 'text-blue-600',
                                'title' => 'BERKAS TERVERIFIKASI — SIAP MENGIKUTI TES',
                                'subtitle' => 'Berkas pendaftaran Anda telah diverifikasi lengkap oleh panitia. Silakan ikuti Ujian Seleksi Online (CBT) sesuai jadwal.',
                                'card_bg' => 'bg-blue-50/70 border-blue-200',
                                'badge_color' => 'text-blue-700 bg-blue-100',
                            ],
                            'rejected' => [
                                'circle_bg' => 'bg-red-100',
                                'icon' => 'fa-user-times text-red-600',
                                'title_color' => 'text-red-600',
                                'title' => 'MAAF, BERKAS PENDAFTARAN DITOLAK',
                                'subtitle' => 'Mohon maaf, berkas pendaftaran Anda belum memenuhi persyaratan verifikasi administrasi.',
                                'card_bg' => 'bg-red-50/70 border-red-200',
                                'badge_color' => 'text-red-700 bg-red-100',
                            ],
                            default => [
                                'circle_bg' => 'bg-amber-100',
                                'icon' => 'fa-clock text-amber-500',
                                'title_color' => 'text-amber-600',
                                'title' => 'BERKAS SEDANG DIVERIFIKASI PANITIA',
                                'subtitle' => 'Data pendaftaran Anda sudah kami terima dan sedang dalam tahap pemeriksaan kelengkapan berkas oleh panitia SPMB.',
                                'card_bg' => 'bg-amber-50/70 border-amber-200',
                                'badge_color' => 'text-amber-700 bg-amber-100',
                            ],
                        };
                    @endphp

                    <div class="text-center mb-8">
                        <div class="w-28 h-28 rounded-full flex items-center justify-center mx-auto mb-4 {{ $theme['circle_bg'] }}">
                            <i class="fas {{ $theme['icon'] }} text-4xl"></i>
                        </div>

                        <h3 class="font-display text-2xl font-extrabold mb-2 uppercase {{ $theme['title_color'] }}">
                            {{ $theme['title'] }}
                        </h3>

                        <p class="text-dark-600 text-sm max-w-xl mx-auto leading-relaxed">
                            {{ $theme['subtitle'] }}
                        </p>
                    </div>

                    {{-- KARTU RINGKASAN SKOR SPK SAW (TAMPIL JIKA DATA SAW TERSEDIA) --}}
                    @if (in_array($status, ['verified', 'lulus', 'tidak_lulus']) && !empty($sawResult) && ($sawResult['is_complete'] ?? false))
                        <div class="border rounded-2xl p-6 mb-6 {{ $sawResult['is_passed'] ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200' }}">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-4 border-b border-black/10">
                                <div>
                                    <span class="text-xs font-extrabold uppercase tracking-wider {{ $sawResult['is_passed'] ? 'text-emerald-700' : 'text-rose-700' }}">
                                        <i class="fas fa-scale-balanced mr-1"></i> Hasil Evaluasi SPK Metode SAW
                                    </span>
                                    <h4 class="font-display font-bold text-lg text-dark-900 mt-0.5">
                                        Peringkat #{{ $sawResult['rank'] }} <span class="text-xs font-medium text-dark-500">dari {{ $sawResult['total_ranked'] }} peserta seleksi</span>
                                    </h4>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-xs text-dark-500 block">Hasil Nilai Akhir (V)</span>
                                    <span class="font-display font-black text-2xl {{ $sawResult['is_passed'] ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ (int) round($sawResult['preference_score'] * 100) }} / 100
                                    </span>
                                    <span class="text-[11px] text-dark-500 block">
                                        Ambang Batas Lulus: <strong>{{ (int) round($sawResult['threshold'] * 100) }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach ($sawResult['criteria'] as $c)
                                    <div class="bg-white/80 rounded-xl p-3 border border-black/5 text-center">
                                        <span class="text-[10px] font-bold uppercase text-dark-400 block">{{ $c->code }} — {{ $c->name }}</span>
                                        <span class="font-display font-extrabold text-base text-dark-900 block mt-1">
                                            {{ (int) round($sawResult['raw_scores'][$c->id] ?? 0) }}
                                        </span>
                                        <span class="text-[10px] text-dark-500">
                                            Terbobot: {{ (int) round(($sawResult['weighted_scores'][$c->id] ?? 0) * 100) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- INFORMASI DAFTAR ULANG (KHUSUS STATUS LULUS) --}}
                    @if ($status === 'lulus')
                        <div class="bg-white border-2 border-emerald-300 rounded-2xl p-6 mb-6 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider mb-2 {{ $reg->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        Status Daftar Ulang: {{ $reg->payment_status_label }}
                                    </span>
                                    <h4 class="font-display font-bold text-dark-900 text-base">
                                        Biaya Daftar Ulang Siswa Baru:
                                        <span class="text-emerald-600 font-black">{{ $reg->payment_amount_formatted }}</span>
                                    </h4>
                                    <p class="text-xs text-dark-500 mt-1">
                                        @if ($reg->payment_status === 'paid')
                                            Pembayaran daftar ulang Anda telah lunas. Anda resmi terdaftar sebagai siswa baru!
                                        @else
                                            Silakan selesaikan pembayaran daftar ulang secara online untuk mengonfirmasi kursi Anda.
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    @if ($reg->payment_status === 'paid')
                                        <a href="{{ route('payment.invoice') }}"
                                            class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                                            <i class="fas fa-file-invoice"></i> Unduh Invoice Lunas
                                        </a>
                                    @elseif ($reg->payment_amount > 0)
                                        <a href="{{ route('payment.index') }}"
                                            class="inline-flex items-center gap-2 px-5 py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                                            <i class="fas fa-credit-card"></i> Bayar Daftar Ulang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- CARD JADWAL TES & INFORMASI (KHUSUS STATUS VERIFIED) --}}
                    @if ($status === 'verified')
                        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-6 text-center">
                            <h4 class="font-bold text-blue-800 mb-3 flex items-center justify-center gap-2">
                                <i class="fas fa-calendar-check"></i> JADWAL UJIAN SELEKSI ONLINE (CBT)
                            </h4>
                            @if ($reg->notes)
                                <p class="text-blue-900 font-medium text-sm sm:text-base mb-4 leading-relaxed">
                                    {!! nl2br(e($reg->notes)) !!}
                                </p>
                            @endif

                            <div class="flex flex-wrap items-center justify-center gap-3 mt-4 pt-4 border-t border-blue-200/60">
                                <a href="{{ route('pendaftar.ujian.index') }}"
                                    class="px-5 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center gap-2">
                                    <i class="fas fa-laptop-code"></i> Buka Halaman Ujian CBT
                                </a>
                                <a href="{{ route('pendaftar.kartu') }}" target="_blank"
                                    class="px-5 py-2.5 bg-white hover:bg-blue-50 text-blue-700 font-bold text-xs rounded-xl border border-blue-300 transition-all flex items-center gap-2">
                                    <i class="fas fa-id-badge"></i> Cetak Kartu Peserta
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Card Detail Biodata --}}
                    <div class="border rounded-2xl p-6 mb-8 {{ $theme['card_bg'] }}">
                        <h4 class="font-display font-bold text-dark-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-primary-600"></i>
                            Ringkasan Data Pendaftaran
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between border-b border-black/5 pb-3">
                                <span class="text-dark-500 text-sm">Nomor Pendaftaran</span>
                                <span class="font-mono font-bold text-dark-900 text-sm">{{ $reg->registration_number }}</span>
                            </div>
                            <div class="flex justify-between border-b border-black/5 pb-3">
                                <span class="text-dark-500 text-sm">Nama Lengkap</span>
                                <span class="font-semibold text-dark-900 text-sm">{{ $reg->name }}</span>
                            </div>
                            <div class="flex justify-between border-b border-black/5 pb-3">
                                <span class="text-dark-500 text-sm">NISN</span>
                                <span class="font-mono font-semibold text-dark-900 text-sm">{{ $reg->nisn }}</span>
                            </div>
                            <div class="flex justify-between border-b border-black/5 pb-3">
                                <span class="text-dark-500 text-sm">Asal Sekolah</span>
                                <span class="font-semibold text-dark-900 text-sm">{{ $reg->school_origin }}</span>
                            </div>
                            <div class="flex justify-between border-b border-black/5 pb-3">
                                <span class="text-dark-500 text-sm">Status Seleksi</span>
                                <span class="px-3 py-1 rounded-full font-bold text-xs uppercase {{ $theme['badge_color'] }}">
                                    {{ $reg->status_label }}
                                </span>
                            </div>

                            {{-- Tampilkan Alasan Jika Ditolak --}}
                            @if ($status === 'rejected' && $reg->notes)
                                <div class="pt-3">
                                    <span class="text-red-700 text-xs font-bold uppercase">Catatan Panitia:</span>
                                    <p class="text-red-600 text-sm mt-1 italic">"{{ $reg->notes }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        @if (in_array($status, ['verified', 'lulus', 'tidak_lulus']))
                            <form method="GET" action="{{ route('pendaftar.cek-status.pdf') }}" class="w-full">
                                <input type="hidden" name="nisn" value="{{ $reg->nisn }}">
                                <button type="submit"
                                    class="w-full py-4 bg-dark-900 text-white font-semibold rounded-xl hover:bg-dark-800 transition-colors flex items-center justify-center gap-2">
                                    <i class="fas fa-file-pdf text-rose-400"></i> Unduh Bukti Pendaftaran & Status (PDF)
                                </button>
                            </form>
                        @elseif($status === 'rejected')
                            <a href="{{ route('contact') }}"
                                class="w-full py-4 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-colors text-center flex items-center justify-center gap-2">
                                <i class="fas fa-info-circle"></i> Hubungi Panitia
                            </a>
                        @endif
                        <a href="{{ route('dashboard') }}"
                            class="text-center text-sm text-primary-600 hover:underline font-bold mt-2">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard Pendaftar
                        </a>
                    </div>
                @endif

                {{-- 3. HASIL JIKA TIDAK DITEMUKAN --}}
                @if (session('not_found'))
                    <div class="text-center">
                        <div class="w-28 h-28 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-question-circle text-gray-400 text-4xl"></i>
                        </div>
                        <h3 class="font-display text-2xl font-bold text-dark-900 mb-2">NISN TIDAK SESUAI</h3>
                        <p class="text-dark-500 mb-8">NISN yang Anda masukkan tidak sesuai dengan data pendaftaran akun Anda ({{ $registration->nisn }}).</p>

                        <div class="flex flex-col gap-3">
                            <a href="{{ route('pendaftar.cek-status') }}"
                                class="w-full py-4 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-500 transition-colors">
                                Coba Lagi
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>
@endsection
