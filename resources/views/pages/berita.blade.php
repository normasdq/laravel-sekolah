@extends('layouts.app')

@section('title', 'Warta & Artikel - ' . $settings->get('school_name', 'SMP Tunas Harapan Bekasi'))

@section('content')
    <!-- ==================== HEADER BERITA ==================== -->
    <section class="relative pt-12 pb-24 bg-dark-950 text-white overflow-hidden">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 ambient-glow-blue opacity-50 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 ambient-glow-cyan opacity-40 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 text-center fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest text-primary-300 bg-primary-950/60 border border-primary-500/30 mb-6 backdrop-blur-md">
                <i class="fa-solid fa-newspaper text-[10px]"></i> Kabar & Prestasi
            </span>
            <h1 class="font-display text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight mb-6 max-w-3xl mx-auto leading-tight">
                Warta Sekolah & <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 via-blue-300 to-cyan-300">
                    Artikel Edukasi
                </span>
            </h1>
            <p class="text-dark-300 max-w-xl mx-auto text-base sm:text-lg leading-relaxed">
                Ikuti liputan kegiatan, prestasi siswa, pengumuman resmi, dan artikel inspiratif dari SMP Tunas Harapan.
            </p>
        </div>
    </section>

    <!-- ==================== NEWS GRID SECTION ==================== -->
    <section class="py-20 bg-white text-dark-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            @if ($posts->count())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($posts as $post)
                        <article class="group rounded-3xl bg-gray-50 border border-gray-150 overflow-hidden hover-lift flex flex-col h-full transition-all duration-300">
                            <!-- Image Header -->
                            <div class="aspect-[16/10] overflow-hidden relative bg-dark-900">
                                @if ($post->image)
                                    <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-950 to-dark-900 flex items-center justify-center text-primary-400">
                                        <i class="fa-solid fa-newspaper text-4xl opacity-50"></i>
                                    </div>
                                @endif

                                @if ($post->category)
                                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-dark-950/80 text-primary-300 border border-white/10 backdrop-blur-md">
                                        {{ $post->category->name }}
                                    </span>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="p-7 flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-3 text-xs font-semibold text-dark-400 mb-3">
                                        <span class="text-primary-600 font-bold flex items-center gap-1.5">
                                            <i class="fa-regular fa-calendar text-[11px]"></i>
                                            {{ $post->created_at->format('d M Y') }}
                                        </span>
                                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-regular fa-clock text-[11px]"></i>
                                            {{ $post->reading_time ?? '5' }} Min
                                        </span>
                                    </div>

                                    <h3 class="font-display font-bold text-lg text-dark-900 group-hover:text-primary-600 transition-colors line-clamp-2 leading-snug mb-3">
                                        <a href="{{ route('berita.detail', $post->slug) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h3>

                                    <p class="text-xs sm:text-sm text-dark-500 leading-relaxed line-clamp-3 mb-6">
                                        {{ $post->excerpt }}
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-gray-200/70 flex items-center justify-between text-xs font-bold text-primary-600 group-hover:text-primary-700">
                                    <a href="{{ route('berita.detail', $post->slug) }}" class="inline-flex items-center gap-1.5">
                                        <span>Baca Selengkapnya</span>
                                        <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-16 flex justify-center">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="text-center py-16 text-dark-400">
                    <i class="fa-solid fa-newspaper text-5xl mb-4 text-gray-300"></i>
                    <p class="font-display font-bold text-lg text-dark-700">Belum Ada Warta Terbaru</p>
                    <p class="text-sm mt-1">Artikel dan berita sekolah akan segera diperbarui.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
