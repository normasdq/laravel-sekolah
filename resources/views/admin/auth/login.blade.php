<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Administrator - SMP Tunas Harapan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background: radial-gradient(circle at 50% 15%, #1e293b 0%, #090d16 100%) !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0;
            padding: 24px 16px;
            overflow-x: hidden;
            color: #f8fafc;
        }

        /* Ambient Glow Orbs */
        .ambient-glow-blue {
            position: absolute;
            top: -60px;
            left: 20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.22) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
        }

        .ambient-glow-cyan {
            position: absolute;
            bottom: -60px;
            right: 20%;
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.18) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
        }

        .login-box {
            max-width: 440px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .login-card-body {
            background: rgba(15, 23, 42, 0.78) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 24px !important;
            padding: 2.25rem 2rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6) !important;
            color: #ffffff !important;
        }

        .brand-logo-wrapper {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            transition: transform 0.2s ease;
        }

        .brand-logo-wrapper:hover {
            transform: scale(1.05);
        }

        .brand-title {
            font-size: 1.45rem;
            font-weight: 800;
            color: #ffffff !important;
            letter-spacing: -0.025em;
            line-height: 1.2;
            text-decoration: none !important;
        }

        .brand-subtitle-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 9999px;
            background: rgba(37, 99, 235, 0.18);
            color: #93c5fd;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border: 1px solid rgba(147, 197, 253, 0.25);
            margin-top: 6px;
        }

        /* Seamless Dark Input Form */
        .custom-input-dark {
            width: 100% !important;
            height: 48px !important;
            padding: 10px 16px 10px 44px !important;
            border-radius: 12px !important;
            border: 1px solid rgba(255, 255, 255, 0.16) !important;
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #ffffff !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
        }

        .custom-input-dark:focus {
            background-color: rgba(255, 255, 255, 0.08) !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3) !important;
            outline: none !important;
        }

        .custom-input-dark::placeholder {
            color: #64748b !important;
            font-weight: 400 !important;
        }

        .input-icon-left {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
            pointer-events: none;
            z-index: 5;
            transition: color 0.2s ease;
        }

        .position-relative:focus-within .input-icon-left {
            color: #60a5fa;
        }

        .password-toggle-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 4px;
            cursor: pointer;
            z-index: 5;
            transition: color 0.2s ease;
        }

        .password-toggle-btn:hover {
            color: #ffffff;
        }

        .btn-primary-gradient {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            border: none !important;
            border-radius: 12px !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 14.5px !important;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.4) !important;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease !important;
        }

        .btn-primary-gradient:hover:not(:disabled) {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(37, 99, 235, 0.5) !important;
        }

        .btn-primary-gradient:disabled {
            background: #475569 !important;
            box-shadow: none !important;
            cursor: not-allowed !important;
        }

        .alert-ban {
            background-color: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 14px;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="ambient-glow-blue"></div>
    <div class="ambient-glow-cyan"></div>

    <div class="login-box">
        <!-- Brand Header -->
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="d-inline-flex flex-column align-items-center text-decoration-none">
                <div class="brand-logo-wrapper">
                    <img src="{{ asset('assets/images/logo-tunas-harapan.png') }}" alt="Logo" style="width: 40px; height: 40px; object-fit: contain;">
                </div>
                <h1 class="brand-title mb-0">SMP Tunas Harapan</h1>
                <span class="brand-subtitle-pill">Portal Administrator</span>
            </a>
        </div>

        <!-- Login Card Body -->
        <div class="card login-card-body">
            <div class="text-center mb-4">
                <h2 class="font-weight-bold mb-1" style="font-size: 1.35rem; color: #ffffff !important;">Masuk Workspace</h2>
                <p class="small mb-0" style="color: #94a3b8 !important;">Masukkan kredensial akun terdaftar Anda.</p>
            </div>

            <!-- Flash Error -->
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2.5 px-3 mb-3 border-0"
                    style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; border-radius: 12px;" role="alert">
                    <small class="font-weight-bold d-flex align-items-center">
                        <i class="fa-solid fa-circle-exclamation mr-2" style="font-size: 14px;"></i>
                        <span>{{ session('error') }}</span>
                    </small>
                    <button type="button" class="close text-white" data-dismiss="alert" style="opacity: 0.8;">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            <!-- Lockout Ban Alert -->
            @if ($banned)
                <div class="alert-ban p-3 mb-4 text-center" id="ban-alert">
                    <i class="fa-solid fa-lock fa-2x mb-2 text-danger"></i>
                    <h6 class="font-weight-bold mb-1 text-white">Akun Terkunci Sementara</h6>
                    <p class="mb-2" style="font-size: 12.5px; color: #fca5a5;">Terlalu banyak percobaan login gagal. Silakan tunggu:</p>
                    <div class="font-weight-bold text-2xl text-danger" id="countdown-timer">{{ $wait_time }} detik</div>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('admin.login') }}" id="login-form">
                @csrf

                <!-- Email -->
                <div class="form-group mb-3">
                    <div class="position-relative">
                        <span class="input-icon-left">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="custom-input-dark @error('email') is-invalid @enderror" placeholder="Email administrator"
                            @if ($banned) disabled @endif>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block text-danger font-weight-bold mt-1" style="font-size: 12px; color: #fca5a5 !important;">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group mb-3">
                    <div class="position-relative">
                        <span class="input-icon-left">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="password-input" name="password" required
                            class="custom-input-dark @error('password') is-invalid @enderror" placeholder="Kata sandi"
                            style="padding-right: 42px !important;"
                            @if ($banned) disabled @endif>
                        <button type="button" id="toggle-password" class="password-toggle-btn" title="Tampilkan/Sembunyikan Sandi">
                            <i class="fa-solid fa-eye" id="toggle-password-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block text-danger font-weight-bold mt-1" style="font-size: 12px; color: #fca5a5 !important;">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                            @if ($banned) disabled @endif>
                        <label class="custom-control-label small font-weight-medium" for="remember" style="color: #cbd5e1; cursor: pointer; user-select: none;">
                            Ingat sesi saya
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary-gradient" id="login-btn"
                    @if ($banned) disabled @endif>
                    <span>Masuk</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <!-- Back to School Website -->
            <div class="text-center pt-3 mt-4 border-top" style="border-color: rgba(255, 255, 255, 0.08) !important;">
                <a href="{{ route('home') }}" class="small font-weight-semibold text-decoration-none" style="color: #94a3b8; transition: color 0.2s ease;">
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Beranda Sekolah
                </a>
            </div>
        </div>

        <!-- Footer Copyright -->
        <p class="text-center mt-3 text-xs mb-0" style="color: #64748b; font-size: 12px;">
            &copy; {{ date('Y') }} SMP Tunas Harapan Bekasi &bull; All Rights Reserved.
        </p>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Password Show/Hide Toggle
            const toggleBtn = document.getElementById('toggle-password');
            const passInput = document.getElementById('password-input');
            const toggleIcon = document.getElementById('toggle-password-icon');

            if (toggleBtn && passInput && toggleIcon) {
                toggleBtn.addEventListener('click', function () {
                    const isPass = passInput.getAttribute('type') === 'password';
                    passInput.setAttribute('type', isPass ? 'text' : 'password');
                    toggleIcon.classList.toggle('fa-eye', !isPass);
                    toggleIcon.classList.toggle('fa-eye-slash', isPass);
                });
            }
        });
    </script>

    @if ($banned)
        <script>
            let timeLeft = {{ $wait_time }};
            const timerElement = document.getElementById('countdown-timer');
            const loginBtn = document.getElementById('login-btn');
            const banAlert = document.getElementById('ban-alert');

            function formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
            }

            const countdownInterval = setInterval(function () {
                timeLeft--;
                if (timerElement) {
                    timerElement.innerText = formatTime(timeLeft);
                }

                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    banAlert?.remove();
                    if (loginBtn) {
                        loginBtn.disabled = false;
                        loginBtn.innerHTML = '<span>Masuk</span> <i class="fa-solid fa-arrow-right"></i>';
                    }

                    document.querySelectorAll('#login-form input').forEach(function (input) {
                        input.disabled = false;
                    });

                    document.querySelector('input[name="email"]')?.focus();
                }
            }, 1000);

            if (timerElement) {
                timerElement.innerText = formatTime(timeLeft);
            }
        </script>
    @endif

</body>

</html>
