<!DOCTYPE html>
<html lang="id">

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
    
    <!-- GSAP Flag -->
    <script>document.documentElement.classList.add('js', 'gsap-on');</script>
    
    
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
        /* Prevent CSS smooth scroll from fighting GSAP ScrollToPlugin */
        html {
            scroll-behavior: auto !important;
        }

        /* Smooth transitions untuk carousel */
        .slide-bg {
            transition: none;
            transform: none;
        }
        .slide-bg.active {
            opacity: 1 !important;
            transform: none !important;
        }
        
        .public-navbar,
        .public-navbar nav,
        .nav-link,
        .mobile-nav-link {
            transition: background-color 280ms ease, border-color 280ms ease, box-shadow 280ms ease, backdrop-filter 280ms ease, color 200ms ease, transform 280ms ease;
        }

        .public-navbar {
            width: max-content;
            max-width: calc(100% - 2rem);
        }

        .public-navbar nav {
            border: 1px solid transparent;
            background: transparent;
            box-shadow: none;
            backdrop-filter: blur(0);
            -webkit-backdrop-filter: blur(0);
            justify-content: center;
            padding: 0.625rem 1.25rem;
        }

        .public-navbar.is-scrolled nav {
            border-color: rgba(229, 231, 235, 0.62);
            background: rgba(255, 255, 255, 0.72);
            box-shadow: 0 8px 28px rgba(2, 44, 34, 0.1);
            backdrop-filter: blur(20px) saturate(150%);
            -webkit-backdrop-filter: blur(20px) saturate(150%);
        }

        .desktop-nav-links {
            display: flex;
            position: static;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            right: 0.5rem;
            bottom: 0.25rem;
            left: 0.5rem;
            height: 2px;
            border-radius: 9999px;
            background: #86efac;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 200ms ease;
        }
        .nav-link { color: rgba(255, 255, 255, 0.9); }
        .nav-link:hover, .nav-link.is-active { color: #fff; }
        .nav-link.is-active::after { transform: scaleX(1); }
        .public-navbar.is-scrolled .nav-link { color: #6b7280; }
        .public-navbar.is-scrolled .nav-link:hover,
        .public-navbar.is-scrolled .nav-link.is-active { color: #0f623f; }
        .public-navbar.is-scrolled .nav-link::after { background: #0f623f; }

        .mobile-header, .mobile-sidebar, .mobile-sidebar-overlay { display: none; }

        @media (max-width: 899px) {
            .public-navbar { display: none; }
            .mobile-header {
                position: fixed;
                z-index: 50;
                top: 0;
                right: 0;
                left: 0;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                padding: calc(0.65rem + env(safe-area-inset-top)) 1rem 0.65rem;
                border-bottom: 1px solid transparent;
                transition: background-color 240ms ease, border-color 240ms ease, box-shadow 240ms ease;
            }
            .mobile-header.is-scrolled {
                border-color: rgba(229, 231, 235, 0.8);
                background: rgba(255, 255, 255, 0.9);
                box-shadow: 0 6px 24px rgba(2, 44, 34, 0.08);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
            }
            .mobile-menu-toggle {
                display: inline-flex;
                width: 2.75rem;
                height: 2.75rem;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(255, 255, 255, 0.38);
                border-radius: 9999px;
                background: rgba(255, 255, 255, 0.14);
                color: #fff;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }
            .mobile-header.is-scrolled .mobile-menu-toggle { border-color: #e5e7eb; background: rgba(255,255,255,.8); color: #064e3b; }
            .mobile-menu-toggle svg { width: 1.4rem; height: 1.4rem; }
            .mobile-sidebar-overlay {
                position: fixed;
                z-index: 60;
                inset: 0;
                display: block;
                background: rgba(2, 44, 34, 0.38);
                opacity: 0;
                pointer-events: none;
                transition: opacity 260ms ease, visibility 260ms;
                visibility: hidden;
            }
            .mobile-sidebar-overlay.is-open { opacity: 1; pointer-events: auto; visibility: visible; }
            .mobile-sidebar {
                position: fixed;
                z-index: 61;
                top: 0;
                right: 0;
                display: flex;
                width: min(84vw, 360px);
                height: 100vh;
                height: 100dvh;
                flex-direction: column;
                border-radius: 1.5rem 0 0 1.5rem;
                background: #fff;
                padding: calc(1.25rem + env(safe-area-inset-top)) 1.25rem calc(1.25rem + env(safe-area-inset-bottom));
                box-shadow: -16px 0 48px rgba(2, 44, 34, 0.18);
                transform: translateX(105%);
                transition: transform 300ms cubic-bezier(0.22, 1, 0.36, 1), visibility 300ms;
                visibility: hidden;
            }
            .mobile-sidebar.is-open { transform: translateX(0); visibility: visible; }
            .mobile-sidebar-top { display: flex; align-items: center; justify-content: flex-end; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb; }
            .mobile-sidebar-close { display: inline-flex; width: 2.75rem; height: 2.75rem; align-items: center; justify-content: center; border-radius: 9999px; background: #f3f4f6; color: #064e3b; }
            .mobile-sidebar-close svg { width: 1.25rem; height: 1.25rem; }
            .mobile-sidebar-links { display: grid; gap: 0.5rem; padding-top: 1.5rem; }
            .mobile-sidebar-link { display: flex; min-height: 3.5rem; align-items: center; gap: 0.9rem; border-radius: 0.9rem; padding: 0 1rem; color: #4b5563; font-size: 0.95rem; font-weight: 600; transition: color 180ms ease, background-color 180ms ease, transform 180ms ease; }
            .mobile-sidebar-link svg { width: 1.25rem; height: 1.25rem; flex: 0 0 auto; }
            .mobile-sidebar-link:hover, .mobile-sidebar-link.is-active { background: #e9f4ed; color: #0f623f; }
            .mobile-sidebar-link:active { transform: scale(0.98); }
            body.sidebar-open { overflow: hidden; }
        }

        @media (prefers-reduced-motion: reduce) {
            .public-navbar, .public-navbar nav, .nav-link, .mobile-header, .mobile-sidebar, .mobile-sidebar-overlay { transition-duration: 0.01ms !important; }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #022c22; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #064e3b; }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased overflow-x-hidden">

    <!-- ==================== DESKTOP NAVBAR ==================== -->
    <div id="public-navbar" class="public-navbar fixed left-1/2 top-6 z-50 -translate-x-1/2">
        <nav class="relative flex items-center rounded-full py-2" aria-label="Navigasi utama">
            <div class="desktop-nav-links">
                <a href="#home" data-nav-link class="nav-link relative px-2 py-2 text-xs font-medium sm:px-3 sm:text-sm">Beranda</a>
                <a href="#tentang" data-nav-link class="nav-link relative px-2 py-2 text-xs font-medium sm:px-3 sm:text-sm">Tentang Kami</a>
                <a href="#produk" data-nav-link class="nav-link relative px-2 py-2 text-xs font-medium sm:px-3 sm:text-sm">Produk</a>
                <a href="#umkm" data-nav-link class="nav-link relative px-2 py-2 text-xs font-medium sm:px-3 sm:text-sm">Maps</a>
                <a href="#kontak" data-nav-link class="nav-link relative px-2 py-2 text-xs font-medium sm:px-3 sm:text-sm">Kontak</a>
            </div>
        </nav>
    </div>

    <header id="mobile-header" class="mobile-header">
        <button id="mobile-menu-toggle" class="mobile-menu-toggle" type="button" aria-label="Buka navigasi" aria-controls="mobile-sidebar" aria-expanded="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </header>

    <button id="mobile-sidebar-overlay" class="mobile-sidebar-overlay" type="button" aria-label="Tutup navigasi" tabindex="-1"></button>
    <aside id="mobile-sidebar" class="mobile-sidebar" role="dialog" aria-modal="true" aria-label="Navigasi mobile" aria-hidden="true" inert>
        <div class="mobile-sidebar-top">
            <button id="mobile-menu-close" class="mobile-sidebar-close" type="button" aria-label="Tutup navigasi">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="mobile-sidebar-links" aria-label="Navigasi halaman">
            <a href="#home" data-nav-link class="mobile-sidebar-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 10 9-7 9 7"/><path d="M5 9v11h14V9M9 20v-6h6v6"/></svg>Beranda</a>
            <a href="#tentang" data-nav-link class="mobile-sidebar-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>Tentang Kami</a>
            <a href="#produk" data-nav-link class="mobile-sidebar-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 7 9-4 9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7M12 11v10"/></svg>Produk</a>
            <a href="#umkm" data-nav-link class="mobile-sidebar-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10h18l-2-6H5l-2 6Z"/><path d="M5 10v10h14V10M9 20v-6h6v6M3 10c0 2 3 2 3 0 0 2 3 2 3 0 0 2 3 2 3 0 0 2 3 2 3 0 0 2 3 2 3 0"/></svg>UMKM</a>
            <a href="#kontak" data-nav-link class="mobile-sidebar-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-3 2a15 15 0 0 0 5 5l2-3 5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1-2-2Z"/></svg>Kontak</a>
        </nav>
    </aside>

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
                                <li><a href="#home" class="text-sm font-medium text-[#6B7280] hover:text-[#0F623F] transition-colors">Beranda</a></li>
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
        const publicNavbar = document.getElementById('public-navbar');
        const mobileHeader = document.getElementById('mobile-header');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');
        const navLinks = document.querySelectorAll('[data-nav-link]');
        let lockedScrollY = 0;

        const setMobileMenu = (open) => {
            const isOpen = mobileSidebar.classList.contains('is-open');
            if (open === isOpen) return;

            mobileSidebar.classList.toggle('is-open', open);
            mobileSidebarOverlay.classList.toggle('is-open', open);
            mobileSidebar.setAttribute('aria-hidden', String(!open));
            mobileSidebar.inert = !open;
            mobileMenuToggle.setAttribute('aria-expanded', String(open));
            mobileMenuToggle.setAttribute('aria-label', open ? 'Tutup navigasi' : 'Buka navigasi');

            if (open) {
                lockedScrollY = window.scrollY;
                document.body.classList.add('sidebar-open');
                document.body.style.top = `-${lockedScrollY}px`;
                mobileMenuClose.focus();
            } else {
                document.body.classList.remove('sidebar-open');
                document.body.style.top = '';
                window.scrollTo(0, lockedScrollY);
                mobileMenuToggle.focus();
            }
        };

        mobileMenuToggle.addEventListener('click', () => setMobileMenu(true));
        mobileMenuClose.addEventListener('click', () => setMobileMenu(false));
        mobileSidebarOverlay.addEventListener('click', () => setMobileMenu(false));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setMobileMenu(false);
        });

        const setActiveLink = (id) => {
            navLinks.forEach((link) => {
                const active = link.getAttribute('href') === `#${id}`;
                link.classList.toggle('is-active', active);
                if (active) {
                    link.setAttribute('aria-current', 'page');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        };

        let isNavClicking = false;
        let navClickTimer = null;

        navLinks.forEach((link) => link.addEventListener('click', (event) => {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
                const id = href.slice(1);
                const target = document.getElementById(id);
                if (!target) return;
                event.preventDefault();
                setActiveLink(id);
                isNavClicking = true;
                clearTimeout(navClickTimer);
                navClickTimer = setTimeout(() => { isNavClicking = false; }, 1400);
                history.pushState(null, '', href);
                setMobileMenu(false);
                const offset = window.matchMedia('(max-width: 899px)').matches ? 84 : 104;
                window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - offset, behavior: 'smooth' });
            }
        }));

        const sections = [...document.querySelectorAll('section[id]'), document.getElementById('kontak')].filter(Boolean);
        let scrollTicking = false;
        const updateActiveSection = () => {
            if (isNavClicking) return;
            const navOffset = 160;
            const currentPosition = window.scrollY + navOffset;
            let activeSection = sections[0];

            for (let i = 0; i < sections.length; i++) {
                if (sections[i].offsetTop <= currentPosition) {
                    activeSection = sections[i];
                }
            }

            if (activeSection) {
                setActiveLink(activeSection.id);
            }
        };

        const updateNavbarScrollState = () => {
            const scrolled = window.scrollY > 24;
            publicNavbar.classList.toggle('is-scrolled', scrolled);
            mobileHeader.classList.toggle('is-scrolled', scrolled);
        };

        window.addEventListener('scroll', () => {
            if (!scrollTicking) {
                window.requestAnimationFrame(() => {
                    updateActiveSection();
                    updateNavbarScrollState();
                    scrollTicking = false;
                });
                scrollTicking = true;
            }
        }, { passive: true });
        window.addEventListener('hashchange', updateActiveSection);
        window.addEventListener('popstate', updateActiveSection);
        updateNavbarScrollState();
        updateActiveSection();
    </script>

    <!-- GSAP Core & Plugins (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>

    @stack('scripts')
</body>

</html>