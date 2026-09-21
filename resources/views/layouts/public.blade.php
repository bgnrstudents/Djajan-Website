<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Djajan')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white text-gray-800">

    <!-- ==================== NAVBAR ==================== -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex h-[72px] items-center justify-between">

                <!-- Logo -->
                <a href="#" class="flex items-center gap-2.5 group">
                    <div class="h-9 w-9 rounded-lg bg-[#0F623F] flex items-center justify-center transition-transform duration-300 group-hover:scale-105">
                        <span class="font-display font-bold text-white text-sm">D</span>
                    </div>
                    <span class="font-display font-bold text-white text-lg">Djajan</span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center gap-1">
                    <a href="#home" class="nav-link px-4 py-2 text-sm font-medium text-white/70 rounded-lg hover:text-white hover:bg-white/10 transition-all duration-200">
                        Home
                    </a>
                    <a href="#tentang" class="nav-link px-4 py-2 text-sm font-medium text-white/70 rounded-lg hover:text-white hover:bg-white/10 transition-all duration-200">
                        Tentang
                    </a>
                    <a href="#produk" class="nav-link px-4 py-2 text-sm font-medium text-white/70 rounded-lg hover:text-white hover:bg-white/10 transition-all duration-200">
                        Produk
                    </a>
                    <a href="#umkm" class="nav-link px-4 py-2 text-sm font-medium text-white/70 rounded-lg hover:text-white hover:bg-white/10 transition-all duration-200">
                        UMKM
                    </a>

                </div>

                <!-- CTA + Mobile Toggle -->
                <div class="flex items-center">
                    <a href="#umkm" class="hidden lg:inline-flex items-center gap-1.5 rounded-full bg-[#F59E0B] px-7 py-2.5 text-sm font-semibold text-[#111827] hover:bg-[#FBBF24] transition-all duration-200 hover:shadow-lg hover:shadow-[#F59E0B]/20">
                        Login
                    </a>

                    <!-- Mobile Menu Button -->
                    <button id="menu-toggle" class="lg:hidden h-10 w-10 flex items-center justify-center rounded-lg border border-white/20 text-white hover:border-white/40 hover:bg-white/10 transition-all">
                        <svg id="menu-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="lg:hidden hidden bg-[#111827]/95 backdrop-blur-lg border-t border-white/10">
            <div class="px-6 py-5 space-y-1">
                <a href="#home" class="block px-4 py-3 text-sm font-medium text-white rounded-lg hover:bg-white/10 transition-colors">
                    Home
                </a>
                <a href="#tentang" class="block px-4 py-3 text-sm font-medium text-white/70 rounded-lg hover:bg-white/10 hover:text-white transition-colors">
                    Tentang
                </a>
                <a href="#produk" class="block px-4 py-3 text-sm font-medium text-white/70 rounded-lg hover:bg-white/10 hover:text-white transition-colors">
                    Produk
                </a>
                <a href="#umkm" class="block px-4 py-3 text-sm font-medium text-white/70 rounded-lg hover:bg-white/10 hover:text-white transition-colors">
                    UMKM
                </a>

                <div class="pt-3 border-t border-white/10 mt-3">
                    <a href="#umkm" class="block w-full text-center rounded-full bg-[#F59E0B] px-5 py-3 text-sm font-semibold text-[#111827] hover:bg-[#FBBF24] transition-colors">
                        Jelajahi UMKM
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <style>
        /* Navbar scrolled state */
        #navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        #navbar.scrolled .font-display {
            color: #111827;
        }

        #navbar.scrolled .nav-link {
            color: #374151;
        }

        #navbar.scrolled .nav-link:hover,
        #navbar.scrolled .nav-link.active {
            color: #0F623F;
            background: rgba(15, 98, 63, 0.06);
        }

        #navbar.scrolled #menu-toggle {
            border-color: #E5E7EB;
            color: #111827;
        }

        #navbar.scrolled #menu-toggle:hover {
            border-color: #0F623F;
            color: #0F623F;
            background: rgba(15, 98, 63, 0.04);
        }

        /* Active link state (transparent navbar) */
        .nav-link.active {
            color: #F59E0B;
            background: rgba(245, 158, 11, 0.1);
        }

        /* Mobile menu scrolled state */
        #navbar.scrolled #mobile-menu {
            background: rgba(255, 255, 255, 0.98);
            border-top-color: #E5E7EB;
        }

        #navbar.scrolled #mobile-menu a:not(:last-child) {
            color: #111827;
        }

        #navbar.scrolled #mobile-menu a:not(:last-child):hover {
            background: #F9FAFB;
        }
    </style>

    <script>
        const navbar = document.getElementById('navbar');
        const navLinks = document.querySelectorAll('.nav-link');
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');

        // Scroll effect
        window.addEventListener('scroll', () => {
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            // Active link detection
            const sections = document.querySelectorAll('section[id]');
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                if (window.scrollY >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });

        // Mobile menu toggle
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            const isOpen = !mobileMenu.classList.contains('hidden');
            menuIcon.innerHTML = isOpen ?
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>' :
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
        });

        // Close mobile menu on link click
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
            });
        });
    </script>


    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- ==================== FOOTER ==================== -->
    <footer class="bg-[#FAFAF9] relative">

        <!-- Top accent line -->
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-[#0F623F]/30 to-transparent"></div>

        <div class="mx-auto max-w-7xl px-6 lg:px-8">

            <!-- Main Footer -->
            <div class="py-16 lg:py-20">

                <div class="grid lg:grid-cols-12 gap-12 lg:gap-16">

                    <!-- Left: Brand Statement (7 cols) -->
                    <div class="lg:col-span-7">

                        <!-- Logo -->
                        <a href="#" class="inline-flex items-center gap-2.5 group mb-8">
                            <div class="h-9 w-9 rounded-lg bg-[#0F623F] flex items-center justify-center transition-transform duration-300 group-hover:scale-105">
                                <span class="font-display font-bold text-white text-sm">D</span>
                            </div>
                            <span class="font-display font-bold text-[#111827] text-lg">Djajan</span>
                        </a>

                        <!-- Large closing statement -->
                        <h3 class="font-display text-3xl lg:text-4xl font-bold leading-tight text-[#111827] mb-4 max-w-lg">
                            Dari desa, untuk semua.
                            <br>
                            <span class="text-[#0F623F]">Mari dukung bersama.</span>
                        </h3>

                        <p class="text-base text-[#6B7280] leading-relaxed max-w-md mb-8">
                            Platform digital untuk mengenalkan UMKM dan produk lokal Desa Kreyongan Atas agar lebih mudah ditemukan.
                        </p>

                        <!-- Contact row (horizontal, compact) -->
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm">
                            <a href="mailto:halo@djajan.id" class="inline-flex items-center gap-2 text-[#374151] hover:text-[#0F623F] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                halo@djajan.id
                            </a>
                            <a href="#" class="inline-flex items-center gap-2 text-[#374151] hover:text-[#0F623F] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Kreyongan Atas, Jabar
                            </a>
                        </div>

                    </div>

                    <!-- Right: Links (5 cols) -->
                    <div class="lg:col-span-5">
                        <div class="grid grid-cols-2 gap-8">

                            <!-- Column 1 -->
                            <div>
                                <h4 class="font-display font-semibold text-[#111827] text-sm mb-5">Navigasi</h4>
                                <ul class="space-y-3">
                                    <li>
                                        <a href="#home" class="text-sm text-[#6B7280] hover:text-[#0F623F] transition-colors duration-200">Home</a>
                                    </li>
                                    <li>
                                        <a href="#tentang" class="text-sm text-[#6B7280] hover:text-[#0F623F] transition-colors duration-200">Tentang</a>
                                    </li>
                                    <li>
                                        <a href="#produk" class="text-sm text-[#6B7280] hover:text-[#0F623F] transition-colors duration-200">Produk</a>
                                    </li>
                                    <li>
                                        <a href="#umkm" class="text-sm text-[#6B7280] hover:text-[#0F623F] transition-colors duration-200">UMKM</a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Column 2 -->
                            <div>
                                <h4 class="font-display font-semibold text-[#111827] text-sm mb-5">Informasi</h4>
                                <ul class="space-y-3">
                                    <li>
                                        <a href="#" class="text-sm text-[#6B7280] hover:text-[#0F623F] transition-colors duration-200">FAQ</a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-sm text-[#6B7280] hover:text-[#0F623F] transition-colors duration-200">Kebijakan Privasi</a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-sm text-[#6B7280] hover:text-[#0F623F] transition-colors duration-200">Syarat & Ketentuan</a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-sm text-[#6B7280] hover:text-[#0F623F] transition-colors duration-200">Kontak</a>
                                    </li>
                                </ul>
                            </div>

                        </div>

                        <!-- Social Links (bottom-right) -->
                        <div class="mt-10 pt-8 border-t border-gray-200">
                            <p class="text-xs text-[#6B7280] mb-4 uppercase tracking-wider font-medium">Ikuti Kami</p>
                            <div class="flex items-center gap-2">
                                <a href="#" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-[#6B7280] hover:bg-[#0F623F] hover:border-[#0F623F] hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                    </svg>
                                </a>
                                <a href="#" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-[#6B7280] hover:bg-[#0F623F] hover:border-[#0F623F] hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                                    </svg>
                                </a>
                                <a href="#" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-[#6B7280] hover:bg-[#0F623F] hover:border-[#0F623F] hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                                    </svg>
                                </a>
                                <a href="#" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-[#6B7280] hover:bg-[#25D366] hover:border-[#25D366] hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-200 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-3">

                    <p class="text-xs text-[#6B7280]">
                        &copy; {{ date('Y') }} Djajan. Semua hak dilindungi.
                    </p>

                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-[#6B7280]">Dibuat dengan</span>
                        <svg class="w-3.5 h-3.5 text-[#F59E0B]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                        </svg>
                        <span class="text-xs text-[#6B7280]">untuk Desa Kreyongan Atas</span>
                    </div>

                </div>
            </div>

        </div>

    </footer>
</body>

</html>