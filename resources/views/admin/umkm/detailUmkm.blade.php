@extends('layouts.admin')

@section('title', 'Dapur Kreyongan - Detail UMKM')

@section('content')

    <!-- TOPBAR -->
    <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-6 flex-shrink-0">
        <div class="flex items-center gap-4 flex-1">
            <button class="lg:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            
            <div class="hidden md:flex items-center gap-2 flex-1 max-w-md">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" placeholder="Cari di seluruh sistem..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary">
                </div>
            </div>
        </div>

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

        <!-- BREADCRUMB -->
        <nav class="mb-4">
            <ol class="flex items-center gap-2 text-sm flex-wrap">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Dashboard</a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <a href="{{ route('admin.umkm.index') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Data UMKM</a>
                </li>
                <li class="text-gray-400">/</li>
                <li class="text-djajan-neutral font-medium">Dapur Kreyongan</li>
            </ol>
        </nav>

        <!-- PAGE HEADER -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Detail UMKM</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Aktif
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">Informasi lengkap usaha Dapur Kreyongan.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        Edit UMKM
                    </a>
                    <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- HERO SECTION (Sesuai card home) -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                
                <!-- Image Side -->
                <div class="relative h-64 lg:h-auto bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&auto=format&fit=crop" alt="Dapur Kreyongan" class="w-full h-full object-cover">
                    
                    <!-- Badge Unggulan -->
                    <div class="absolute top-4 left-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-djajan-primary text-white text-xs font-medium rounded-full shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            UMKM Unggulan
                        </span>
                    </div>

                    <!-- Lokasi Badge -->
                    <div class="absolute bottom-4 left-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-gray-700 text-xs font-medium rounded-full shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Desa Kreyongan Atas
                        </span>
                    </div>
                </div>

                <!-- Info Side -->
                <div class="p-6 lg:p-8">
                    <div class="mb-4">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-djajan-primary uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-djajan-primary"></span>
                            MAKANAN & KATERING
                        </span>
                    </div>
                    
                    <h2 class="text-3xl font-bold text-djajan-neutral font-heading tracking-tight mb-3">Dapur Kreyongan</h2>
                    
                    <p class="text-sm text-gray-600 leading-relaxed mb-6">
                        Usaha makanan rumahan yang sudah berdiri sejak 2018. Menyediakan nasi kotak, catering acara, dan berbagai olahan makanan khas desa untuk kebutuhan masyarakat sekitar.
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-100">
                        <div>
                            <p class="text-2xl font-bold text-djajan-neutral font-heading">15+</p>
                            <p class="text-xs text-gray-500 mt-0.5">Produk</p>
                        </div>
                        <div class="border-l border-gray-100 pl-4">
                            <p class="text-2xl font-bold text-djajan-neutral font-heading">6</p>
                            <p class="text-xs text-gray-500 mt-0.5">Tahun</p>
                        </div>
                        <div class="border-l border-gray-100 pl-4">
                            <p class="text-2xl font-bold text-djajan-tertiary font-heading">4.9</p>
                            <p class="text-xs text-gray-500 mt-0.5">Rating</p>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <a href="#" class="inline-flex items-center justify-center gap-2 w-full px-5 py-3 bg-djajan-neutral text-white text-sm font-medium rounded-lg hover:bg-djajan-neutral/90 transition-colors">
                            Kunjungi Halaman UMKM
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- DETAIL GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT COLUMN (2 cols) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Informasi Usaha -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-djajan-neutral font-heading">Informasi Usaha</h3>
                    </div>
                    
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-8">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Nama UMKM</dt>
                                <dd class="text-sm font-medium text-gray-900">Dapur Kreyongan</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">ID UMKM</dt>
                                <dd class="text-sm font-medium text-gray-900 font-mono">UMKM-001</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Nama Pemilik</dt>
                                <dd class="text-sm font-medium text-gray-900">Budi Santoso</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Kategori</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">Makanan</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Nomor Telepon</dt>
                                <dd class="text-sm font-medium text-gray-900">0812-3456-7890</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Tahun Berdiri</dt>
                                <dd class="text-sm font-medium text-gray-900">2018</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Alamat Lengkap</dt>
                                <dd class="text-sm font-medium text-gray-900">Jl. Raya Kreyongan No. 12, RT 03/RW 02, Desa Kreyongan Atas, Kecamatan Ngancar, Kabupaten Kediri</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Deskripsi Usaha</dt>
                                <dd class="text-sm text-gray-700 leading-relaxed">Usaha makanan rumahan yang sudah berdiri sejak 2018. Menyediakan nasi kotak, catering acara, dan berbagai olahan makanan khas desa untuk kebutuhan masyarakat sekitar. Menggunakan bahan-bahan segar dari petani lokal.</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Produk UMKM -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-djajan-neutral font-heading">Produk UMKM</h3>
                            <p class="text-xs text-gray-500 mt-0.5">15 produk terdaftar</p>
                        </div>
                        <a href="#" class="text-xs font-medium text-djajan-primary hover:text-djajan-primary/80">Lihat semua</a>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <!-- Product 1 -->
                        <div class="px-6 py-4 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0 overflow-hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">Nasi Kotak Spesial</p>
                                    <p class="text-xs text-gray-500">Makanan · Rp 25.000</p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200/50 flex-shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Aktif
                                </span>
                            </div>
                        </div>

                        <!-- Product 2 -->
                        <div class="px-6 py-4 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0 overflow-hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">Paket Catering 50 Pax</p>
                                    <p class="text-xs text-gray-500">Makanan · Rp 750.000</p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200/50 flex-shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Aktif
                                </span>
                            </div>
                        </div>

                        <!-- Product 3 -->
                        <div class="px-6 py-4 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0 overflow-hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">Lauk Pauk Tradisional</p>
                                    <p class="text-xs text-gray-500">Makanan · Rp 15.000</p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200/50 flex-shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (1 col) -->
            <div class="space-y-6">

                <!-- Status & Metadata -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-djajan-neutral font-heading mb-4">Status & Metadata</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Status</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Aktif
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Terdaftar</span>
                            <span class="text-sm font-medium text-gray-900">12 Jan 2024</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Terakhir Update</span>
                            <span class="text-sm font-medium text-gray-900">18 Sep 2026</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Total Produk</span>
                            <span class="text-sm font-medium text-gray-900">15 produk</span>
                        </div>
                    </div>
                </div>

                <!-- Kontak Pemilik -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-djajan-neutral font-heading mb-4">Kontak Pemilik</h3>
                    
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-full bg-djajan-primary/10 text-djajan-primary flex items-center justify-center font-semibold text-sm font-heading">
                            BS
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Budi Santoso</p>
                            <p class="text-xs text-gray-500">Pemilik Usaha</p>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <a href="tel:081234567890" class="flex items-center gap-3 px-3 py-2.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <span class="text-sm text-gray-700">0812-3456-7890</span>
                        </a>
                        <a href="mailto:budi@dapurkreyongan.id" class="flex items-center gap-3 px-3 py-2.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <span class="text-sm text-gray-700 truncate">budi@dapurkreyongan.id</span>
                        </a>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white border border-red-200 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-red-700 font-heading mb-2">Zona Berbahaya</h3>
                    <p class="text-xs text-gray-600 mb-4">Tindakan di bawah ini tidak dapat dibatalkan.</p>
                    
                    <div class="space-y-2">
                        <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                            Nonaktifkan UMKM
                        </button>
                        <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-red-200 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            Hapus UMKM
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </main>

@endsection