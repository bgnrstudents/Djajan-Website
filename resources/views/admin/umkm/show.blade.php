@extends('layouts.admin')

@section('title', $umkm->nama_umkm . ' - Detail UMKM')

@section('content')

<!-- BREADCRUMB -->
<nav class="mb-4">
    <ol class="flex items-center gap-2 text-sm">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Dashboard</a></li>
        <li class="text-gray-400">/</li>
        <li><a href="{{ route('admin.umkm.index') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Data UMKM</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-djajan-neutral font-semibold">Detail UMKM</li>
    </ol>
</nav>

<!-- PAGE HEADER -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">{{ $umkm->nama_umkm }}</h1>
                @if($umkm->status_publikasi === 'aktif')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Aktif
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    Tidak Aktif
                </span>
                @endif
                @if($umkm->unggulan)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="text-amber-500"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    Unggulan
                </span>
                @endif
            </div>
            <p class="text-sm text-gray-500">Informasi lengkap mengenai profil usaha, data pemilik, dan daftar produk terdaftar.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.umkm.edit', $umkm->id) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-djajan-primary text-white text-sm font-medium rounded-lg hover:bg-djajan-primary-hover transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Edit UMKM
            </a>
            <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="space-y-6">

    <!-- INFORMASI UTAMA HERO CARD -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3">

            <!-- Gambar UMKM -->
            <div class="bg-gray-50 border-b lg:border-b-0 lg:border-r border-gray-200 p-6 flex items-center justify-center">
                @if($umkm->gambar)
                <img src="{{ asset('storage/' . $umkm->gambar) }}" alt="{{ $umkm->nama_umkm }}" class="w-full max-h-64 object-cover rounded-xl border border-gray-200/80 shadow-sm">
                @else
                <div class="w-full h-56 rounded-xl bg-gray-100 flex flex-col items-center justify-center text-gray-400 border border-gray-200/60">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <span class="text-xs font-medium text-gray-500">Foto belum diunggah</span>
                </div>
                @endif
            </div>

            <!-- Detail Profil -->
            <div class="lg:col-span-2 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-4 mb-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-djajan-primary/10 text-djajan-primary">
                            Kategori: {{ $umkm->kategori }}
                        </span>
                        <span class="text-xs text-gray-400 font-mono">
                            ID: UMKM-{{ str_pad($umkm->id, 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 font-heading mb-2">{{ $umkm->nama_umkm }}</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $umkm->deskripsi ?: 'Belum ada deskripsi profil untuk UMKM ini.' }}</p>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading">Tahun Berdiri</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $umkm->tahun_berdiri ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading">Total Produk</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $umkm->products->count() }} Produk</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading">Alamat Usaha</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5 truncate" title="{{ $umkm->alamat }}">{{ $umkm->alamat }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- DATA PEMILIK CARD -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900 font-heading">Data Pemilik Usaha</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-heading font-semibold">Nama Pemilik</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $umkm->nama_pemilik }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-heading font-semibold">Kontak Telepon / WA</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $umkm->no_telepon }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- DAFTAR PRODUK CARD -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-semibold text-gray-900 font-heading">Daftar Produk UMKM</h2>
                <p class="text-xs text-gray-500 mt-0.5">Seluruh produk yang dipasarkan oleh {{ $umkm->nama_umkm }}.</p>
            </div>
            <a href="{{ route('admin.products.create', $umkm->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-djajan-primary bg-djajan-primary/10 hover:bg-djajan-primary/20 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Produk
            </a>
        </div>

        @if($umkm->products->count())
        <div class="divide-y divide-gray-100">
            @foreach($umkm->products as $product)
            <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors">
                <!-- Gambar Produk -->
                <div class="w-14 h-14 flex-shrink-0 rounded-lg border border-gray-200 overflow-hidden bg-gray-50">
                    @if($product->gambar)
                    <img src="{{ asset('storage/' . $product->gambar) }}" alt="{{ $product->nama_produk }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect></svg>
                    </div>
                    @endif
                </div>

                <!-- Informasi Produk -->
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 font-heading">{{ $product->nama_produk }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $product->kategori ?: 'Tanpa Kategori' }}</p>
                </div>

                <!-- Harga & Status -->
                <div class="text-right">
                    <p class="text-sm font-semibold text-djajan-primary">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>
                    @if($product->status === 'aktif')
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Aktif
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-400 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                        Nonaktif
                    </span>
                    @endif
                </div>

                <!-- Edit Action -->
                <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-djajan-primary hover:bg-djajan-primary/10 transition-colors" title="Edit Produk">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-10 text-center">
            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
            </div>
            <p class="text-sm font-semibold text-gray-900 font-heading">Belum Ada Produk</p>
            <p class="text-xs text-gray-500 mt-0.5 mb-3">UMKM ini belum memiliki produk yang terdaftar.</p>
            <a href="{{ route('admin.products.create', $umkm->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-djajan-primary hover:bg-djajan-primary-hover rounded-lg transition-colors shadow-sm">
                + Tambah Produk Pertama
            </a>
        </div>
        @endif
    </div>

</div>

@endsection