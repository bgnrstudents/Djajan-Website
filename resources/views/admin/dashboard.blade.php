@extends('layouts.admin')

@section('title', 'Dashboard - Djajan')

@section('content')

    <!-- TOPBAR -->
    <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-6 flex-shrink-0">
        <div class="flex items-center gap-4 flex-1">
            <button class="lg:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            
            <!-- Search Bar -->
            <div class="hidden md:flex items-center gap-2 flex-1 max-w-md">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" placeholder="Cari UMKM, produk, atau pengguna..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary">
                </div>
                <kbd class="hidden lg:inline-flex items-center gap-1 px-2 py-1 bg-gray-100 border border-gray-200 rounded text-xs text-gray-500 font-medium">
                    <span class="text-[10px]">⌘</span>K
                </kbd>
            </div>
        </div>

        <!-- Right Actions -->
        <div class="flex items-center gap-3">
            <button class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors" title="Notifikasi">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-djajan-tertiary rounded-full ring-2 ring-white"></span>
            </button>
            
            <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
            
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-gray-900">Admin Djajan</p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>
                <div class="w-9 h-9 bg-djajan-primary text-white rounded-full flex items-center justify-center font-semibold text-sm ring-2 ring-white shadow-sm">
                    A
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">

        <!-- PAGE HEADER -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Pantau dan kelola aktivitas utama Djajan dari satu tempat.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2.5 bg-djajan-primary text-white text-sm font-medium rounded-lg hover:bg-djajan-primary/90 transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah UMKM
                    </a>
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Export Data
                    </a>
                </div>
            </div>
        </div>

        <!-- SUMMARY STATISTICS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            <!-- Stat 1: Total UMKM (SOLID GREEN) -->
            <div class="bg-djajan-primary rounded-xl p-5 text-white relative overflow-hidden shadow-sm">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-3">
                        <p class="text-sm font-medium text-white/80">Total UMKM</p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/60"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                    </div>
                    <p class="text-4xl font-bold font-heading tracking-tight mb-2">12</p>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-white/60"></span>
                        <p class="text-xs text-white/70">UMKM terdaftar aktif</p>
                    </div>
                </div>
            </div>

            <!-- Stat 2: Total Produk -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-sm font-medium text-gray-600">Total Produk</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </div>
                <p class="text-3xl font-bold text-djajan-neutral font-heading tracking-tight mb-2">35</p>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-djajan-secondary"></span>
                    <p class="text-xs text-gray-500">Produk dari semua UMKM</p>
                </div>
            </div>

            <!-- Stat 3: Total Pengguna -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-sm font-medium text-gray-600">Total Pengguna</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </div>
                <p class="text-3xl font-bold text-djajan-neutral font-heading tracking-tight mb-2">48</p>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <p class="text-xs text-gray-500">Pengguna terdaftar</p>
                </div>
            </div>

            <!-- Stat 4: Pengajuan Baru -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-sm font-medium text-gray-600">Pengajuan Baru</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </div>
                <p class="text-3xl font-bold text-djajan-neutral font-heading tracking-tight mb-2">4</p>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-djajan-tertiary"></span>
                    <p class="text-xs text-gray-500">Menunggu tinjauan</p>
                </div>
            </div>
        </div>

        <!-- MAIN GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT COLUMN (2 cols) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Aktivitas UMKM Chart -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-base font-semibold text-djajan-neutral font-heading">Aktivitas UMKM</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Pengajuan 7 hari terakhir</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="px-3 py-1.5 text-xs font-medium text-djajan-primary bg-djajan-primary/10 rounded-lg">Minggu</button>
                            <button class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Bulan</button>
                        </div>
                    </div>

                    <!-- Simple Bar Chart -->
                    <div class="flex items-end justify-between gap-2 h-32">
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-djajan-primary/20 rounded-t-md" style="height: 40%"></div>
                            <span class="text-xs text-gray-500">Sen</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-djajan-primary/40 rounded-t-md" style="height: 60%"></div>
                            <span class="text-xs text-gray-500">Sel</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-djajan-primary/60 rounded-t-md" style="height: 80%"></div>
                            <span class="text-xs text-gray-500">Rab</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-djajan-primary rounded-t-md" style="height: 100%"></div>
                            <span class="text-xs text-gray-500 font-medium">Kam</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-djajan-primary/50 rounded-t-md" style="height: 70%"></div>
                            <span class="text-xs text-gray-500">Jum</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-djajan-primary/30 rounded-t-md" style="height: 50%"></div>
                            <span class="text-xs text-gray-500">Sab</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-djajan-primary/20 rounded-t-md" style="height: 30%"></div>
                            <span class="text-xs text-gray-500">Min</span>
                        </div>
                    </div>
                </div>

                <!-- Pengajuan UMKM Terbaru - TABLE VERSION -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-djajan-neutral font-heading">Pengajuan UMKM Terbaru</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Daftar pengajuan yang masuk beberapa hari terakhir</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            3 Menunggu
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">UMKM</th>
                                    <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemilik</th>
                                    <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-right py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <!-- Row 1 -->
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-djajan-primary/10 text-djajan-primary flex items-center justify-center text-xs font-bold flex-shrink-0 font-heading">
                                                DK
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">Dapur Kreyongan</p>
                                                <p class="text-xs text-gray-400">Makanan & Minuman</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-gray-600">Budi Santoso</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">Makanan</span>
                                    </td>
                                    <td class="py-4 px-5 text-gray-500">18 Sep 2026</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right">
                                        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-djajan-primary hover:bg-djajan-primary/5 rounded-md transition-colors" title="Lihat Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                <!-- Row 2 -->
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-djajan-secondary/10 text-djajan-secondary flex items-center justify-center text-xs font-bold flex-shrink-0 font-heading">
                                                KL
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">Karya Lokal</p>
                                                <p class="text-xs text-gray-400">Kerajinan Tangan</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-gray-600">Siti Aminah</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">Kerajinan</span>
                                    </td>
                                    <td class="py-4 px-5 text-gray-500">17 Sep 2026</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right">
                                        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-djajan-primary hover:bg-djajan-primary/5 rounded-md transition-colors" title="Lihat Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                <!-- Row 3 -->
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-djajan-tertiary/10 text-djajan-tertiary flex items-center justify-center text-xs font-bold flex-shrink-0 font-heading">
                                                SK
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">Segar Kreyongan</p>
                                                <p class="text-xs text-gray-400">Minuman Segar</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-gray-600">Andi</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">Minuman</span>
                                    </td>
                                    <td class="py-4 px-5 text-gray-500">16 Sep 2026</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Disetujui
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right">
                                        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-djajan-primary hover:bg-djajan-primary/5 rounded-md transition-colors" title="Lihat Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                <!-- Row 4 - Empty State Example -->
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center text-xs font-bold flex-shrink-0 font-heading">
                                                WB
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">Warung Berkah</p>
                                                <p class="text-xs text-gray-400">Makanan Tradisional</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-gray-600">Pak Joko</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">Makanan</span>
                                    </td>
                                    <td class="py-4 px-5 text-gray-500">15 Sep 2026</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Ditolak
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right">
                                        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-djajan-primary hover:bg-djajan-primary/5 rounded-md transition-colors" title="Lihat Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/30 flex justify-center">
                        <a href="#" class="inline-flex items-center gap-1.5 text-sm font-medium text-djajan-primary hover:text-djajan-primary/80 transition-colors">
                            Lihat semua pengajuan
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN (1 col) -->
            <div class="space-y-6">

                <!-- Tinjau Pengajuan (CTA Card) - FIXED -->
                <div class="bg-djajan-primary rounded-xl p-5 text-white relative overflow-hidden shadow-lg border-2 border-djajan-primary">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <span class="text-xs font-medium bg-white/25 px-2.5 py-1 rounded-full backdrop-blur-sm">Prioritas</span>
                        </div>
                        <h4 class="text-base font-semibold font-heading mb-1">Tinjau Pengajuan</h4>
                        <p class="text-sm text-white/90 leading-relaxed mb-4">3 pengajuan UMKM baru menunggu persetujuan Anda.</p>
                        <a href="#" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-djajan-primary text-sm font-semibold rounded-lg hover:bg-white/95 transition-colors w-full shadow-sm">
                            Tinjau Sekarang
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>

                <!-- Distribusi Kategori -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Distribusi Kategori</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Total 12 UMKM terdaftar</p>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-djajan-primary"></span>
                                    <span class="text-sm font-medium text-gray-700">Makanan</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-900">6 <span class="text-gray-400 font-normal">UMKM</span></span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-djajan-primary rounded-full" style="width: 50%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-djajan-secondary"></span>
                                    <span class="text-sm font-medium text-gray-700">Minuman</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-900">3 <span class="text-gray-400 font-normal">UMKM</span></span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-djajan-secondary rounded-full" style="width: 25%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-djajan-tertiary"></span>
                                    <span class="text-sm font-medium text-gray-700">Kerajinan</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-900">2 <span class="text-gray-400 font-normal">UMKM</span></span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-djajan-tertiary rounded-full" style="width: 16.6%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                    <span class="text-sm font-medium text-gray-700">Lainnya</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-900">1 <span class="text-gray-400 font-normal">UMKM</span></span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gray-400 rounded-full" style="width: 8.3%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produk Terbaru -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Produk Terbaru</h3>
                        <a href="#" class="text-xs font-medium text-djajan-primary hover:text-djajan-primary/80">Lihat semua</a>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">Nasi Kotak</p>
                                <p class="text-xs text-gray-500 truncate">Dapur Kreyongan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">Kerajinan Lokal</p>
                                <p class="text-xs text-gray-500 truncate">Karya Lokal</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">Es Segar</p>
                                <p class="text-xs text-gray-500 truncate">Segar Kreyongan</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </main>

@endsection