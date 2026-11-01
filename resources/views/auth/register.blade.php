@extends('layouts.app')

@section('title', 'Daftar Akun Pendaftar - SMP Tunas Harapan')

@section('content')
    <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden py-16 px-4 bg-dark-950">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 ambient-glow-blue opacity-50 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 ambient-glow-cyan opacity-40 blur-3xl pointer-events-none"></div>

        <div class="max-w-lg w-full relative z-10 fade-up">
            <!-- Glassmorphism Card -->
            <div class="rounded-3xl p-8 sm:p-10 bg-dark-900/85 backdrop-blur-2xl border border-white/10 shadow-2xl shadow-black/60">
                
                <!-- Card Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-500/15 border border-primary-500/30 text-primary-400 mb-4 shadow-inner">
                        <i class="fa-solid fa-user-plus text-2xl"></i>
                    </div>
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        Registrasi Calon Siswa
                    </h2>
                    <p class="text-dark-400 text-xs sm:text-sm mt-1.5 leading-relaxed">
                        Satu akun untuk seluruh proses pendaftaran, ujian online (CBT), dan daftar ulang.
                    </p>
                </div>

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-dark-300 uppercase tracking-wider mb-2">
                            Nama Lengkap Calon Siswa / Wali
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-dark-400">
                                <i class="fa-solid fa-user text-sm"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-dark-950/60 border border-white/10 text-white placeholder-dark-500 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all @error('name') border-rose-500 @enderror"
                                placeholder="Contoh: Muhammad Budi">
                        </div>
                        @error('name')
                            <p class="text-rose-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email Aktif -->
                    <div>
                        <label class="block text-xs font-bold text-dark-300 uppercase tracking-wider mb-2">
                            Alamat Email Aktif (Gmail / Yahoo)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-dark-400">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-dark-950/60 border border-white/10 text-white placeholder-dark-500 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all @error('email') border-rose-500 @enderror"
                                placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <p class="text-rose-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password & Confirmation (2 Cols) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-dark-300 uppercase tracking-wider mb-2">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-dark-400">
                                    <i class="fa-solid fa-lock text-xs"></i>
                                </span>
                                <input type="password" name="password" id="reg-password" required
                                    class="w-full pl-9 pr-10 py-3 rounded-xl bg-dark-950/60 border border-white/10 text-white placeholder-dark-500 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all"
                                    placeholder="Min. 8 karakter">
                                <button type="button" onclick="togglePassword('reg-password', this)" aria-label="Lihat Sandi"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-dark-400 hover:text-white transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-dark-300 uppercase tracking-wider mb-2">
                                Konfirmasi Sandi
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-dark-400">
                                    <i class="fa-solid fa-lock-open text-xs"></i>
                                </span>
                                <input type="password" name="password_confirmation" id="reg-password-confirm" required
                                    class="w-full pl-9 pr-10 py-3 rounded-xl bg-dark-950/60 border border-white/10 text-white placeholder-dark-500 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all"
                                    placeholder="Ulangi sandi">
                                <button type="button" onclick="togglePassword('reg-password-confirm', this)" aria-label="Lihat Sandi"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-dark-400 hover:text-white transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-rose-400 text-xs font-medium flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                        </p>
                    @enderror

                    <!-- Info Box: Verification -->
                    <div class="p-3.5 rounded-xl bg-primary-950/40 border border-primary-500/30 flex items-start gap-3 mt-4">
                        <i class="fa-solid fa-envelope-circle-check text-primary-400 mt-0.5 text-sm"></i>
                        <p class="text-[11px] text-primary-200/90 leading-relaxed">
                            Link verifikasi akan dikirim ke email Anda. Harap pastikan email aktif dan dapat menerima pesan.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="shimmer-btn w-full py-4 bg-gradient-to-r from-primary-600 via-primary-500 to-blue-500 text-white font-extrabold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 hover:-translate-y-0.5 active:translate-y-0 transition-all text-sm uppercase tracking-wider mt-3">
                        Buat Akun Pendaftar
                    </button>
                </form>

                <!-- Footer Switch -->
                <div class="mt-8 pt-6 border-t border-white/10 text-center text-xs text-dark-400">
                    <span>Sudah memiliki akun pendaftar?</span>
                    <a href="{{ route('login') }}" class="text-primary-400 font-bold hover:text-primary-300 hover:underline ml-1">
                        Masuk Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endpush
