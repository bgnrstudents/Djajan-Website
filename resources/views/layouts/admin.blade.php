<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title', 'Admin - Djajan')</title>
    <script>
        (() => {
            try {
                const theme = localStorage.getItem('djajan-admin-theme');
                document.documentElement.dataset.adminTheme = theme === 'dark' ? 'dark' : 'light';
            } catch (error) {
                document.documentElement.dataset.adminTheme = 'light';
                console.warn('Preferensi tema admin tidak dapat dibaca.', error);
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['"Plus Jakarta Sans"', 'sans-serif'],
                        'brand': ['"Bree Serif"', 'Georgia', 'serif'],
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
</head>

<body class="admin-shell bg-djajan-light antialiased text-djajan-neutral font-body">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

        {{-- Backdrop for Mobile Sidebar --}}
        <div
            id="sidebar-backdrop"
            class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden hidden transition-opacity"
            onclick="toggleSidebar(false)"></div>

        {{-- Sidebar Admin --}}
        <aside
            id="sidebar-container"
            class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col flex-shrink-0 transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0 h-screen">
            {{-- Brand Header --}}
            <div class="h-16 flex items-center justify-center px-6 border-b border-gray-200 bg-white flex-shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('logo_djajan.png') }}" alt="Logo Djajan" class="w-8 h-8 object-contain">
                    <span class="text-xl font-bold text-djajan-primary font-brand tracking-tight">Djajan</span>
                </a>
                <button onclick="toggleSidebar(false)" class="lg:hidden p-1.5 text-gray-400 hover:text-gray-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            {{-- Navigation Items --}}
            <nav class="flex-1 overflow-y-auto py-5 px-3">
                {{-- MENU Section --}}
                <div class="mb-6">
                    <p class="px-3 mb-2 text-[11px] font-semibold text-gray-400 uppercase tracking-wider font-heading">Menu Utama</p>
                    <ul class="space-y-1">
                        {{-- Dashboard --}}
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-djajan-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                                Dashboard
                            </a>
                        </li>

                        {{-- Verifikasi UMKM --}}
                        <li>
                            <a href="{{ route('admin.umkm.verifikasi') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.umkm.verifikasi') ? 'bg-djajan-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    <path d="M9 12l2 2 4-4"></path>
                                </svg>
                                Verifikasi Pendaftaran
                            </a>
                        </li>

                        {{-- Data UMKM --}}
                        <li>
                            <a href="{{ route('admin.umkm.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.umkm.index') || request()->routeIs('admin.umkm.show') ? 'bg-djajan-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                Data UMKM
                            </a>
                        </li>

                        {{-- Data Produk --}}
                        <li>
                            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-djajan-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m16.5 9.4-9-5.19"></path>
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                                Data Produk
                            </a>
                        </li>


                    </ul>
                </div>

                {{-- GENERAL Section --}}
                <div>
                    <ul class="space-y-1">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors text-left">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round">

                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>

                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            {{-- Bottom Card: Info Desa --}}
            <div class="p-3 flex-shrink-0">
                <div class="bg-djajan-primary rounded-xl p-4 text-white relative overflow-hidden shadow-sm">
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
                                <p class="text-sm font-semibold font-heading leading-tight">Desa Kreyongan Atas</p>
                                <p class="text-xs text-white/80 mt-0.5 leading-snug">Platform digital UMKM lokal</p>
                            </div>
                        </div>
                        <a href="{{ route('public.home') }}" target="_blank" class="block w-full py-2 bg-white text-djajan-primary text-xs font-semibold text-center rounded-lg hover:bg-white/90 transition-colors">
                            Lihat Website Utama
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        {{-- MAIN WRAPPER (Topbar + Content) --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            {{-- Global Topbar Header --}}
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-6 flex-shrink-0 z-10">
                <div class="flex items-center gap-4 flex-1">
                    {{-- Mobile Hamburger --}}
                    <button type="button" onclick="toggleSidebar(true)" class="lg:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Buka menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>

                    {{-- Search Bar Global --}}
                    <div class="hidden md:flex items-center gap-2 flex-1 max-w-md">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" placeholder="Cari UMKM, produk, atau pengguna..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors">
                        </div>
                    </div>
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-3">
                    <button
                        id="admin-theme-toggle"
                        type="button"
                        class="admin-theme-toggle rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100"
                        aria-label="Aktifkan mode gelap"
                        aria-pressed="false"
                        title="Aktifkan mode gelap">
                        <svg class="theme-icon-light" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path>
                        </svg>
                        <svg class="theme-icon-dark hidden" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6.5 6.5 0 0 0 8.268 8.268c.344-.215.825-.003.803.401"></path>
                        </svg>
                    </button>

                    <div class="relative" id="admin-notifications">
                        <button
                            id="notification-toggle"
                            type="button"
                            class="relative rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100"
                            title="Notifikasi pengajuan UMKM"
                            aria-label="Notifikasi pengajuan UMKM"
                            aria-expanded="false"
                            aria-controls="notification-dropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <span id="notification-count" class="absolute -right-0.5 -top-0.5 hidden min-w-4 rounded-full bg-red-600 px-1 text-center text-[10px] font-bold leading-4 text-white ring-2 ring-white"></span>
                        </button>

                        <section
                            id="notification-dropdown"
                            class="absolute right-0 top-full z-50 mt-2 hidden w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl"
                            aria-label="Pengajuan verifikasi UMKM">
                            <div class="flex items-start justify-between border-b border-gray-100 px-4 py-3">
                                <div>
                                    <h2 class="text-sm font-semibold text-gray-900">Pengajuan verifikasi</h2>
                                    <p id="notification-summary" class="mt-0.5 text-xs text-gray-500">Memuat pengajuan...</p>
                                </div>
                                <span class="mt-1 h-2 w-2 rounded-full bg-gray-300" id="notification-status" title="Status pembaruan"></span>
                            </div>
                            <div id="notification-list" class="max-h-80 overflow-y-auto" aria-live="polite">
                                <p class="px-4 py-6 text-center text-sm text-gray-500">Memuat pengajuan...</p>
                            </div>
                            <a href="{{ route('admin.umkm.verifikasi') }}" class="block border-t border-gray-100 px-4 py-3 text-center text-sm font-semibold text-djajan-primary hover:bg-gray-50">
                                Buka semua pengajuan
                            </a>
                        </section>
                    </div>

                    <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-900 leading-tight">Admin Djajan</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                        <div class="w-9 h-9 bg-djajan-primary text-white rounded-full flex items-center justify-center font-semibold text-sm ring-2 ring-white shadow-sm font-heading">
                            A
                        </div>
                    </div>
                </div>
            </header>

            {{-- Scrollable Main Area --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 bg-djajan-light">
                @yield('content')
            </main>
        </div>

    </div>

    <script>
        (() => {
            const button = document.getElementById('admin-theme-toggle');
            const lightIcon = button.querySelector('.theme-icon-light');
            const darkIcon = button.querySelector('.theme-icon-dark');

            function applyTheme(theme) {
                document.documentElement.dataset.adminTheme = theme;
                const isDark = theme === 'dark';
                lightIcon.classList.toggle('hidden', isDark);
                darkIcon.classList.toggle('hidden', !isDark);
                button.setAttribute('aria-pressed', String(isDark));
                button.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
                button.title = isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap';
                document.querySelector('meta[name="theme-color"]')?.setAttribute('content', isDark ? '#111a16' : '#ffffff');
            }

            applyTheme(document.documentElement.dataset.adminTheme === 'dark' ? 'dark' : 'light');
            button.addEventListener('click', () => {
                const theme = document.documentElement.dataset.adminTheme === 'dark' ? 'light' : 'dark';
                applyTheme(theme);
                try {
                    localStorage.setItem('djajan-admin-theme', theme);
                } catch (error) {
                    console.warn('Preferensi tema admin tidak dapat disimpan.', error);
                }
            });
        })();

        function toggleSidebar(show) {
            const sidebar = document.getElementById('sidebar-container');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (show) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        (() => {
            const toggle = document.getElementById('notification-toggle');
            const dropdown = document.getElementById('notification-dropdown');
            const list = document.getElementById('notification-list');
            const count = document.getElementById('notification-count');
            const summary = document.getElementById('notification-summary');
            const status = document.getElementById('notification-status');
            const endpoint = @json(route('admin.notifications.pending-verifications'));
            let requestController = null;

            function renderNotifications(data) {
                count.textContent = data.count > 99 ? '99+' : String(data.count);
                count.classList.toggle('hidden', data.count === 0);
                toggle.setAttribute('aria-label', `Notifikasi pengajuan UMKM: ${data.count} menunggu`);
                summary.textContent = data.count === 0
                    ? 'Tidak ada pengajuan yang menunggu.'
                    : `${data.count} pengajuan menunggu ditinjau.`;
                list.replaceChildren();

                if (data.applications.length === 0) {
                    const empty = document.createElement('p');
                    empty.className = 'px-4 py-8 text-center text-sm text-gray-500';
                    empty.textContent = 'Semua pengajuan sudah ditinjau.';
                    list.appendChild(empty);
                    return;
                }

                data.applications.forEach(application => {
                    const item = document.createElement('div');
                    item.className = 'flex items-start gap-3 border-b border-gray-100 px-4 py-3 last:border-b-0';

                    const indicator = document.createElement('span');
                    indicator.className = 'mt-1.5 h-2 w-2 shrink-0 rounded-full bg-amber-500';

                    const details = document.createElement('div');
                    details.className = 'min-w-0 flex-1';
                    const name = document.createElement('p');
                    name.className = 'truncate text-sm font-semibold text-gray-800';
                    name.textContent = application.nama_usaha;
                    const date = document.createElement('p');
                    date.className = 'mt-0.5 text-xs text-gray-500';
                    date.textContent = application.created_at
                        ? `Terdaftar ${application.created_at}`
                        : 'Pengajuan baru';
                    const action = document.createElement('a');
                    action.href = application.url;
                    action.className = 'mt-2 inline-flex items-center gap-1 text-xs font-semibold text-djajan-primary hover:underline';
                    action.textContent = 'Tinjau dan verifikasi';
                    details.append(name, date, action);
                    item.append(indicator, details);
                    list.appendChild(item);
                });
            }

            async function refreshNotifications() {
                if (document.hidden) return;
                requestController?.abort();
                requestController = new AbortController();

                try {
                    const response = await fetch(endpoint, {
                        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                        signal: requestController.signal
                    });
                    if (!response.ok) throw new Error(`Notifikasi gagal dimuat (${response.status})`);
                    renderNotifications(await response.json());
                    status.className = 'mt-1 h-2 w-2 rounded-full bg-green-500';
                    status.title = 'Diperbarui otomatis';
                } catch (error) {
                    if (error.name === 'AbortError') return;
                    status.className = 'mt-1 h-2 w-2 rounded-full bg-red-500';
                    status.title = 'Pembaruan notifikasi gagal';
                    if (list.childElementCount === 0) {
                        const message = document.createElement('p');
                        message.className = 'px-4 py-6 text-center text-sm text-red-600';
                        message.textContent = 'Notifikasi belum dapat dimuat. Coba lagi beberapa saat.';
                        list.appendChild(message);
                    }
                    console.error('Gagal memuat notifikasi pengajuan UMKM:', error);
                }
            }

            toggle.addEventListener('click', () => {
                const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', String(!isOpen));
                dropdown.classList.toggle('hidden', isOpen);
                if (!isOpen) refreshNotifications();
            });

            document.addEventListener('click', event => {
                if (!event.target.closest('#admin-notifications')) {
                    dropdown.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    dropdown.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.focus();
                }
            });

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) refreshNotifications();
            });

            refreshNotifications();
            window.setInterval(refreshNotifications, 30000);
        })();
    </script>
    @stack('scripts')
</body>

</html>