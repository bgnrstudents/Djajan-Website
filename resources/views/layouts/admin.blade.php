<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Djajan')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['"Plus Jakarta Sans"', 'sans-serif'],
                        'body': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'djajan-primary': '#0F623F',
                        'djajan-secondary': '#4CAF50',
                        'djajan-tertiary': '#F59E0B',
                        'djajan-neutral': '#111827',
                        'djajan-light': '#F8FAFC',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-djajan-light antialiased">


    {{-- WRAPPER FLEX: Sidebar + Main Content --}}
    <div class="flex h-screen overflow-hidden">


        {{-- Sidebar Admin Djajan - Donezo Style --}}
        <aside class="w-64 bg-gray-50 border-r border-gray-200 flex flex-col flex-shrink-0 hidden lg:flex h-screen">


            {{-- Brand --}}
            <div class="h-16 flex items-center px-6 border-b border-gray-200 bg-white">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-djajan-primary rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-djajan-neutral font-heading tracking-tight">Djajan</span>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-5 px-3">

                {{-- MENU Section --}}
                <div class="mb-6">
                    <p class="px-3 mb-2 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Menu</p>
                    <ul class="space-y-1">

                        {{-- Dashboard (Active) - Solid green dengan accent bar --}}
                        <li>
                            <a href="#" class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg bg-djajan-primary text-white font-medium text-sm group">
                                {{-- Accent bar di kiri --}}
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-r-full"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                                Dashboard
                            </a>
                        </li>

                        {{-- Data UMKM (Inactive) --}}
                        <li>
                            <a href="{{ route('admin.umkm.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-white hover:text-gray-900 transition-colors text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                Data UMKM
                            </a>
                        </li>

                        {{-- Data Produk --}}
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-white hover:text-gray-900 transition-colors text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                                Data Produk
                            </a>
                        </li>

                        {{-- Kategori --}}
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-white hover:text-gray-900 transition-colors text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                </svg>
                                Kategori
                            </a>
                        </li>

                        {{-- Pengguna --}}
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-white hover:text-gray-900 transition-colors text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                Pengguna
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- GENERAL Section --}}
                <div>
                    <p class="px-3 mb-2 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">General</p>
                    <ul class="space-y-1">

                        {{-- Pengaturan --}}
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-white hover:text-gray-900 transition-colors text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                Pengaturan
                            </a>
                        </li>

                        {{-- Keluar --}}
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            {{-- Bottom Card: Info Desa - Donezo Style --}}
            <div class="p-3 flex-shrink-0">
                <div class="bg-djajan-primary rounded-xl p-4 text-white relative overflow-hidden">
                    {{-- Decorative circles pattern --}}
                    <div class="absolute top-0 right-0 w-20 h-20 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                    <div class="relative z-10">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold leading-tight">Desa Kreyongan Atas</p>
                                <p class="text-xs text-white/70 mt-0.5 leading-snug">Platform digital UMKM lokal</p>
                            </div>
                        </div>
                        <button class="w-full py-2 bg-white text-djajan-primary text-xs font-semibold rounded-lg hover:bg-white/90 transition-colors">
                            Lihat Profil Desa
                        </button>
                    </div>
                </div>
            </div>

        </aside>

        {{-- MAIN WRAPPER (Topbar + Content) --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            @yield('content')
        </div>

    </div>
    @stack('scripts')
</body>

</html>