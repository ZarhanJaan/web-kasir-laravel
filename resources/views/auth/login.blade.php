<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Login — {{ $store_name ?? 'web' }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login ke sistem kasir">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('login_template/images/icons/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('login_template/css/login.css') }}">
</head>

<body>

    <!-- Animated Background -->
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Floating Particles -->
    <div class="particles" id="particles"></div>

    <!-- Login Card -->
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Brand -->
            <div class="brand-section">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z" />
                        <path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9" />
                        <path d="M12 3v6" />
                    </svg>
                </div>
                <h1 class="brand-title">{{ $store_name ?? 'web' }}</h1>
                <p class="brand-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input id="email" type="email"
                            class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            placeholder="contoh@email.com">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2" />
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                        </svg>
                    </div>
                    @error('email')
                        <div class="error-msg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" x2="12" y1="8" y2="12" />
                                <line x1="12" x2="12.01" y1="16" y2="16" />
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input id="password" type="password"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                            name="password" required autocomplete="current-password"
                            placeholder="Masukkan password">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Tampilkan password">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" id="eyeIcon">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-msg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" x2="12" y1="8" y2="12" />
                                <line x1="12" x2="12.01" y1="16" y2="16" />
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="btnLogin">
                    <span class="btn-text">
                        Masuk
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                    </span>
                </button>
            </form>

            <!-- Footer -->
            <div class="login-footer">
                <p>
                    Belum punya akun?
                    <a href="{{ route('register') }}">Daftar Sekarang</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // ===== Generate Floating Particles =====
        (function () {
            const container = document.getElementById('particles');
            const count = 30;
            for (let i = 0; i < count; i++) {
                const dot = document.createElement('div');
                dot.classList.add('particle');
                dot.style.left = Math.random() * 100 + '%';
                dot.style.width = dot.style.height = (Math.random() * 3 + 1.5) + 'px';
                dot.style.animationDuration = (Math.random() * 12 + 8) + 's';
                dot.style.animationDelay = (Math.random() * 10) + 's';
                dot.style.opacity = Math.random() * 0.4 + 0.1;
                container.appendChild(dot);
            }
        })();

        // ===== Password Toggle =====
        (function () {
            const toggle = document.getElementById('togglePassword');
            const pwField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (toggle && pwField) {
                toggle.addEventListener('click', function () {
                    const isPassword = pwField.type === 'password';
                    pwField.type = isPassword ? 'text' : 'password';
                    // Swap icon
                    if (isPassword) {
                        eyeIcon.innerHTML = '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/>';
                    } else {
                        eyeIcon.innerHTML = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>';
                    }
                });
            }
        })();
    </script>

</body>

</html>