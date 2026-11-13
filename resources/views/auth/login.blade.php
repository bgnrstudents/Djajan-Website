<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="theme-color" content="#ffffff">
    <title>Masuk | Djajan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/login.css', 'resources/js/app.js'])
</head>
<body class="login-page antialiased">
    <main class="login-shell">
        <section class="login-visual" aria-label="Cerita UMKM lokal di Jember">
            <div class="login-photo-stage" aria-hidden="true">
                <img class="login-photo is-active" src="{{ asset('img/admin-login-market.jpg') }}" alt="" fetchpriority="high">
                <img class="login-photo" src="{{ asset('img/admin-login-market-2.jpg') }}" alt="" loading="eager">
                <img class="login-photo" src="{{ asset('img/admin-login-market-3.jpg') }}" alt="" loading="eager">
            </div>
            <a href="{{ route('login') }}" class="login-brand" aria-label="Djajan">
                <img src="{{ asset('logo_djajan_mark.png') }}" alt="" class="login-logo">
                <span>Djajan</span>
            </a>
            <div class="login-visual-footer">
                <div class="login-story">
                    <span class="login-story-label">CERITA DARI SEKITAR KITA</span>
                    <h2>Usaha lokal,<br>tumbuh bersama.</h2>
                    <p>Mendukung langkah kecil, menguatkan ekonomi sekitar.</p>
                </div>
                <div class="login-slide-controls" role="group" aria-label="Pilih foto latar">
                    <button class="login-slide-dot is-active" type="button" aria-label="Tampilkan foto pedagang sayur" aria-pressed="true" data-slide="0"></button>
                    <button class="login-slide-dot" type="button" aria-label="Tampilkan foto pasar tradisional" aria-pressed="false" data-slide="1"></button>
                    <button class="login-slide-dot" type="button" aria-label="Tampilkan foto pedagang pasar" aria-pressed="false" data-slide="2"></button>
                </div>
                <span class="login-location">Kreongan Atas <span aria-hidden="true">·</span> Jember</span>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-form-wrap">
                <a href="{{ route('login') }}" class="login-brand login-brand-mobile" aria-label="Djajan">
                    <img src="{{ asset('logo_djajan_mark.png') }}" alt="" class="login-logo">
                    <span>Djajan</span>
                </a>

                <div class="login-heading">
                    <span class="login-eyebrow">ADMINISTRATOR</span>
                    <h1>Selamat datang</h1>
                    <p>Masuk ke akun Djajan Anda.</p>
                </div>

                <!-- Session Status Alert -->
                @if (session('status'))
                    <div class="login-status mb-5 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm font-medium flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 flex-shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="login-form">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="login-label block text-sm font-medium text-gray-700 mb-1.5">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </div>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Masukkan email admin"
                                class="login-input w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F623F]/20 focus:border-[#0F623F] transition-colors @error('email') border-red-300 focus:ring-red-200 focus:border-red-400 @enderror"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="login-label block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="login-forgot text-xs font-medium text-[#0F623F] hover:text-[#0B4E32] transition-colors">
                                    Lupa password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="login-input w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F623F]/20 focus:border-[#0F623F] transition-colors @error('password') border-red-300 focus:ring-red-200 focus:border-red-400 @enderror"
                            >
                            <button 
                                type="button" 
                                id="toggle-password" 
                                class="login-password-toggle absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
                                title="Tampilkan/Sembunyikan Password"
                            >
                                <!-- Eye icon (Show) -->
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <!-- Eye-off icon (Hide) -->
                                <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input 
                                id="remember_me" 
                                type="checkbox" 
                                name="remember" 
                                class="login-checkbox w-4 h-4 rounded border-gray-300 text-[#0F623F] focus:ring-[#0F623F]"
                            >
                            <span class="ms-2 text-xs font-medium text-gray-600">Ingat Saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="login-submit w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-[#0F623F] hover:bg-[#0B4E32] text-white text-sm font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-[#0F623F]/30"
                        >
                            <span>Masuk</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>

                </form>

                <div class="login-secure-note">
                    <span class="login-secure-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <span>Akses khusus administrator</span>
                </div>

            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const photos = Array.from(document.querySelectorAll('.login-photo'));
            const dots = Array.from(document.querySelectorAll('.login-slide-dot'));
            const visual = document.querySelector('.login-visual');
            let activeSlide = 0;
            let slideTimer;

            function showSlide(index) {
                activeSlide = (index + photos.length) % photos.length;
                photos.forEach((photo, photoIndex) => {
                    const active = photoIndex === activeSlide;
                    photo.classList.toggle('is-active', active);
                    photo.setAttribute('aria-hidden', String(!active));
                });
                dots.forEach((dot, dotIndex) => {
                    const active = dotIndex === activeSlide;
                    dot.classList.toggle('is-active', active);
                    dot.setAttribute('aria-pressed', String(active));
                });
            }

            function startSlideshow() {
                window.clearInterval(slideTimer);
                slideTimer = window.setInterval(() => showSlide(activeSlide + 1), 5200);
            }

            dots.forEach(dot => {
                dot.addEventListener('click', () => {
                    showSlide(Number(dot.dataset.slide));
                    startSlideshow();
                });
            });
            visual.addEventListener('mouseenter', () => window.clearInterval(slideTimer));
            visual.addEventListener('mouseleave', startSlideshow);
            visual.addEventListener('focusin', () => window.clearInterval(slideTimer));
            visual.addEventListener('focusout', startSlideshow);
            showSlide(0);
            startSlideshow();

            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('toggle-password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';

                    if (isPassword) {
                        eyeIcon.classList.add('hidden');
                        eyeOffIcon.classList.remove('hidden');
                    } else {
                        eyeIcon.classList.remove('hidden');
                        eyeOffIcon.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>
