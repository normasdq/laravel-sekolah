@extends('layouts.app')

@section('title', 'Tentang Kami - ' . $settings->get('school_name', 'SMP Tunas Harapan'))

@section('content')
    <!-- ==================== HERO SECTION ABOUT ==================== -->
    <section class="relative pt-12 pb-24 bg-dark-950 text-white overflow-hidden">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 ambient-glow-blue opacity-50 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 ambient-glow-cyan opacity-40 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 text-center fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest text-primary-300 bg-primary-950/60 border border-primary-500/30 mb-6 backdrop-blur-md">
                <i class="fa-solid fa-compass text-[10px]"></i> Mengenal Lebih Dekat
            </span>
            <h1 class="font-display text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight mb-6 max-w-4xl mx-auto leading-tight">
                Membentuk Generasi Berkarakter, <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 via-blue-300 to-cyan-300">
                    Cerdas, & Mandiri Digital
                </span>
            </h1>
            <p class="text-dark-300 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed">
                {{ $settings->get('school_name', 'SMP Tunas Harapan Bekasi') }} berkomitmen menyelenggarakan ekosistem pendidikan yang memadukan keunggulan akademik, integritas moral Islami, dan literasi teknologi modern.
            </p>
        </div>
    </section>

    <!-- ==================== STATISTIK KAMPUS ==================== -->
    <section class="relative -mt-12 z-20">
        <div class="max-w-5xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-white rounded-3xl p-6 sm:p-8 shadow-2xl shadow-black/10 border border-gray-150 text-dark-900">
                <div class="text-center p-3">
                    <p class="font-display text-3xl sm:text-4xl font-extrabold text-primary-600">600+</p>
                    <p class="text-xs text-dark-500 font-bold uppercase tracking-wider mt-1">Siswa Aktif</p>
                </div>
                <div class="text-center p-3 md:border-l border-gray-150">
                    <p class="font-display text-3xl sm:text-4xl font-extrabold text-blue-600">35+</p>
                    <p class="text-xs text-dark-500 font-bold uppercase tracking-wider mt-1">Tenaga Pengajar</p>
                </div>
                <div class="text-center p-3 md:border-l border-gray-150">
                    <p class="font-display text-3xl sm:text-4xl font-extrabold text-cyan-600">30+</p>
                    <p class="text-xs text-dark-500 font-bold uppercase tracking-wider mt-1">Ekstrakurikuler</p>
                </div>
                <div class="text-center p-3 md:border-l border-gray-150">
                    <p class="font-display text-3xl sm:text-4xl font-extrabold text-emerald-600">A</p>
                    <p class="text-xs text-dark-500 font-bold uppercase tracking-wider mt-1">Akreditasi Unggul</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== SEJARAH & NILAI ==================== -->
    <section class="py-24 bg-white text-dark-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center mb-28">
                <!-- Image Side -->
                <div class="relative fade-up">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-gray-150 bg-gray-50">
                        <img src="{{ asset('assets/images/gedung-sekolah.jpeg') }}" alt="Gedung Sekolah"
                            class="w-full h-[420px] object-cover hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-dark-950/60 to-transparent"></div>
                    </div>
                    <div class="absolute -bottom-6 -left-6 p-6 rounded-2xl bg-white border border-gray-150 shadow-xl hidden sm:flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-dark-400 font-bold uppercase tracking-wider">Berpengalaman</p>
                            <p class="font-display font-extrabold text-dark-900 text-base">Mendidik Sejak 2010</p>
                        </div>
                    </div>
                </div>

                <!-- Story Side -->
                <div class="space-y-6 fade-up">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-primary-600 bg-primary-50 border border-primary-100">
                        <i class="fa-solid fa-landmark text-[10px]"></i> Profil Sekolah
                    </span>
                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-dark-900 leading-tight">
                        Mempersiapkan Pemimpin Tangguh Sejak Dini
                    </h2>
                    <p class="text-dark-500 text-base leading-relaxed">
                        SMP Tunas Harapan Bekasi didirikan dengan dedikasi untuk menjadi pusat keunggulan pendidikan yang memadukan integritas moral, kedalaman ilmu pengetahuan, dan keterampilan teknologi praktis.
                    </p>
                    <p class="text-dark-500 text-base leading-relaxed">
                        Kami meyakini bahwa setiap anak memiliki bakat unik. Melalui kurikulum yang holistik, pembiasaan ibadah harian, laboratorium digital, serta aneka wadah ekstrakurikuler, siswa dibimbing untuk menjadi insan yang mandiri, adaptif, dan berwawasan luas.
                    </p>
                </div>
            </div>

            <!-- Visi & Misi Bento Cards -->
            <div class="grid md:grid-cols-2 gap-8 mb-28">
                <!-- Visi -->
                <div class="rounded-3xl p-8 sm:p-10 bg-gradient-to-br from-primary-900 via-primary-800 to-blue-900 text-white shadow-xl hover-lift">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 text-primary-300 border border-white/20 flex items-center justify-center text-2xl mb-6">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest text-primary-300 block mb-2">Visi Kami</span>
                    <h3 class="font-display text-2xl sm:text-3xl font-extrabold mb-4 leading-snug">
                        Terwujudnya Generasi Cerdas Berkarakter Islami & Berdaya Saing Digital.
                    </h3>
                    <p class="text-primary-100/90 text-sm sm:text-base leading-relaxed">
                        Menjadi lembaga pendidikan terdepan yang menghasilkan insan beriman, berbudi pekerti luhur, berprestasi di bidang sains teknologi, serta berjiwa kepemimpinan.
                    </p>
                </div>

                <!-- Misi -->
                <div class="rounded-3xl p-8 sm:p-10 bg-gray-50 border border-gray-200 text-dark-900 hover-lift">
                    <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-600 border border-primary-100 flex items-center justify-center text-2xl mb-6">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest text-primary-600 block mb-2">Misi Kami</span>
                    <ul class="space-y-3.5 text-dark-600 text-sm sm:text-base leading-relaxed">
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-primary-500 text-sm mt-1"></i>
                            <span>Menyelenggarakan proses pembelajaran aktif, inovatif, kreatif, dan menyenangkan.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-primary-500 text-sm mt-1"></i>
                            <span>Menumbuhkembangkan pembiasaan ibadah, akhlak mulia, dan nilai-nilai Al-Qur'an.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-primary-500 text-sm mt-1"></i>
                            <span>Meningkatkan literasi teknologi informasi dan komunikasi untuk menunjang era industri.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-primary-500 text-sm mt-1"></i>
                            <span>Mengembangkan minat, bakat, dan potensi siswa melalui program ekstrakurikuler terpadu.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Guru & Tenaga Pendidik -->
            <div class="fade-up">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-primary-600 bg-primary-50 mb-3 border border-primary-100">
                        <i class="fa-solid fa-chalkboard-user text-[10px]"></i> Pendidik Berdedikasi
                    </span>
                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-dark-900 tracking-tight">
                        Pilar Pendidik & Tenaga Pengajar
                    </h2>
                    <p class="text-dark-500 text-sm sm:text-base mt-2">
                        Didukung oleh dewan guru yang profesional, penuh kasih sayang, dan berdedikasi tinggi dalam mendampingi tumbuh kembang siswa.
                    </p>
                </div>

                @if ($teachers->isNotEmpty())
                    <div class="flex flex-wrap justify-center gap-6 sm:gap-8">
                        @foreach ($teachers as $teacher)
                            @php
                                $photoUrl = null;
                                if (!empty($teacher->photo) && file_exists(public_path('storage/' . $teacher->photo))) {
                                    $photoUrl = asset('storage/' . $teacher->photo);
                                } elseif (str_contains(strtolower($teacher->position), 'kepala') && file_exists(public_path('kepala-sekolah.jpeg'))) {
                                    $photoUrl = asset('kepala-sekolah.jpeg');
                                } else {
                                    $photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($teacher->name) . '&background=0284c7&color=fff&size=256&bold=true';
                                }
                            @endphp
                            <div class="w-full sm:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.5rem)] max-w-sm group rounded-3xl bg-gray-50 border border-gray-150 p-6 text-center hover-lift transition-all">
                                <div class="w-32 h-32 mx-auto rounded-2xl overflow-hidden mb-5 border-4 border-white shadow-md bg-gray-100 relative group-hover:shadow-primary-500/20 group-hover:border-primary-100 transition-all">
                                    <img src="{{ $photoUrl }}" alt="{{ $teacher->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=0284c7&color=fff&size=256&bold=true';">
                                </div>
                                <h3 class="font-display font-bold text-dark-900 text-lg mb-1">
                                    {{ $teacher->name }}
                                </h3>
                                <p class="text-xs text-primary-600 font-extrabold uppercase tracking-wider mb-3">
                                    {{ $teacher->position ?? 'Tenaga Pendidik' }}
                                </p>
                                @if ($teacher->bio)
                                    <p class="text-xs text-dark-500 leading-relaxed italic line-clamp-3">
                                        "{{ $teacher->bio }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Staf Administrasi & Pendukung -->
                @if (isset($staff) && $staff->isNotEmpty())
                    <div class="mt-20 rounded-3xl bg-gray-50 border border-gray-200/80 p-8 sm:p-12">
                        <div class="text-center max-w-xl mx-auto mb-10">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-primary-600 bg-white border border-primary-100 mb-2">
                                <i class="fa-solid fa-users-gear text-[10px]"></i> Tenaga Kependidikan
                            </span>
                            <h3 class="font-display text-2xl font-bold text-dark-900">
                                Staf Administrasi &amp; Pendukung
                            </h3>
                            <p class="text-xs sm:text-sm text-dark-500 mt-1">
                                Mendukung kelancaran layanan operasional, tata usaha, dan administrasi sekolah.
                            </p>
                        </div>

                        <div class="flex flex-wrap justify-center gap-6 sm:gap-8">
                            @foreach ($staff as $s)
                                @php
                                    $staffPhotoUrl = null;
                                    if (!empty($s->photo) && file_exists(public_path('storage/' . $s->photo))) {
                                        $staffPhotoUrl = asset('storage/' . $s->photo);
                                    } else {
                                        $staffPhotoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($s->name) . '&background=0f172a&color=fff&size=256&bold=true';
                                    }
                                @endphp
                                <div class="w-full sm:w-64 bg-white rounded-2xl p-6 text-center border border-gray-150 shadow-sm hover-lift transition-all">
                                    <div class="w-20 h-20 mx-auto rounded-full overflow-hidden mb-4 border-4 border-gray-50 shadow bg-gray-100">
                                        <img src="{{ $staffPhotoUrl }}" alt="{{ $s->name }}"
                                            class="w-full h-full object-cover"
                                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($s->name) }}&background=0f172a&color=fff&size=256&bold=true';">
                                    </div>
                                    <h4 class="font-display font-bold text-dark-900 text-sm mb-1">{{ $s->name }}</h4>
                                    <p class="text-xs text-primary-600 font-extrabold uppercase tracking-wider">{{ $s->position ?? 'Staf Tata Usaha' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
