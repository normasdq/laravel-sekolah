@extends('layouts.app')

@section('title', 'Hubungi Kami - ' . $settings->get('school_name', 'SMP Tunas Harapan'))

@section('content')
    <!-- ==================== HEADER CONTACT ==================== -->
    <section class="relative pt-12 pb-24 bg-dark-950 text-white overflow-hidden">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 ambient-glow-blue opacity-50 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 ambient-glow-cyan opacity-40 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 text-center fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest text-primary-300 bg-primary-950/60 border border-primary-500/30 mb-6 backdrop-blur-md">
                <i class="fa-solid fa-headset text-[10px]"></i> Layanan Informasi
            </span>
            <h1 class="font-display text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight mb-6 max-w-3xl mx-auto leading-tight">
                Ada Pertanyaan? <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 via-blue-300 to-cyan-300">
                    Tim Kami Siap Membantu
                </span>
            </h1>
            <p class="text-dark-300 max-w-xl mx-auto text-base sm:text-lg leading-relaxed">
                Silakan hubungi kami melalui formulir pesan resmi di bawah atau kontak langsung saluran representatif sekolah kami.
            </p>
        </div>
    </section>

    <!-- ==================== CONTACT BODY & FORM ==================== -->
    <section class="py-20 bg-white text-dark-900 relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-start">

                <!-- Left Column: Contact Cards (5 Cols) -->
                <div class="lg:col-span-5 space-y-6 fade-up">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-primary-600 bg-primary-50 border border-primary-100">
                        <i class="fa-solid fa-address-book text-[10px]"></i> Saluran Resmi
                    </span>
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-dark-900 tracking-tight">
                        Informasi Kontak & Layanan
                    </h2>

                    <div class="space-y-4 pt-2">
                        @php
                            $contacts = [
                                ['icon' => 'fa-map-location-dot', 'color' => 'primary', 'title' => 'Alamat Kampus', 'val' => $settings->get('school_address', 'Bekasi, Jawa Barat')],
                                ['icon' => 'fa-phone-volume', 'color' => 'blue', 'title' => 'Telepon / WhatsApp', 'val' => $settings->get('school_phone', '(021) 8888-9999')],
                                ['icon' => 'fa-envelope-open-text', 'color' => 'cyan', 'title' => 'Email Resmi', 'val' => $settings->get('school_email', 'info@smptunasharapan.sch.id')],
                                ['icon' => 'fa-clock', 'color' => 'emerald', 'title' => 'Jam Pelayanan SPMB', 'val' => 'Senin - Jumat: 08.00 - 15.00 WIB'],
                            ];
                        @endphp

                        @foreach ($contacts as $c)
                            <div class="p-5 rounded-2xl bg-gray-50 border border-gray-150 flex items-start gap-4 hover-lift-sm transition-all">
                                <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 text-primary-600 flex items-center justify-center text-lg flex-shrink-0 shadow-sm">
                                    <i class="fa-solid {{ $c['icon'] }}"></i>
                                </div>
                                <div>
                                    <h3 class="font-display font-bold text-sm text-dark-900 mb-0.5">{{ $c['title'] }}</h3>
                                    <p class="text-xs sm:text-sm text-dark-500 leading-relaxed">{{ $c['val'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Direct WhatsApp Card -->
                    <div class="p-6 rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-xl shadow-emerald-600/20 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-base">Konsultasi Cepat PPDB</h3>
                                <p class="text-xs text-emerald-100">Respon cepat via WhatsApp</p>
                            </div>
                        </div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->get('school_phone', '6281234567890')) }}" target="_blank"
                            class="block w-full text-center py-2.5 bg-white text-emerald-700 font-bold text-xs rounded-xl hover:bg-emerald-50 transition-all shadow-md">
                            Mulai Chat Sekarang
                        </a>
                    </div>
                </div>

                <!-- Right Column: Contact Form (7 Cols) -->
                <div class="lg:col-span-7 fade-up">
                    <div class="rounded-3xl p-8 sm:p-10 bg-white border border-gray-150 shadow-xl shadow-gray-200/50">
                        <h3 class="font-display font-bold text-xl text-dark-900 mb-2">
                            Kirim Pesan atau Pertanyaan
                        </h3>
                        <p class="text-xs text-dark-400 mb-8">
                            Isi formulir berikut dan petugas panitia kami akan membalas melalui email atau WhatsApp.
                        </p>

                        <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                            @csrf
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-dark-500 uppercase tracking-wider mb-2">
                                        Nama Lengkap
                                    </label>
                                    <input type="text" name="name" value="{{ old('name') }}" required
                                        class="w-full px-4 py-3.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 text-sm outline-none transition-all @error('name') border-rose-500 @enderror"
                                        placeholder="Contoh: Budi Santoso">
                                    @error('name')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-dark-500 uppercase tracking-wider mb-2">
                                        Alamat Email
                                    </label>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                        class="w-full px-4 py-3.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 text-sm outline-none transition-all @error('email') border-rose-500 @enderror"
                                        placeholder="nama@email.com">
                                    @error('email')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-dark-500 uppercase tracking-wider mb-2">
                                    Pesan / Pertanyaan Detail
                                </label>
                                <textarea name="message" required rows="5"
                                    class="w-full px-4 py-3.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 text-sm outline-none transition-all resize-none @error('message') border-rose-500 @enderror"
                                    placeholder="Tuliskan pertanyaan atau kebutuhan informasi Anda di sini...">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                class="shimmer-btn w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-primary-600 via-primary-500 to-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-2.5">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span>Kirim Pesan Sekarang</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Location Map Frame -->
            <div class="mt-24 fade-up">
                <div class="flex items-center gap-4 mb-6">
                    <h3 class="font-display font-bold text-xl text-dark-900 tracking-tight">Peta Lokasi Kampus</h3>
                    <div class="h-px flex-1 bg-gray-200"></div>
                </div>
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-gray-100">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2346007184847!2d107.00926392499038!3d-6.232773993755419!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e698e9f48e94667%3A0x94fc76a321fc8e47!2sSekolah%20Menengah%20Pertama%20Tunas%20Harapan!5e0!3m2!1sid!2sid!4v1776742249193!5m2!1sid!2sid"
                        width="100%" height="380" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" class="sm:h-[450px]"></iframe>
                </div>
            </div>
        </div>
    </section>
@endsection
