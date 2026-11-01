@extends('layouts.app')

@section('title', 'Beranda - ' . $settings->get('school_name', 'SMP Tunas Harapan Bekasi'))

@section('content')

    <!-- ==================== HERO SECTION (MODERN ATMOSPHERIC) ==================== -->
    <section class="relative min-h-[92vh] flex items-center overflow-hidden bg-dark-950 pt-8 pb-20">
        <!-- Ambient Glowing Orbs Background -->
        <div class="absolute -top-32 -left-32 w-[500px] h-[500px] ambient-glow-blue opacity-70 blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/3 -right-32 w-[550px] h-[550px] ambient-glow-cyan opacity-60 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 left-1/3 w-[450px] h-[450px] ambient-glow-purple opacity-40 blur-3xl pointer-events-none"></div>

        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none mix-blend-luminosity">
            <img src="{{ asset('assets/images/gedung-sekolah.jpeg') }}" alt="Gedung SMP Tunas Harapan"
                class="w-full h-full object-cover scale-105" loading="eager">
            <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-dark-950/80 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 w-full">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Hero Content (7 Cols) -->
                <div class="lg:col-span-7 fade-up">
                    
                    <!-- Live Pill Notification -->
                    <div class="inline-flex items-center gap-3 px-4 py-2 bg-white/5 border border-white/10 rounded-full mb-8 backdrop-blur-md hover:border-primary-500/40 transition-all">
                        <span class="flex h-2.5 w-2.5 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <span class="text-white/90 text-xs font-bold uppercase tracking-widest">
                            SPMB Online 2026/2027 — Gelombang 1 Dibuka
                        </span>
                    </div>

                    <!-- Main Dynamic Headline -->
                    <h1 class="font-display text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white leading-[1.08] tracking-tight mb-7">
                        Masa Depan Unggul <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 via-blue-300 to-cyan-300">
                            Dimulai Bersama Kami.
                        </span>
                    </h1>

                    <!-- School Motto -->
                    <p class="text-base sm:text-lg text-dark-300 leading-relaxed max-w-xl mb-10 font-normal">
                        {{ $settings->get('school_motto', 'Membentuk generasi cerdas, berkarakter Islami, dan siap menghadapi tantangan era digital dengan inovasi tiada henti.') }}
                    </p>

                    <!-- CTA Action Buttons -->
                    <div class="flex flex-wrap items-center gap-4 sm:gap-5 mb-14">
                        <a href="{{ route('register') }}"
                            class="shimmer-btn px-8 py-4 bg-gradient-to-r from-primary-600 via-primary-500 to-blue-500 text-white font-bold rounded-2xl shadow-xl shadow-primary-500/25 hover:shadow-primary-500/40 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-3 text-sm sm:text-base">
                            <span>Daftar Sekarang</span>
                            <i class="fa-solid fa-arrow-right-long text-xs"></i>
                        </a>

                        <a href="{{ route('about') }}"
                            class="px-7 py-4 bg-white/5 hover:bg-white/10 text-white font-semibold rounded-2xl border border-white/15 backdrop-blur-md hover:border-white/30 transition-all flex items-center gap-2.5 text-sm sm:text-base">
                            <i class="fa-regular fa-compass text-primary-400"></i>
                            <span>Profil Sekolah</span>
                        </a>

                        <a href="{{ route('pendaftar.cek-status') }}"
                            class="px-5 py-4 text-dark-300 hover:text-cyan-300 font-semibold transition-colors flex items-center gap-2 text-sm sm:text-base">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            <span>Cek Status</span>
                        </a>
                    </div>

                    <!-- Bento Statistics Strip -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 pt-8 border-t border-white/10 max-w-2xl">
                        <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5">
                            <p class="text-2xl sm:text-3xl font-display font-black text-white">600+</p>
                            <p class="text-[11px] font-semibold text-dark-400 uppercase tracking-wider mt-0.5">Siswa Aktif</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5">
                            <p class="text-2xl sm:text-3xl font-display font-black text-white">30+</p>
                            <p class="text-[11px] font-semibold text-dark-400 uppercase tracking-wider mt-0.5">Ekskul & Klub</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5">
                            <p class="text-2xl sm:text-3xl font-display font-black text-emerald-400">A</p>
                            <p class="text-[11px] font-semibold text-dark-400 uppercase tracking-wider mt-0.5">Akreditasi</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5">
                            <p class="text-2xl sm:text-3xl font-display font-black text-cyan-400">100%</p>
                            <p class="text-[11px] font-semibold text-dark-400 uppercase tracking-wider mt-0.5">Lulus Teruji</p>
                        </div>
                    </div>
                </div>

                <!-- Right Hero Card / Visual Bento (5 Cols) -->
                <div class="lg:col-span-5 relative fade-up" style="transition-delay: 150ms">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Floating Pill Badge 1 -->
                        <div class="absolute -top-5 -left-4 z-20 px-4 py-2.5 rounded-2xl bg-dark-900/90 backdrop-blur-xl border border-white/15 shadow-xl flex items-center gap-3 animate-float-slow">
                            <div class="w-8 h-8 rounded-xl bg-primary-500/20 text-primary-400 flex items-center justify-center text-sm font-bold">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-dark-400 font-bold uppercase tracking-wider">Kurikulum Merdeka</p>
                                <p class="text-xs font-bold text-white">Berbasis Karakter Digital</p>
                            </div>
                        </div>

                        <!-- Main Showcase Card with Frame -->
                        <div class="relative z-10 rounded-3xl overflow-hidden border-2 border-white/15 shadow-2xl shadow-black/60 bg-dark-900">
                            <img src="{{ asset('assets/images/gedung-sekolah.jpeg') }}" 
                                class="w-full aspect-[4/5] object-cover hover:scale-105 transition-transform duration-700"
                                alt="Kampus SMP Tunas Harapan">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-dark-950/30 to-transparent"></div>
                            
                            <!-- Bottom Glass Quote Card -->
                            <div class="absolute bottom-6 left-6 right-6 p-6 rounded-2xl bg-dark-900/80 backdrop-blur-xl border border-white/15 shadow-lg">
                                <div class="flex items-center gap-2 mb-2 text-primary-400">
                                    <i class="fa-solid fa-quote-left text-sm"></i>
                                    <span class="text-[10px] uppercase font-bold tracking-widest text-primary-300">Nilai Inti</span>
                                </div>
                                <p class="text-white text-sm font-medium italic leading-relaxed">
                                    "Menyelaraskan kecerdasan intelektual, kemandirian teknologi, dan akhlakul karimah."
                                </p>
                            </div>
                        </div>

                        <!-- Floating Pill Badge 2 -->
                        <div class="absolute -bottom-4 -right-4 z-20 px-4 py-3 rounded-2xl bg-dark-900/90 backdrop-blur-xl border border-white/15 shadow-xl flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-dark-400 font-bold uppercase tracking-wider">Lingkungan Belajar</p>
                                <p class="text-xs font-bold text-white">Aman, Asri & Nyaman</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== PROGRAM UNGGULAN (MODERN BENTO GRID) ==================== -->
    <section class="py-28 bg-white text-dark-900 relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-primary-600 bg-primary-50 mb-3 border border-primary-100">
                        <i class="fa-solid fa-star text-[10px]"></i> Program Unggulan
                    </span>
                    <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold text-dark-900 tracking-tight leading-tight">
                        Ekosistem Pendidikan <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-blue-500">
                            Modern & Menyeluruh
                        </span>
                    </h2>
                </div>
                <p class="text-dark-500 max-w-sm font-medium text-sm sm:text-base leading-relaxed">
                    Dirancang untuk membimbing siswa berprestasi di bidang akademik, teknologi, dan akhlak mulia.
                </p>
            </div>

            <!-- Bento Grid Showcase -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($programs as $index => $program)
                    @php
                        // Memberi aksen visual bervariasi pada bento grid
                        $isFeatured = $index === 0;
                    @endphp
                    <div class="group relative rounded-3xl p-8 sm:p-9 transition-all duration-300 hover-lift {{ $isFeatured ? 'md:col-span-2 bg-gradient-to-br from-primary-900 via-primary-800 to-blue-900 text-white shadow-xl shadow-primary-900/20' : 'bg-gray-50/90 hover:bg-white border border-gray-150 hover:border-primary-200 hover:shadow-xl text-dark-900' }}">
                        
                        <div class="flex items-start justify-between gap-4 mb-8">
                            <!-- Icon Container -->
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-300 {{ $isFeatured ? 'bg-white/10 text-primary-300 border border-white/20 group-hover:scale-110' : 'bg-primary-50 text-primary-600 border border-primary-100 group-hover:bg-primary-500 group-hover:text-white' }}">
                                <i class="{{ $program->icon ?? 'fas fa-graduation-cap' }} text-2xl"></i>
                            </div>

                            <span class="text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full {{ $isFeatured ? 'bg-white/10 text-white border border-white/20' : 'bg-gray-200/70 text-dark-600' }}">
                                Unggulan 0{{ $index + 1 }}
                            </span>
                        </div>

                        <h3 class="font-display text-xl sm:text-2xl font-bold mb-3 tracking-tight {{ $isFeatured ? 'text-white' : 'text-dark-900' }}">
                            {{ $program->title }}
                        </h3>

                        <p class="text-sm leading-relaxed {{ $isFeatured ? 'text-primary-100/90' : 'text-dark-500' }}">
                            {{ $program->description }}
                        </p>
                    </div>
                @empty
                    <!-- Fallback Cards jika database kosong -->
                    <div class="p-8 rounded-3xl bg-gray-50 border border-gray-150">
                        <h3 class="font-bold text-lg mb-2">Program Unggulan</h3>
                        <p class="text-sm text-dark-500">Program sekolah sedang diperbarui.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ==================== FASILITAS SEKOLAH (INTERACTIVE SHOWCASE) ==================== -->
    <section class="py-28 bg-dark-950 text-white relative overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute -top-40 right-0 w-96 h-96 ambient-glow-cyan opacity-40 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-cyan-400 bg-cyan-950/50 mb-3 border border-cyan-500/20">
                    <i class="fa-solid fa-building-columns text-[10px]"></i> Sarana & Prasarana
                </span>
                <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold text-white tracking-tight">
                    Fasilitas Pembelajaran <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Modern</span>
                </h2>
                <p class="text-dark-400 text-sm sm:text-base mt-4 leading-relaxed">
                    Mendukung seluruh aktivitas belajar, eksperimen sains, dan kegiatan ekstrakurikuler dengan infrastruktur yang memadai.
                </p>
            </div>

            <!-- Bento Masonry Facilities Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $facilities = [
                        ['title' => 'Laboratorium Komputer', 'desc' => 'Dukungan perangkat PC modern untuk pembelajaran digital & CBT', 'img' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=700&q=80'],
                        ['title' => 'Perpustakaan Digital', 'desc' => 'Koleksi ribuan buku referensi fisik dan akses e-library', 'img' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=700&q=80'],
                        ['title' => 'Masjid & Pusat Karakter', 'desc' => 'Sarana ibadah dan pembinaan akhlak berjamaah setiap hari', 'img' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=700&q=80'],
                        ['title' => 'Lapangan Olahraga Terpadu', 'desc' => 'Area futsal, basket, voli, dan upacara bendera', 'img' => 'https://images.unsplash.com/photo-1584466977773-e625c37cdd50?auto=format&fit=crop&w=700&q=80'],
                    ];
                @endphp

                @foreach ($facilities as $facility)
                    <div class="group relative rounded-3xl overflow-hidden aspect-[3/4] border border-white/10 hover:border-cyan-400/40 shadow-xl transition-all duration-500">
                        <img src="{{ $facility['img'] }}" alt="{{ $facility['title'] }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-dark-950/40 to-transparent"></div>
                        
                        <div class="absolute bottom-6 left-6 right-6 z-10">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-cyan-300 border border-white/15 backdrop-blur-md mb-2">
                                Fasilitas Kampus
                            </span>
                            <h3 class="font-display text-xl font-bold text-white mb-1.5 leading-snug">
                                {{ $facility['title'] }}
                            </h3>
                            <p class="text-xs text-dark-300 line-clamp-2 leading-relaxed opacity-90">
                                {{ $facility['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ==================== SAMBUTAN KEPALA SEKOLAH (EDITORIAL STYLE) ==================== -->
    <section class="py-28 bg-gray-50 text-dark-900 relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="rounded-3xl lg:rounded-[3.5rem] bg-white border border-gray-200/80 p-8 sm:p-12 lg:p-20 shadow-xl shadow-gray-200/50 relative overflow-hidden">
                
                <!-- Background Large Watermark Quote -->
                <i class="fa-solid fa-quote-right absolute -top-8 -right-8 text-[14rem] text-gray-50 pointer-events-none"></i>

                <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center relative z-10">
                    
                    <!-- Left: Editorial Content (7 Cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest text-primary-600 bg-primary-50 border border-primary-100">
                            <i class="fa-solid fa-feather text-[10px]"></i> Sambutan Kepala Sekolah
                        </span>

                        <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-dark-900 tracking-tight leading-tight">
                            "Membentuk Generasi Berkarakter, Mengukir Prestasi Nyata."
                        </h2>

                        <div class="text-dark-600 text-base sm:text-lg leading-relaxed italic border-l-4 border-primary-500 pl-6 my-6">
                            {!! nl2br(e($settings->get(
                                'headmaster_welcome',
                                'Selamat datang di portal resmi SMP Tunas Harapan Bekasi. Kami berkomitmen memberikan layanan pendidikan terbaik yang mengintegrasikan nilai-nilai akhlak mulia dengan kemandirian teknologi digital guna menyiapkan lulusan yang kompetitif dan berintegritas.',
                            ))) !!}
                        </div>

                        <div class="pt-2 flex items-center gap-4">
                            <div class="w-12 h-1 bg-gradient-to-r from-primary-600 to-blue-400 rounded-full"></div>
                            <div>
                                <p class="font-display font-extrabold text-dark-900 text-base">
                                    {{ $headmaster?->name ?? 'Dra. Hj. Neneng Yeti.D, M.Pd' }}
                                </p>
                                <p class="text-xs text-dark-400 font-semibold">
                                    {{ $headmaster?->subject ?? 'Kepala Sekolah SMP Tunas Harapan' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Headmaster Portrait (5 Cols) -->
                    <div class="lg:col-span-5 flex justify-center">
                        <div class="relative">
                            <!-- Glowing Aura Ring Behind Image -->
                            <div class="absolute -inset-4 bg-gradient-to-tr from-primary-500/20 via-blue-500/20 to-cyan-500/20 rounded-[2.5rem] blur-xl"></div>
                            
                            <div class="relative rounded-[2rem] overflow-hidden border-4 border-white shadow-2xl bg-white">
                                <img src="{{ asset('kepala-sekolah.jpeg') }}" alt="Kepala Sekolah"
                                    class="w-72 sm:w-80 h-[380px] sm:h-[420px] object-cover">
                                
                                <div class="absolute bottom-4 left-4 right-4 p-3.5 rounded-xl bg-dark-950/80 backdrop-blur-md border border-white/10 text-white text-center">
                                    <p class="font-display font-bold text-xs">Pendidikan Karakter & Digital</p>
                                    <p class="text-[10px] text-primary-300">Kepemimpinan Inspiratif</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== WARTA & BERITA TERKINI ==================== -->
    <section class="py-28 bg-white text-dark-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-16 gap-4">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-primary-600 bg-primary-50 mb-3 border border-primary-100">
                        <i class="fa-solid fa-newspaper text-[10px]"></i> Kabar Sekolah
                    </span>
                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-dark-900 tracking-tight">
                        Warta & Kegiatan <span class="text-primary-600">Terbaru</span>
                    </h2>
                </div>
                <a href="{{ route('berita') }}"
                    class="inline-flex items-center gap-2 font-bold text-sm text-primary-600 hover:text-primary-700 transition-colors uppercase tracking-wider">
                    <span>Lihat Semua Berita</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse ($posts as $post)
                    <article class="group rounded-3xl bg-gray-50 border border-gray-150 overflow-hidden hover-lift flex flex-col h-full">
                        <div class="aspect-[16/10] overflow-hidden relative">
                            @if ($post->image)
                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-900 to-dark-900 flex items-center justify-center text-primary-400">
                                    <i class="fa-solid fa-newspaper text-4xl opacity-50"></i>
                                </div>
                            @endif

                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-dark-950/80 text-primary-300 border border-white/10 backdrop-blur-md">
                                {{ $post->category?->name ?? 'Kegiatan' }}
                            </span>
                        </div>

                        <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-3 text-xs font-semibold text-dark-400 mb-3">
                                    <span class="text-primary-600 font-bold">{{ $post->created_at->format('d M Y') }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span>5 Menit Baca</span>
                                </div>

                                <h3 class="font-display font-bold text-lg text-dark-900 group-hover:text-primary-600 transition-colors line-clamp-2 leading-snug mb-3">
                                    <a href="{{ route('berita.detail', $post->slug) }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                            </div>

                            <div class="pt-4 border-t border-gray-200/60 flex items-center justify-between text-xs font-semibold text-primary-600 group-hover:text-primary-700">
                                <span>Baca Selengkapnya</span>
                                <i class="fa-solid fa-chevron-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 text-center py-12 text-dark-400 text-sm">
                        Belum ada warta terbaru yang dipublikasikan.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ==================== CTA FINAL PPDB BANNER ==================== -->
    <section class="py-20 px-6 bg-dark-950 relative overflow-hidden">
        <div class="max-w-7xl mx-auto rounded-3xl lg:rounded-[3.5rem] bg-gradient-to-br from-primary-700 via-primary-600 to-blue-700 p-10 md:p-16 lg:p-24 text-center relative overflow-hidden shadow-2xl shadow-primary-600/30">
            <!-- Decorative Light Aura -->
            <div class="absolute -top-32 -left-32 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-cyan-400/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl mx-auto space-y-6">
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest bg-white/15 text-white border border-white/20 backdrop-blur-md">
                    Penerimaan Siswa Baru 2026/2027
                </span>

                <h2 class="font-display text-3xl sm:text-4xl md:text-6xl font-black text-white tracking-tight leading-tight">
                    Siap Menjadi Bagian dari Generasi Berprestasi?
                </h2>

                <p class="text-primary-100 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto">
                    Daftarkan putra-putri Anda sekarang secara online. Wujudkan potensi terbaik dalam lingkungan belajar yang ramah, inovatif, dan berakhlak.
                </p>

                <div class="pt-6 flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="{{ route('register') }}"
                        class="w-full sm:w-auto px-10 py-5 bg-white text-primary-700 font-extrabold rounded-2xl hover:bg-primary-50 transition-all shadow-xl active:scale-95 text-base uppercase tracking-wider flex items-center justify-center gap-2">
                        <span>Daftar Akun SPMB</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>

                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->get('school_phone', '6281234567890')) }}"
                        target="_blank" rel="noopener noreferrer"
                        class="w-full sm:w-auto px-9 py-5 bg-primary-800/60 hover:bg-primary-800 text-white font-bold rounded-2xl border border-white/20 transition-all active:scale-95 text-base uppercase tracking-wider flex items-center justify-center gap-2.5">
                        <i class="fab fa-whatsapp text-lg text-emerald-400"></i>
                        <span>Konsultasi Panitia</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
