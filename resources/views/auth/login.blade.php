@extends('layouts.app')

@section('title', 'Masuk Akun Pendaftar - SMP Tunas Harapan')

@section('content')
    <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden py-16 px-4 bg-dark-950">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 ambient-glow-blue opacity-50 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 ambient-glow-cyan opacity-40 blur-3xl pointer-events-none"></div>

        <div class="max-w-md w-full relative z-10 fade-up">
            <!-- Glassmorphism Card -->
            <div class="rounded-3xl p-8 sm:p-10 bg-dark-900/85 backdrop-blur-2xl border border-white/10 shadow-2xl shadow-black/60">
                
                <!-- Card Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-500/15 border border-primary-500/30 text-primary-400 mb-4 shadow-inner">
                        <i class="fa-solid fa-user-shield text-2xl"></i>
                    </div>
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        Masuk Akun SPMB
                    </h2>
                    <p class="text-dark-400 text-xs sm:text-sm mt-1.5 leading-relaxed">
                        Masuk untuk melanjutkan formulir, ujian CBT, atau pantau status pendaftaran.
                    </p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    
                    <!-- Email Field -->
                    <div>
                        <label class="block text-xs font-bold text-dark-300 uppercase tracking-wider mb-2">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-dark-400">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-dark-950/60 border border-white/10 text-white placeholder-dark-500 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all @error('email') border-rose-500 @enderror"
                                placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <p class="text-rose-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label class="block text-xs font-bold text-dark-300 uppercase tracking-wider mb-2">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-dark-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" id="login-password" required
                                class="w-full pl-11 pr-11 py-3.5 rounded-xl bg-dark-950/60 border border-white/10 text-white placeholder-dark-500 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePassword('login-password', this)" aria-label="Lihat Kata Sandi"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-dark-400 hover:text-white transition-colors">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="shimmer-btn w-full py-4 bg-gradient-to-r from-primary-600 via-primary-500 to-blue-500 text-white font-extrabold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 hover:-translate-y-0.5 active:translate-y-0 transition-all text-sm uppercase tracking-wider mt-2">
                        Masuk Sekarang
                    </button>
                </form>

                <!-- Footer Switch -->
                <div class="mt-8 pt-6 border-t border-white/10 text-center text-xs text-dark-400">
                    <span>Belum memiliki akun pendaftar?</span>
                    <a href="{{ route('register') }}" class="text-primary-400 font-bold hover:text-primary-300 hover:underline ml-1">
                        Daftar Akun Baru
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
