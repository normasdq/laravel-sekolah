<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Terjadi Kendala Server | SMP Tunas Harapan</title>
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

<body class="bg-[#090d16] text-slate-100 min-h-screen flex flex-col justify-between selection:bg-purple-600 selection:text-white">
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

    <!-- Konten Utama Error 500 -->
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="max-w-lg w-full text-center">
            <!-- Badge Angka Error -->
            <div class="relative inline-block mb-4">
                <span class="font-display font-black text-8xl md:text-9xl tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-purple-500 via-indigo-400 to-blue-300 drop-shadow-2xl">
                    500
                </span>
                <div class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-purple-500/20 border border-purple-400/40 flex items-center justify-center text-purple-300 text-sm animate-pulse">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>

            <h1 class="font-display font-extrabold text-2xl md:text-3xl text-white mb-3">
                Terjadi Kendala pada Server
            </h1>
            <p class="text-slate-400 text-sm md:text-base leading-relaxed mb-8 max-w-md mx-auto">
                Sistem kami sedang mengalami gangguan teknis sementara. Tim pengelola telah menerima informasi ini dan sedang memperbaikinya. Silakan coba beberapa saat lagi.
            </p>

            <!-- Aksi Pintasan -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <button type="button" onclick="window.location.reload()"
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-500 hover:from-purple-500 hover:to-indigo-400 text-white font-bold text-xs md:text-sm shadow-lg shadow-purple-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>Coba Muat Ulang</span>
                </button>
                <a href="{{ route('contact') }}"
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 hover:text-white font-semibold text-xs md:text-sm transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-headset"></i>
                    <span>Hubungi Panitia SPMB</span>
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

