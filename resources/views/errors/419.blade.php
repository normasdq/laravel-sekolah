<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Sesi Halaman Berakhir | SMP Tunas Harapan</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-tunas-harapan.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="bg-[#090d16] text-slate-100 min-h-screen flex flex-col justify-between selection:bg-amber-600 selection:text-white">
    <!-- Header Minimalis -->
    <header class="py-6 px-6 max-w-7xl mx-auto w-full flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 p-1 flex items-center justify-center">
                <img src="{{ asset('assets/images/logo-tunas-harapan.png') }}" alt="Logo SMP Tunas Harapan" class="w-full h-full object-contain">
            </div>
            <span class="font-display font-extrabold text-white text-base">SMP Tunas Harapan</span>
        </a>
        <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Website</span>
        </a>
    </header>

    <!-- Konten Utama Error 419 -->
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="max-w-lg w-full text-center">
            <!-- Badge Angka Error -->
            <div class="relative inline-block mb-4">
                <span class="font-display font-black text-8xl md:text-9xl tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-500 via-orange-400 to-yellow-300 drop-shadow-2xl">
                    419
                </span>
                <div class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-amber-500/20 border border-amber-400/40 flex items-center justify-center text-amber-300 text-sm animate-pulse">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>

            <h1 class="font-display font-extrabold text-2xl md:text-3xl text-white mb-3">
                Sesi Halaman Telah Berakhir
            </h1>
            <p class="text-slate-400 text-sm md:text-base leading-relaxed mb-8 max-w-md mx-auto">
                Halaman ini telah terbuka terlalu lama sehingga token keamanan kedaluwarsa demi melindungi keamanan data akun Anda. Silakan muat ulang halaman untuk melanjutkan.
            </p>

            <!-- Aksi Pintasan -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <button type="button" onclick="window.location.reload()"
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-amber-600 to-orange-500 hover:from-amber-500 hover:to-orange-400 text-white font-bold text-xs md:text-sm shadow-lg shadow-amber-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>Muat Ulang Halaman</span>
                </button>
                <a href="{{ route('login') }}"
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 hover:text-white font-semibold text-xs md:text-sm transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Masuk Kembali</span>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer Copyright -->
    <footer class="py-6 text-center text-xs text-slate-500 border-t border-white/5">
        &copy; {{ date('Y') }} SMP Tunas Harapan Bekasi. Seluruh hak cipta dilindungi.
    </footer>
</body>

</html>

