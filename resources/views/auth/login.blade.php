<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-[#F8FAFC]">
        <div class="w-full max-w-4xl bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-12">
            
            <!-- LEFT SIDE: Branding Panel (Desktop Only) -->
            <div class="hidden md:flex md:col-span-5 bg-[#0F623F] p-8 text-white flex-col justify-between relative overflow-hidden">
                <!-- Background Accents -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>

                <!-- Brand Logo & Header -->
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center border border-white/20 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </div>
                        <div>
                            <span class="text-2xl font-bold font-heading tracking-tight text-white block leading-none">Djajan</span>
                            <span class="text-[11px] text-white/80 font-medium mt-1 block">Admin Portal</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/20">
                            Desa Kreyongan Atas
                        </span>
                        <h2 class="text-2xl font-bold font-heading leading-tight text-white">
                            Sistem Informasi Pengelolaan UMKM
                        </h2>
                        <p class="text-sm text-white/85 leading-relaxed">
                            Platform terpadu untuk pendataan usaha, katalog produk unggulan, dan pengelolaan sistem informasi UMKM desa.
                        </p>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="relative z-10 pt-6 border-t border-white/15">
                    <div class="flex items-center gap-2 text-xs text-white/75">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span>Akses khusus Administrator</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE: Login Form Panel -->
            <div class="md:col-span-7 p-6 sm:p-8 lg:p-10 flex flex-col justify-center">

                <!-- Mobile Brand Header -->
                <div class="md:hidden flex items-center gap-2.5 mb-6">
                    <div class="w-9 h-9 bg-[#0F623F] rounded-lg flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold font-heading text-[#111827]">Djajan</span>
                        <span class="text-xs text-gray-500 block -mt-1">Admin Website</span>
                    </div>
                </div>

                <!-- Title & Subtitle -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-[#111827] font-heading tracking-tight">
                        Masuk ke Admin Djajan
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Kelola data UMKM dan produk dengan mudah.
                    </p>
                </div>

                <!-- Session Status Alert -->
                @if (session('status'))
                    <div class="mb-5 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm font-medium flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 flex-shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
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
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F623F]/20 focus:border-[#0F623F] transition-colors @error('email') border-red-300 focus:ring-red-200 focus:border-red-400 @enderror"
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
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-medium text-[#0F623F] hover:text-[#0B4E32] transition-colors">
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
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F623F]/20 focus:border-[#0F623F] transition-colors @error('password') border-red-300 focus:ring-red-200 focus:border-red-400 @enderror"
                            >
                            <button 
                                type="button" 
                                id="toggle-password" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
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
                                class="w-4 h-4 rounded border-gray-300 text-[#0F623F] focus:ring-[#0F623F]"
                            >
                            <span class="ms-2 text-xs font-medium text-gray-600">Ingat Saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-[#0F623F] hover:bg-[#0B4E32] text-white text-sm font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-[#0F623F]/30"
                        >
                            <span>Masuk</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
    @endpush
</x-guest-layout>
