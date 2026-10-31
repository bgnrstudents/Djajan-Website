<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Djajan - Platform UMKM Modern')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Inter + Sora + Lora wordmark -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Lora:ital,wght@0,600;0,700;1,600;1,700&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        hero: ['Sora', 'sans-serif'],
                        wordmark: ['Lora', 'serif']
                    },
                    colors: {
                        'dj-dark': '#022c22',
                        'dj-green': '#064e3b',
                    }
                }
            }
        }
    </script>

    <style>
        /* Smooth transitions untuk carousel */
        .slide-bg {
            transition: opacity 1.2s ease-in-out, transform 8s ease-out;
            transform: scale(1.05);
        }
        .slide-bg.active {
            opacity: 1 !important;
            transform: scale(1);
        }
        
        /* Active desktop navigation: clean text with a subtle underline. */
        .nav-link::after {
            content: '';
            position: absolute;
            right: 0.75rem;
            bottom: 0.2rem;
            left: 0.75rem;
            height: 2px;
            border-radius: 9999px;
            background: #064e3b;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 200ms ease;
        }
        .nav-link.is-active::after { transform: scaleX(1); }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #022c22; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #064e3b; }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased overflow-x-hidden">

    <!-- ==================== FLOATING PILL NAVBAR ==================== -->
    <div class="fixed top-6 left-1/2 z-50 w-[calc(100%-3rem)] max-w-[1100px] -translate-x-1/2 lg:w-[calc(100%-4rem)]">
        <nav class="flex items-center justify-between rounded-full border border-gray-100 bg-white/95 py-2 pl-4 pr-2 shadow-sm backdrop-blur-md sm:pl-6">
            <a href="#home" class="relative z-10 flex group" aria-label="Djajan - Beranda">
                <img src="{{ asset('logo_djajan.png') }}" alt="Djajan" class="h-9 w-auto object-contain transition-transform group-hover:scale-105">
            </a>

            <div class="absolute left-1/2 hidden -translate-x-1/2 items-center gap-2 lg:flex">
                <a href="#home" data-nav-link class="nav-link relative px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-dj-green">Utama</a>
                <a href="#tentang" data-nav-link class="nav-link relative px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-dj-green">Tentang Kami</a>
                <a href="#produk" data-nav-link class="nav-link relative px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-dj-green">Produk</a>
                <a href="#umkm" data-nav-link class="nav-link relative px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-dj-green">UMKM</a>
                <a href="#kontak" data-nav-link class="nav-link relative px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-dj-green">Kontak</a>
            </div>

            <div class="relative z-10 flex items-center">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="group hidden items-center gap-4 rounded-full bg-dj-dark py-1.5 pl-6 pr-2 text-sm font-medium text-white transition-all hover:bg-dj-green hover:shadow-md md:flex">
                        Dashboard
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white transition-transform group-hover:translate-x-1">
                            <svg class="w-4 h-4 text-dj-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14M12 5l7 7-7 7" /></svg>
                        </span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="group hidden items-center gap-4 rounded-full bg-dj-dark py-1.5 pl-6 pr-2 text-sm font-medium text-white transition-all hover:bg-dj-green hover:shadow-md md:flex">
                        Login Admin
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white transition-transform group-hover:translate-x-1">
                            <svg class="w-4 h-4 text-dj-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14M12 5l7 7-7 7" /></svg>
                        </span>
                    </a>
                @endauth

                <button id="menu-toggle" type="button" aria-label="Buka menu navigasi" aria-controls="mobile-menu" aria-expanded="false" class="ml-2 inline-flex h-11 w-11 items-center justify-center rounded-full text-dj-dark transition-colors hover:bg-gray-100 lg:hidden">
                    <span class="relative block h-5 w-6" aria-hidden="true">
                        <span class="menu-bar absolute left-0 top-0 h-0.5 w-6 rounded-full bg-current transition-all duration-300"></span>
                        <span class="menu-bar absolute left-0 top-1/2 h-0.5 w-6 -translate-y-1/2 rounded-full bg-current transition-all duration-300"></span>
                        <span class="menu-bar absolute bottom-0 left-0 h-0.5 w-6 rounded-full bg-current transition-all duration-300"></span>
                    </span>
                </button>
            </div>
        </nav>
    </div>

    <div id="mobile-menu" class="pointer-events-none fixed inset-0 z-40 bg-white/95 pt-28 opacity-0 backdrop-blur-md transition-all duration-300 lg:hidden" aria-hidden="true">
        <div class="translate-y-4 px-6 py-8 transition-transform duration-300" data-mobile-panel>
            <nav class="space-y-1" aria-label="Navigasi mobile">
                <a href="#home" data-nav-link class="block py-1 text-xs text-gray-700 hover:text-dj-green">Utama</a>
                <a href="#tentang" data-nav-link class="block py-1 text-xs text-gray-700 hover:text-dj-green">Tentang Kami</a>
                <a href="#produk" data-nav-link class="block py-1 text-xs text-gray-700 hover:text-dj-green">Produk</a>
                <a href="#umkm" data-nav-link class="block py-1 text-xs text-gray-700 hover:text-dj-green">UMKM</a>
                <a href="#kontak" data-nav-link class="block py-1 text-xs text-gray-700 hover:text-dj-green">Kontak</a>
            </nav>
            <div class="mt-6 border-t border-gray-100 pt-6">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between rounded-full bg-dj-dark py-2 pl-6 pr-2 text-base font-semibold text-white transition-colors hover:bg-dj-green">Dashboard Admin <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white"><svg class="h-5 w-5 text-dj-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" /></svg></span></a>
                @else
                    <a href="{{ route('login') }}" class="flex items-center justify-between rounded-full bg-dj-dark py-2 pl-6 pr-2 text-base font-semibold text-white transition-colors hover:bg-dj-green">Login Admin <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white"><svg class="h-5 w-5 text-dj-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" /></svg></span></a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

  {{-- ==================== FOOTER (LIGHT EDITORIAL) ==================== --}}
