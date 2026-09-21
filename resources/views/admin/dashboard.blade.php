@extends('layouts.admin')

@section('title', 'Dashboard - Djajan')

@section('content')

<!-- PAGE HEADER -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau dan kelola aktivitas utama Djajan dari satu tempat.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.umkm.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-djajan-primary text-white text-sm font-medium rounded-lg hover:bg-djajan-primary-hover transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah UMKM
            </a>
            <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Kelola UMKM
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
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </div>
            </div>
            <p class="text-3xl font-bold font-heading tracking-tight mb-2">12</p>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-white/80"></span>
                <p class="text-xs text-white/70">UMKM terdaftar aktif</p>
            </div>
        </div>
    </div>

    <!-- Stat 2: Total Produk -->
    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <p class="text-sm font-medium text-gray-600">Total Produk</p>
            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            </div>
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
            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
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
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
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
                    <button type="button" class="px-3 py-1.5 text-xs font-medium text-djajan-primary bg-djajan-primary/10 rounded-lg">Minggu</button>
                    <button type="button" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Bulan</button>
                </div>
            </div>

            <!-- Simple Bar Chart -->
            <div class="flex items-end justify-between gap-2 h-32 pt-4">
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-djajan-primary/20 rounded-t-md transition-all hover:bg-djajan-primary/30" style="height: 40%"></div>
                    <span class="text-xs text-gray-500">Sen</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-djajan-primary/40 rounded-t-md transition-all hover:bg-djajan-primary/50" style="height: 60%"></div>
                    <span class="text-xs text-gray-500">Sel</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-djajan-primary/60 rounded-t-md transition-all hover:bg-djajan-primary/70" style="height: 80%"></div>
                    <span class="text-xs text-gray-500">Rab</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-djajan-primary rounded-t-md transition-all hover:bg-djajan-primary-hover" style="height: 100%"></div>
                    <span class="text-xs text-gray-700 font-semibold">Kam</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-djajan-primary/50 rounded-t-md transition-all hover:bg-djajan-primary/60" style="height: 70%"></div>
                    <span class="text-xs text-gray-500">Jum</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-djajan-primary/30 rounded-t-md transition-all hover:bg-djajan-primary/40" style="height: 50%"></div>
                    <span class="text-xs text-gray-500">Sab</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-djajan-primary/20 rounded-t-md transition-all hover:bg-djajan-primary/30" style="height: 30%"></div>
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
                        <tr class="bg-gray-50/80 border-b border-gray-200">
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">UMKM</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemilik</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-right py-3 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-djajan-primary/10 text-djajan-primary flex items-center justify-center text-xs font-bold flex-shrink-0 font-heading">
                                        DK
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">Dapur Kreyongan</p>
                                        <p class="text-xs text-gray-400">Makanan & Minuman</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-gray-600">Budi Santoso</td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">Makanan</span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Menunggu
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-djajan-primary hover:bg-djajan-primary/10 rounded-lg transition-colors" title="Lihat Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        <!-- Row 2 -->
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-djajan-secondary/10 text-djajan-secondary flex items-center justify-center text-xs font-bold flex-shrink-0 font-heading">
                                        KL
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">Karya Lokal</p>
                                        <p class="text-xs text-gray-400">Kerajinan Tangan</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-gray-600">Siti Aminah</td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">Kerajinan</span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Menunggu
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-djajan-primary hover:bg-djajan-primary/10 rounded-lg transition-colors" title="Lihat Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        <!-- Row 3 -->
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-djajan-primary/10 text-djajan-primary flex items-center justify-center text-xs font-bold flex-shrink-0 font-heading">
                                        SK
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">Segar Kreyongan</p>
                                        <p class="text-xs text-gray-400">Minuman Segar</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-gray-600">Andi</td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">Minuman</span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200/50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Aktif
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-djajan-primary hover:bg-djajan-primary/10 rounded-lg transition-colors" title="Lihat Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/30 flex justify-center">
                <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-djajan-primary hover:text-djajan-primary-hover transition-colors">
                    Lihat semua data UMKM
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN (1 col) -->
    <div class="space-y-6">

        <!-- Tinjau Pengajuan (CTA Card) -->
        <div class="bg-djajan-primary rounded-xl p-5 text-white relative overflow-hidden shadow-sm border border-djajan-primary">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <span class="text-xs font-medium bg-white/20 px-2.5 py-1 rounded-full">Prioritas</span>
                </div>
                <h4 class="text-base font-semibold font-heading mb-1">Tinjau Pengajuan</h4>
                <p class="text-sm text-white/90 leading-relaxed mb-4">4 pengajuan UMKM baru menunggu persetujuan Anda.</p>
                <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-djajan-primary text-sm font-semibold rounded-lg hover:bg-white/95 transition-colors w-full shadow-sm">
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

            <div class="space-y-3.5">
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
                            <span class="text-sm font-medium text-gray-700">Jasa & Lainnya</span>
                        </div>
                        <span class="text-xs font-semibold text-gray-900">1 <span class="text-gray-400 font-normal">UMKM</span></span>
                    </div>
                    <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gray-400 rounded-full" style="width: 8.3%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection