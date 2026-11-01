<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="google-site-verification" content="{{ $settings->get('google_site_verification') ?? '' }}" />
    <meta name="msvalidate.01" content="{{ $settings->get('msvalidate_01') ?: ($settings->get('msvalidate.01') ?? '') }}" />

    <title>@yield('title', 'SMP Tunas Harapan Bekasi - Berbasis Karakter & Digital')</title>
    @yield('meta_tags')
    <meta name="description" content="@yield('meta_description', 'SMP Tunas Harapan Bekasi - Sekolah Menengah Pertama Berbasis Karakter & Kreativitas Digital')">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-tunas-harapan.png') }}">

    {{-- Typography: Plus Jakarta Sans & Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Vite Production Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .font-display {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media print {
            header,
            footer,
            .whatsapp-pill,
            .flash-msg {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-dark-950 text-dark-100 selection:bg-primary-500 selection:text-white overflow-x-hidden min-h-screen flex flex-col">

    <!-- ==================== FLOATING GLASS NAVBAR ==================== -->
    <header id="main-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 py-4 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <nav id="navbar"
                class="transition-all duration-300 rounded-2xl md:rounded-3xl px-4 sm:px-6 py-3 md:py-3.5 bg-dark-950/85 backdrop-blur-xl border border-white/10 shadow-2xl shadow-black/40 flex items-center justify-between">
                
                <!-- Logo & School Brand -->
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                    <div class="w-11 h-11 md:w-12 md:h-12 rounded-2xl bg-white/10 border border-white/15 p-1.5 flex items-center justify-center group-hover:scale-105 group-hover:border-primary-400/50 transition-all shadow-md">
                        <img src="{{ asset('assets/images/logo-tunas-harapan.png') }}" alt="Logo SMP Tunas Harapan"
                            class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="font-display font-extrabold text-base md:text-lg leading-tight block text-white tracking-tight group-hover:text-primary-300 transition-colors">
                            {{ $settings->get('school_short_name', 'SMP') }} Tunas Harapan
                        </span>
                        <span class="text-[11px] font-medium text-dark-400 block tracking-wide">
                            Islamic & Digital School
                        </span>
                    </div>
                </a>

                <!-- Desktop Menu Navigation -->
                <div class="hidden md:flex items-center gap-1.5 lg:gap-2">
                    @php 
                        $currentRoute = request()->route()?->getName() ?? ''; 
                        $navLinks = [
                            'home' => ['label' => 'Beranda', 'icon' => 'fa-house'],
                            'about' => ['label' => 'Tentang Kami', 'icon' => 'fa-circle-info'],
                            'berita' => ['label' => 'Berita', 'icon' => 'fa-newspaper'],
                            'contact' => ['label' => 'Kontak', 'icon' => 'fa-envelope'],
                        ];
                    @endphp

                    @auth
                        @php
                            $user = auth()->user();
                            $pendaftarLinks = [
                                'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-gauge-high'],
                                'spmb' => ['label' => 'Formulir SPMB', 'icon' => 'fa-file-lines'],
                                'pendaftar.ujian.index' => ['label' => 'Ujian CBT', 'icon' => 'fa-laptop-code'],
                                'pendaftar.cek-status' => ['label' => 'Pengumuman', 'icon' => 'fa-bullhorn'],
                            ];
                        @endphp

                        @if ($user->role === 'user')
                            @foreach ($pendaftarLinks as $route => $item)
                                @php 
                                    $isActive = $currentRoute === $route;
                                @endphp
                                <a href="{{ route($route) }}"
                                    class="px-3.5 py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all duration-200 flex items-center gap-2 {{ $isActive ? 'text-primary-400 bg-primary-500/15 border border-primary-500/30 shadow-sm' : 'text-dark-300 hover:text-white hover:bg-white/5' }}">
                                    <i class="fa-solid {{ $item['icon'] }} text-xs opacity-75"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach

                            <div class="h-5 w-[1px] bg-white/10 mx-1"></div>

                            <!-- Link Akses Web Utama -->
                            <a href="{{ route('home') }}" title="Kunjungi Website Publik"
                                class="px-3 py-1.5 rounded-xl text-xs font-medium text-dark-300 hover:text-white hover:bg-white/5 transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] opacity-75"></i>
                                <span>Web Utama</span>
                            </a>
                        @else
                            <!-- Admin / Penulis / Staff -->
                            <a href="{{ route('admin.dashboard') }}"
                                class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-500 text-white text-xs font-bold rounded-xl hover:from-emerald-500 hover:to-teal-400 transition-all shadow-lg shadow-emerald-500/25 flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved"></i> Panel Admin
                            </a>
                        @endif

                        <div class="h-5 w-[1px] bg-white/10 mx-1"></div>

                        <!-- User Profile Avatar & Quick Actions -->
                        <div class="flex items-center gap-2.5">
                            <!-- Avatar Button with Dedicated Relative Anchor -->
                            <div class="relative">
                                <button id="user-menu-btn" type="button" aria-expanded="false" title="Menu Akun: {{ $user->name }}"
                                    class="w-9 h-9 rounded-xl bg-gradient-to-tr from-primary-600 to-cyan-400 p-0.5 flex-shrink-0 shadow-md hover:scale-105 active:scale-95 transition-all group focus:outline-none focus:ring-2 focus:ring-primary-400/50">
                                    <div class="w-full h-full bg-dark-900 group-hover:bg-dark-850 rounded-[10px] flex items-center justify-center text-primary-300 font-extrabold text-sm transition-colors">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </button>

                                <!-- Dropdown Menu (Strictly positioned below avatar and pointing inward) -->
                                <div id="user-dropdown-menu"
                                    style="top: calc(100% + 12px); right: 0;"
                                    class="hidden absolute w-64 rounded-2xl bg-dark-900/95 backdrop-blur-2xl border border-white/10 shadow-2xl p-2.5 z-50">
                                    <div class="px-3 py-3 border-b border-white/10 mb-1.5 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary-600 to-cyan-400 p-0.5 flex-shrink-0">
                                            <div class="w-full h-full bg-dark-900 rounded-[10px] flex items-center justify-center text-primary-300 font-extrabold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="overflow-hidden">
                                            <p class="text-xs font-bold text-white truncate">{{ $user->name }}</p>
                                            <p class="text-[11px] text-dark-400 truncate">{{ $user->email }}</p>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 mt-1">
                                                {{ $user->role === 'user' ? 'Pendaftar' : $user->role }}
                                            </span>
                                        </div>
                                    </div>

                                    @if ($user->role === 'user')
                                        <a href="{{ route('pendaftar.profile') }}"
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-dark-200 hover:text-white hover:bg-white/10 transition-all">
                                            <i class="fa-solid fa-user-gear text-primary-400 text-xs w-4 text-center"></i>
                                            <span>Profil & Berkas</span>
                                        </a>
                                        <a href="{{ route('pendaftar.kartu') }}"
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-dark-200 hover:text-white hover:bg-white/10 transition-all">
                                            <i class="fa-solid fa-id-badge text-cyan-400 text-xs w-4 text-center"></i>
                                            <span>Kartu Peserta Ujian</span>
                                        </a>
                                        <a href="{{ route('home') }}"
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-dark-200 hover:text-white hover:bg-white/10 transition-all">
                                            <i class="fa-solid fa-globe text-emerald-400 text-xs w-4 text-center"></i>
                                            <span>Kunjungi Web Utama</span>
                                        </a>
                                        <div class="h-px bg-white/10 my-1.5"></div>
                                    @endif

                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-red-400 hover:text-white hover:bg-red-500/20 transition-all text-left">
                                            <i class="fa-solid fa-power-off text-xs w-4 text-center"></i>
                                            <span>Keluar Akun</span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Direct Quick Logout Button -->
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" title="Keluar Akun"
                                    class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 hover:bg-red-500/20 hover:border-red-500/40 text-dark-400 hover:text-red-400 flex items-center justify-center transition-all">
                                    <i class="fa-solid fa-power-off text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Guest State: Menu Brosur Publik & Auth Actions -->
                        @foreach ($navLinks as $route => $item)
                            @php 
                                $isActive = $currentRoute === $route || ($route === 'berita' && str_contains($currentRoute, 'berita'));
                            @endphp
                            <a href="{{ route($route) }}"
                                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'text-primary-400 bg-primary-500/15 border border-primary-500/30 shadow-sm' : 'text-dark-300 hover:text-white hover:bg-white/5' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        <div class="h-5 w-[1px] bg-white/10 mx-2"></div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 text-sm font-semibold text-dark-300 hover:text-white rounded-xl hover:bg-white/5 transition-all">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}"
                                class="shimmer-btn px-5 py-2.5 bg-gradient-to-r from-primary-600 via-primary-500 to-blue-500 text-white text-sm font-bold rounded-xl hover:shadow-lg hover:shadow-primary-500/30 transition-all active:scale-95 flex items-center gap-2">
                                <span>Daftar PPDB</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <button id="offcanvas-open" aria-label="Buka Menu"
                    class="md:hidden w-11 h-11 rounded-xl bg-white/5 border border-white/10 text-white hover:bg-white/10 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-bars-staggered text-lg"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- ==================== MOBILE OFFCANVAS DRAWER ==================== -->
    <div id="offcanvas-menu" class="fixed inset-0 z-[60] invisible transition-all duration-300">
        <!-- Backdrop -->
        <div id="offcanvas-backdrop"
            class="absolute inset-0 bg-dark-950/75 backdrop-blur-md opacity-0 transition-opacity duration-300"></div>

        <!-- Drawer Content -->
        <div id="offcanvas-content"
            class="absolute top-0 right-0 bottom-0 w-[320px] sm:w-[360px] bg-dark-950/95 backdrop-blur-2xl border-l border-white/10 text-white translate-x-full transition-transform duration-500 cubic-bezier(0.16, 1, 0.3, 1) flex flex-col shadow-2xl">
            
            <!-- Drawer Header -->
            <div class="p-6 flex items-center justify-between border-b border-white/10">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/images/logo-tunas-harapan.png') }}" class="w-9 h-9 object-contain" alt="Logo">
                    <div>
                        <span class="font-display font-extrabold text-sm block leading-tight">SMP Tunas Harapan</span>
                        <span class="text-[10px] text-dark-400 block">Navigasi Utama</span>
                    </div>
                </div>
                <button id="offcanvas-close" aria-label="Tutup Menu"
                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 text-dark-400 hover:text-white hover:bg-white/10 transition-all">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Drawer Links -->
            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">
                @auth
                    @php
                        $user = auth()->user();
                        $reg = $user->registration;
                    @endphp
                    <!-- User Account Card in Mobile -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-primary-950/40 to-blue-900/20 border border-primary-500/30">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-primary-500 text-white flex items-center justify-center font-bold text-sm shadow-md">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-[10px] uppercase tracking-wider font-extrabold text-primary-400">
                                    {{ $user->role === 'user' ? 'Akun Pendaftar' : $user->role }}
                                </p>
                                <p class="font-display font-bold text-white text-sm truncate">{{ $user->name }}</p>
                                @if ($reg)
                                    <p class="text-[10px] text-dark-400 font-mono mt-0.5">No. Reg: {{ $reg->registration_number }}</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('dashboard') }}"
                            class="block w-full text-center py-2 bg-primary-500 text-white text-xs font-bold rounded-xl hover:bg-primary-600 transition-all shadow-md">
                            Buka Dashboard Pendaftar
                        </a>
                    </div>

                    @if ($user->role === 'user')
                        <!-- Portal Pendaftar Navigation -->
                        <div class="space-y-1.5">
                            <p class="text-[10px] font-black uppercase tracking-widest text-dark-400 px-3 mb-2">Menu Portal Pendaftar</p>
                            @php
                                $mobilePendaftarLinks = [
                                    'dashboard' => ['label' => 'Dashboard Utama', 'icon' => 'fa-gauge-high'],
                                    'spmb' => ['label' => 'Formulir Pendaftaran SPMB', 'icon' => 'fa-file-signature'],
                                    'pendaftar.ujian.index' => ['label' => 'Ujian Online (CBT)', 'icon' => 'fa-laptop-code'],
                                    'pendaftar.cek-status' => ['label' => 'Pengumuman Kelulusan', 'icon' => 'fa-bullhorn'],
                                    'pendaftar.profile' => ['label' => 'Profil & Berkas Dokumen', 'icon' => 'fa-id-card'],
                                    'pendaftar.kartu' => ['label' => 'Cetak Kartu Ujian', 'icon' => 'fa-print'],
                                ];
                            @endphp
                            @foreach ($mobilePendaftarLinks as $route => $item)
                                @php $isActive = $currentRoute === $route; @endphp
                                <a href="{{ route($route) }}"
                                    class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $isActive ? 'bg-primary-500/20 text-primary-300 border border-primary-500/30' : 'text-dark-300 hover:text-white hover:bg-white/5' }}">
                                    <i class="fa-solid {{ $item['icon'] }} text-xs opacity-75"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Public Links Section in Auth Mobile -->
                    <div class="pt-4 border-t border-white/10 space-y-1.5">
                        <p class="text-[10px] font-black uppercase tracking-widest text-dark-400 px-3 mb-2">Website Utama</p>
                        @foreach ($navLinks as $route => $item)
                            <a href="{{ route($route) }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs text-dark-400 hover:text-white hover:bg-white/5 transition-all">
                                <i class="fa-solid {{ $item['icon'] }} text-[10px] opacity-60"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <!-- Guest Main Nav Links -->
                    <div class="space-y-1.5">
                        <p class="text-[10px] font-black uppercase tracking-widest text-dark-400 px-3 mb-2">Halaman Utama</p>
                        @foreach ($navLinks as $route => $item)
                            @php 
                                $isActive = $currentRoute === $route || ($route === 'berita' && str_contains($currentRoute, 'berita'));
                            @endphp
                            <a href="{{ route($route) }}"
                                class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ $isActive ? 'bg-primary-500/20 text-primary-300 border border-primary-500/30' : 'text-dark-300 hover:text-white hover:bg-white/5' }}">
                                <i class="fa-solid {{ $item['icon'] }} text-xs opacity-75"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>

                    <!-- Fast Links for Guest -->
                    <div class="pt-4 border-t border-white/10 space-y-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-dark-400 px-3 mb-2">Pendaftaran Siswa Baru</p>
                        <a href="{{ route('spmb') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-file-signature text-primary-400 text-sm"></i>
                                <span class="text-sm font-medium text-white">Formulir SPMB</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-dark-400 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="{{ route('pendaftar.cek-status') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-magnifying-glass text-cyan-400 text-sm"></i>
                                <span class="text-sm font-medium text-white">Cek Status Hasil</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-dark-400 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Drawer Footer Actions -->
            <div class="p-6 border-t border-white/10">
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full py-3 text-sm font-bold text-red-400 hover:text-white hover:bg-red-500/20 border border-red-500/30 rounded-xl transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-power-off"></i> Keluar Akun
                        </button>
                    </form>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('login') }}"
                            class="py-3 text-center text-sm font-semibold text-white bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl transition-all">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                            class="py-3 text-center text-sm font-bold text-white bg-primary-600 hover:bg-primary-500 rounded-xl shadow-lg shadow-primary-500/30 transition-all">
                            Daftar
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <main class="flex-grow pt-24">
        @yield('content')
    </main>

    <!-- ==================== MODERN FOOTER ==================== -->
    <footer class="bg-dark-950 text-dark-300 border-t border-white/10 relative overflow-hidden">
        <!-- Ambient footer glow -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-96 h-96 ambient-glow-blue opacity-40 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-16 pb-12 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 pb-14 border-b border-white/10">
                
                <!-- Col 1: Identity & Social -->
                <div class="space-y-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/15 p-2 flex items-center justify-center shadow-md">
                            <img src="{{ asset('assets/images/logo-tunas-harapan.png') }}" class="w-full h-full object-contain" alt="Logo SMP Tunas Harapan">
                        </div>
                        <div>
                            <span class="font-display font-extrabold text-lg text-white block leading-tight">
                                {{ $settings->get('school_short_name', 'SMP') }} Tunas Harapan
                            </span>
                            <span class="text-xs text-primary-400 font-semibold">Akreditasi A Unggul</span>
                        </div>
                    </div>
                    
                    <p class="text-sm text-dark-300 leading-relaxed">
                        {{ $settings->get('school_motto', 'Membentuk generasi cerdas, berkarakter Islami, dan siap menghadapi tantangan era digital.') }}
                    </p>

                    <div class="flex items-center gap-2.5 pt-2">
                        @php
                            $socialLinks = [
                                'facebook' => ['key' => 'school_facebook', 'icon' => 'fa-facebook-f'],
                                'instagram' => ['key' => 'school_instagram', 'icon' => 'fa-instagram'],
                                'youtube' => ['key' => 'school_youtube', 'icon' => 'fa-youtube'],
                                'tiktok' => ['key' => 'school_tiktok', 'icon' => 'fa-tiktok'],
                            ];
                        @endphp
                        @foreach ($socialLinks as $platform => $item)
                            <a href="{{ $settings->get($item['key'], '#') }}" target="_blank" rel="noopener noreferrer"
                                aria-label="{{ $platform }}"
                                class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 text-dark-300 hover:text-white hover:bg-primary-600 hover:border-primary-500 transition-all flex items-center justify-center">
                                <i class="fab {{ $item['icon'] }} text-sm"></i>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div>
                    <h4 class="font-display font-bold text-white text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                        @auth
                            Portal Pendaftar
                        @else
                            Navigasi Utama
                        @endauth
                    </h4>
                    <ul class="space-y-3 text-sm">
                        @auth
                            <li>
                                <a href="{{ route('dashboard') }}" class="text-dark-300 hover:text-primary-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-gauge-high text-xs opacity-50"></i> Dashboard Saya
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('spmb') }}" class="text-dark-300 hover:text-primary-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-file-pen text-xs opacity-50"></i> Formulir SPMB
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pendaftar.ujian.index') }}" class="text-dark-300 hover:text-primary-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-laptop-code text-xs opacity-50"></i> Ujian Seleksi CBT
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pendaftar.cek-status') }}" class="text-dark-300 hover:text-primary-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-bullhorn text-xs opacity-50"></i> Pengumuman Kelulusan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('home') }}" class="text-dark-400 hover:text-white hover:translate-x-1 transition-all inline-flex items-center gap-2 text-xs pt-1 border-t border-white/5">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] opacity-50"></i> Website Utama Sekolah
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('home') }}" class="text-dark-300 hover:text-primary-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-angle-right text-xs opacity-50"></i> Beranda
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('about') }}" class="text-dark-300 hover:text-primary-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-angle-right text-xs opacity-50"></i> Profil & Guru
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('berita') }}" class="text-dark-300 hover:text-primary-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-angle-right text-xs opacity-50"></i> Warta & Artikel
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" class="text-dark-300 hover:text-primary-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-angle-right text-xs opacity-50"></i> Hubungi Kami
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>

                <!-- Col 3: Contact & Location -->
                <div>
                    <h4 class="font-display font-bold text-white text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        Kontak Sekolah
                    </h4>
                    <ul class="space-y-3.5 text-sm">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-primary-400 mt-1"></i>
                            <span class="text-dark-300 leading-snug">{{ $settings->get('school_address', 'Bekasi, Jawa Barat') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-primary-400"></i>
                            <span class="text-dark-300">{{ $settings->get('school_phone', '(021) 8888-9999') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-primary-400"></i>
                            <span class="text-dark-300">{{ $settings->get('school_email', 'info@smptunasharapan.sch.id') }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Col 4: PPDB 2026 Showcase Widget / Akun Pendaftar Widget -->
                <div>
                    @auth
                        @php
                            $user = auth()->user();
                            $reg = $user->registration;
                        @endphp
                        <h4 class="font-display font-bold text-white text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Akun Pendaftar
                        </h4>
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-white/5 to-white/[0.02] border border-white/10 hover:border-primary-500/40 transition-all space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    {{ $reg ? $reg->status_label : 'Akun Terverifikasi' }}
                                </span>
                            </div>
                            <div>
                                <p class="font-display font-bold text-white text-sm truncate">{{ $user->name }}</p>
                                @if ($reg)
                                    <p class="text-[11px] text-dark-400 mt-0.5">No. Reg: <span class="text-white font-mono font-semibold">{{ $reg->registration_number }}</span></p>
                                @else
                                    <p class="text-[11px] text-dark-400 mt-0.5">Formulir belum dilengkapi</p>
                                @endif
                            </div>
                            <div class="pt-1">
                                <a href="{{ route('dashboard') }}"
                                    class="block w-full py-2.5 text-center bg-gradient-to-r from-primary-600 to-blue-500 hover:from-primary-500 hover:to-blue-400 text-white font-bold text-xs rounded-xl shadow-lg shadow-primary-500/20 transition-all">
                                    Buka Dashboard Saya <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Guest State: Promo SPMB & Daftar Sekarang -->
                        <h4 class="font-display font-bold text-white text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            SPMB Online 2026
                        </h4>
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-white/5 to-white/[0.02] border border-white/10 hover:border-primary-500/40 transition-all space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    Gelombang 1 Dibuka
                                </span>
                            </div>
                            <p class="text-xs text-dark-300 leading-relaxed">
                                Pendaftaran peserta didik baru tahun ajaran 2026/2027 dapat dilakukan langsung secara online.
                            </p>
                            <a href="{{ route('register') }}"
                                class="block w-full py-2.5 text-center bg-primary-600 hover:bg-primary-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-primary-500/20 transition-all">
                                Daftar Sekarang <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-dark-400">
                <p>&copy; {{ date('Y') }} {{ $settings->get('school_name', 'SMP Tunas Harapan Bekasi') }}. Seluruh hak cipta dilindungi.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.login') }}" class="hover:text-white transition-colors flex items-center gap-1.5">
                        <i class="fas fa-lock text-[10px]"></i> Portal Admin
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ==================== FLOATING AI CHATBOT & WHATSAPP CS ==================== -->
    @include('components.chatbot-widget')

    <!-- ==================== TOAST FLASH MESSAGES ==================== -->
    @foreach (['success' => ['bg-emerald-500', 'fa-circle-check'], 'error' => ['bg-rose-500', 'fa-circle-exclamation'], 'info' => ['bg-blue-500', 'fa-circle-info']] as $type => $conf)
        @if (session($type))
            <div
                class="flash-msg fixed bottom-6 left-6 z-[100] {{ $conf[0] }} text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 text-sm font-semibold max-w-md animate-fade-in">
                <i class="fas {{ $conf[1] }} text-lg"></i>
                <span class="flex-1">{{ session($type) }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="opacity-75 hover:opacity-100 transition-opacity">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        @endif
    @endforeach

    <!-- Scripts: Scroll Header & Offcanvas -->
    <script>
        // Header Dynamic Blur On Scroll
        const header = document.getElementById('main-header');
        const navbar = document.getElementById('navbar');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.remove('py-4');
                header.classList.add('py-2');
                navbar.classList.add('bg-dark-950/95', 'shadow-2xl', 'shadow-black/60', 'border-white/15');
            } else {
                header.classList.remove('py-2');
                header.classList.add('py-4');
                navbar.classList.remove('bg-dark-950/95', 'shadow-2xl', 'shadow-black/60', 'border-white/15');
            }
        });

        // Mobile Offcanvas Logic
        const offcanvas = document.getElementById('offcanvas-menu');
        const content = document.getElementById('offcanvas-content');
        const backdrop = document.getElementById('offcanvas-backdrop');
        const openBtn = document.getElementById('offcanvas-open');
        const closeBtn = document.getElementById('offcanvas-close');

        function toggleMenu(show) {
            if (show) {
                offcanvas.classList.remove('invisible');
                setTimeout(() => {
                    backdrop.classList.replace('opacity-0', 'opacity-100');
                    content.classList.replace('translate-x-full', 'translate-x-0');
                }, 10);
                document.body.style.overflow = 'hidden';
            } else {
                backdrop.classList.replace('opacity-100', 'opacity-0');
                content.classList.replace('translate-x-0', 'translate-x-full');
                setTimeout(() => {
                    offcanvas.classList.add('invisible');
                    document.body.style.overflow = '';
                }, 300);
            }
        }

        openBtn?.addEventListener('click', () => toggleMenu(true));
        closeBtn?.addEventListener('click', () => toggleMenu(false));
        backdrop?.addEventListener('click', () => toggleMenu(false));

        // Intersection Observer for Smooth Fade-Up Animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.08
        });

        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

        // Auto-dismiss Flash Toasts
        setTimeout(() => {
            document.querySelectorAll('.flash-msg').forEach(el => {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            });
        }, 5000);

        // User Profile Dropdown Toggle
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userDropdownMenu = document.getElementById('user-dropdown-menu');
        if (userMenuBtn && userDropdownMenu) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdownMenu.classList.toggle('hidden');
            });
            document.addEventListener('click', (e) => {
                if (!userMenuBtn.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                    userDropdownMenu.classList.add('hidden');
                }
            });
        }
    </script>
    @stack('scripts')
</body>

</html>
