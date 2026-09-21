@extends('layouts.admin')

@section('title', 'Dapur Kreyongan - Detail UMKM')

@section('content')

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
        <li class="text-djajan-neutral font-semibold">Dapur Kreyongan</li>
    </ol>
</nav>

<!-- PAGE HEADER -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Detail UMKM</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Aktif
                </span>
            </div>
            <p class="text-sm text-gray-500">Informasi lengkap usaha Dapur Kreyongan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-djajan-primary text-white text-sm font-medium rounded-lg hover:bg-djajan-primary-hover transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Edit UMKM
            </a>
            <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Kembali
            </a>
        </div>
    </div>
</div>

<!-- HERO SECTION -->
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="grid grid-cols-1 lg:grid-cols-2">
        <!-- Image Side -->
        <div class="relative h-64 lg:h-auto bg-gray-100 min-h-[260px]">
            <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&auto=format&fit=crop" alt="Dapur Kreyongan" class="w-full h-full object-cover">
            
            <div class="absolute top-4 left-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-djajan-primary text-white text-xs font-medium rounded-full shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    UMKM Unggulan
                </span>
            </div>

            <div class="absolute bottom-4 left-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-medium rounded-full shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    Desa Kreyongan Atas
                </span>
            </div>
        </div>

        <!-- Info Side -->
        <div class="p-6 lg:p-8 flex flex-col justify-between">
            <div>
                <div class="mb-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-djajan-primary uppercase tracking-wider font-heading">
                        <span class="w-1.5 h-1.5 rounded-full bg-djajan-primary"></span>
                        MAKANAN & KATERING
                    </span>
                </div>
                
                <h2 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight mb-2">Dapur Kreyongan</h2>
                
                <p class="text-sm text-gray-600 leading-relaxed mb-6">
                    Usaha makanan rumahan yang sudah berdiri sejak 2018. Menyediakan nasi kotak, catering acara, dan berbagai olahan makanan khas desa untuk kebutuhan masyarakat sekitar.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                <div>
                    <p class="text-xl font-bold text-djajan-neutral font-heading">15+</p>
                    <p class="text-xs text-gray-500 mt-0.5">Produk</p>
                </div>
                <div class="border-l border-gray-100 pl-4">
                    <p class="text-xl font-bold text-djajan-neutral font-heading">6 Thn</p>
                    <p class="text-xs text-gray-500 mt-0.5">Pengalaman</p>
                </div>
                <div class="border-l border-gray-100 pl-4">
                    <p class="text-xl font-bold text-djajan-tertiary font-heading">4.9</p>
                    <p class="text-xs text-gray-500 mt-0.5">Rating</p>
                </div>
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
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading mb-1">Nama UMKM</dt>
                        <dd class="text-sm font-medium text-gray-900">Dapur Kreyongan</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading mb-1">ID UMKM</dt>
                        <dd class="text-sm font-medium text-gray-900 font-mono">UMKM-001</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading mb-1">Nama Pemilik</dt>
                        <dd class="text-sm font-medium text-gray-900">Budi Santoso</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading mb-1">Kategori</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">Makanan</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading mb-1">Nomor Telepon</dt>
                        <dd class="text-sm font-medium text-gray-900">0812-3456-7890</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading mb-1">Tahun Berdiri</dt>
                        <dd class="text-sm font-medium text-gray-900">2018</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading mb-1">Alamat Lengkap</dt>
                        <dd class="text-sm font-medium text-gray-900">Jl. Raya Kreyongan No. 12, RT 03/RW 02, Desa Kreyongan Atas</dd>
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
                <a href="{{ route('admin.umkm.index') }}" class="text-xs font-medium text-djajan-primary hover:text-djajan-primary-hover">Lihat semua</a>
            </div>

            <div class="divide-y divide-gray-100">
                <div class="px-6 py-4 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0 overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 font-heading">Nasi Kotak Spesial</p>
                            <p class="text-xs text-gray-500 mt-0.5">Makanan · Rp 25.000</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </div>
                </div>
                <div class="px-6 py-4 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0 overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 font-heading">Paket Catering 50 Pax</p>
                            <p class="text-xs text-gray-500 mt-0.5">Makanan · Rp 750.000</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
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
            
            <div class="space-y-3">
                <div class="flex items-center justify-between py-1.5 border-b border-gray-100 text-xs">
                    <span class="text-gray-500">Status</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Aktif
                    </span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-gray-100 text-xs">
                    <span class="text-gray-500">Terdaftar</span>
                    <span class="font-medium text-gray-900">12 Jan 2024</span>
                </div>
                <div class="flex items-center justify-between py-1.5 text-xs">
                    <span class="text-gray-500">Total Produk</span>
                    <span class="font-medium text-gray-900">15 produk</span>
                </div>
            </div>
        </div>

        <!-- Kontak Pemilik -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-djajan-neutral font-heading mb-4">Kontak Pemilik</h3>
            
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-djajan-primary/10 text-djajan-primary flex items-center justify-center font-semibold text-sm font-heading">
                    BS
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Budi Santoso</p>
                    <p class="text-xs text-gray-500">Pemilik Usaha</p>
                </div>
            </div>

            <div class="space-y-2">
                <a href="tel:081234567890" class="flex items-center gap-3 px-3 py-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-xs font-medium text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    0812-3456-7890
                </a>
            </div>
        </div>

    </div>
</div>

@endsection