<footer id="kontak" class="bg-[#FAFAF9] border-t border-gray-200">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">

        {{-- Main Footer --}}
        <div class="py-16 lg:py-20">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16">

                {{-- LEFT: Brand + WhatsApp CTA --}}
                <div class="lg:col-span-7">

                    {{-- Logo --}}
                    <a href="#home" class="inline-flex items-center gap-3 mb-8 group" aria-label="Djajan - Beranda">
                        <img src="{{ asset('logo_djajan.png') }}" alt="Djajan" class="h-9 w-auto object-contain transition-transform group-hover:scale-105">
                        <span class="font-wordmark text-2xl font-bold italic tracking-[-0.06em] text-[#0F623F]">Djajan</span>
                    </a>

                    {{-- Tagline --}}
                    <h3 class="font-display text-3xl lg:text-4xl font-bold leading-[1.1] text-[#111827] tracking-tight mb-4 max-w-md">
                        Dari desa, untuk semua.
                        <span class="text-[#0F623F] block mt-2">Mari dukung bersama.</span>
                    </h3>

                    <p class="text-base text-[#6B7280] leading-relaxed max-w-md mb-8">
                        Platform digital untuk mengenalkan UMKM dan produk lokal Desa Kreyongan Atas agar lebih mudah ditemukan.
                    </p>

                    {{-- WhatsApp CTA (Primary Action) --}}
                    <a href="https://wa.me/6281234567890?text=Halo%20Djajan,%20saya%20ingin%20bertanya%20tentang%20UMKM%20Desa%20Kreyongan%20Atas" 
                       target="_blank"
                       class="group inline-flex items-center gap-3 rounded-full bg-[#25D366] px-6 py-3.5 text-sm font-semibold text-white hover:bg-[#20BA5A] transition-all duration-300 shadow-lg shadow-[#25D366]/20">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        Chat via WhatsApp
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>

                    {{-- Contact Info (Secondary) --}}
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-3 mt-6 text-sm">
                        <a href="mailto:halo@djajan.id" class="inline-flex items-center gap-2 text-[#6B7280] hover:text-[#0F623F] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="hover:underline">halo@djajan.id</span>
                        </a>
                        <span class="text-[#E5E7EB]">•</span>
                        <span class="inline-flex items-center gap-2 text-[#6B7280]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Kreyongan Atas, Jawa Barat
                        </span>
                    </div>

                </div>

                {{-- RIGHT: Links + Social --}}
                <div class="lg:col-span-5 lg:pl-8">
                    <div class="grid grid-cols-2 gap-8">

                        {{-- Navigasi --}}
                        <div>
                            <h4 class="text-[11px] font-bold uppercase tracking-wider text-[#9B9B9B] mb-5">Navigasi</h4>
                            <ul class="space-y-3.5">
                                <li><a href="#home" class="text-sm font-medium text-[#6B7280] hover:text-[#0F623F] transition-colors">Utama</a></li>
                                <li><a href="#tentang" class="text-sm font-medium text-[#6B7280] hover:text-[#0F623F] transition-colors">Tentang</a></li>
                                <li><a href="#produk" class="text-sm font-medium text-[#6B7280] hover:text-[#0F623F] transition-colors">Produk</a></li>
                                <li><a href="#umkm" class="text-sm font-medium text-[#6B7280] hover:text-[#0F623F] transition-colors">UMKM</a></li>
                            </ul>
                        </div>

                        {{-- Informasi --}}
                        <div>
                            <h4 class="text-[11px] font-bold uppercase tracking-wider text-[#9B9B9B] mb-5">Informasi</h4>
                            <ul class="space-y-3.5">
                                <li><a href="#kontak" class="text-sm font-medium text-[#6B7280] hover:text-[#0F623F] transition-colors">Kontak</a></li>
                                @if(Route::has('login'))
                                    <li><a href="{{ route('login') }}" class="text-sm font-medium text-[#6B7280] hover:text-[#0F623F] transition-colors">Login Admin</a></li>
                                @endif
                            </ul>
                        </div>

                    </div>

                    {{-- Social Links --}}
                    <div class="mt-10 pt-8 border-t border-gray-200">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-[#9B9B9B] mb-4">Ikuti Kami</p>
                        <div class="flex items-center gap-3">
                            <a href="#" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-[#6B7280] hover:bg-[#E1306C] hover:border-[#E1306C] hover:text-white transition-all duration-300" aria-label="Instagram">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-[#6B7280] hover:bg-[#1877F2] hover:border-[#1877F2] hover:text-white transition-all duration-300" aria-label="Facebook">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="https://wa.me/6281234567890" target="_blank" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-[#6B7280] hover:bg-[#25D366] hover:border-[#25D366] hover:text-white transition-all duration-300" aria-label="WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="border-t border-gray-200 py-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">

                <p class="text-xs text-[#9B9B9B]">
                    &copy; {{ date('Y') }} Djajan. Semua hak dilindungi.
                </p>

                <p class="text-xs text-[#9B9B9B]">
                    Dibangun bersama <span class="font-medium text-[#6B7280]">Pemerintah Desa Kreyongan Atas</span>
                </p>

            </div>
        </div>

    </div>
</footer>

    <!-- ==================== SCRIPTS ==================== -->
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobilePanel = mobileMenu.querySelector('[data-mobile-panel]');
        const menuBars = menuToggle.querySelectorAll('.menu-bar');
        const navLinks = document.querySelectorAll('[data-nav-link]');

        const setActiveLink = (id) => {
            navLinks.forEach((link) => {
                const active = link.getAttribute('href') === `#${id}`;
                link.classList.toggle('is-active', active);
                link.classList.toggle('text-dj-green', active);
                link.classList.toggle('font-semibold', active);
                link.classList.toggle('text-gray-500', !active && link.closest('.lg\\:flex'));
                link.classList.toggle('text-gray-700', !active && !link.closest('.lg\\:flex'));
                link.setAttribute('aria-current', active ? 'page' : 'false');
            });
        };

        const setMenu = (open) => {
            mobileMenu.classList.toggle('pointer-events-none', !open);
            mobileMenu.classList.toggle('opacity-0', !open);
            mobilePanel.classList.toggle('translate-y-4', !open);
            menuToggle.setAttribute('aria-expanded', String(open));
            menuToggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
            document.body.style.overflow = open ? 'hidden' : '';
            menuBars[0].classList.toggle('top-1/2', open);
            menuBars[0].classList.toggle('-translate-y-1/2', open);
            menuBars[0].classList.toggle('rotate-45', open);
            menuBars[1].classList.toggle('opacity-0', open);
            menuBars[2].classList.toggle('bottom-auto', open);
            menuBars[2].classList.toggle('top-1/2', open);
            menuBars[2].classList.toggle('-translate-y-1/2', open);
            menuBars[2].classList.toggle('-rotate-45', open);
        };

        menuToggle.addEventListener('click', () => setMenu(menuToggle.getAttribute('aria-expanded') !== 'true'));

        navLinks.forEach((link) => link.addEventListener('click', () => {
            const id = link.getAttribute('href').slice(1);
            setActiveLink(id);
            setMenu(false);
        }));

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setMenu(false);
        });

        const sections = [...document.querySelectorAll('section[id]'), document.getElementById('kontak')].filter(Boolean);
        const updateActiveSection = () => {
            const navOffset = 150;
            const currentPosition = window.scrollY + navOffset;
            let activeSection = sections[0];

            sections.forEach((section) => {
                if (section.offsetTop <= currentPosition) activeSection = section;
            });

            setActiveLink(activeSection.id);
        };

        window.addEventListener('scroll', updateActiveSection, { passive: true });
        window.addEventListener('hashchange', updateActiveSection);
        updateActiveSection();
    </script>

    @stack('scripts')
</body>

</html>