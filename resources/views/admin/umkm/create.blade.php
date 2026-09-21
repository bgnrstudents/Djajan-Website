@extends('layouts.admin')

@section('title', 'Tambah UMKM - Djajan')

@section('content')

<!-- TOPBAR -->
<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-6 flex-shrink-0">
    <div class="flex items-center gap-4 flex-1">
        <button class="lg:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <div class="hidden md:flex items-center gap-2 flex-1 max-w-md">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" placeholder="Cari di seluruh sistem..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary">
            </div>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors" title="Notifikasi">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-djajan-tertiary rounded-full ring-2 ring-white"></span>
        </button>
        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-medium text-gray-900">Admin Djajan</p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
            <div class="w-9 h-9 bg-djajan-primary text-white rounded-full flex items-center justify-center font-semibold text-sm ring-2 ring-white shadow-sm">A</div>
        </div>
    </div>
</header>

<!-- MAIN CONTENT -->
<main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">

    <!-- BREADCRUMB -->
    <nav class="mb-4">
        <ol class="flex items-center gap-2 text-sm">
            <li><a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('admin.umkm.index') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Data UMKM</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-djajan-neutral font-medium">Tambah UMKM</li>
        </ol>
    </nav>

    <!-- PAGE HEADER -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Tambah UMKM</h1>
                <p class="text-sm text-gray-500 mt-1">Isi data usaha baru untuk didaftarkan ke sistem Djajan.</p>
            </div>
            <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- FORM -->
    <form action="{{ route('admin.umkm.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT COLUMN (Main Info) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Card 1: Informasi Dasar -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Informasi Dasar</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Data utama yang akan tampil di kartu UMKM.</p>
                    </div>

                    <div class="p-6 space-y-5">

                        <!-- Nama UMKM -->
                        <div>
                            <label for="nama" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nama UMKM <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama" name="nama_umkm"
                                value="{{ old('nama_umkm') }}" placeholder="Contoh: Dapur Kreyongan" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('nama') border-red-300 focus:ring-red-200 focus:border-red-400 @enderror">
                            @error('nama_umkm')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Kategori -->
                            <div>
                                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select id="kategori" name="kategori" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors appearance-none cursor-pointer @error('kategori') border-red-300 @enderror">
                                    <option value="">Pilih Kategori</option>
                                    <option value="Makanan & Katering" {{ old('kategori') == 'Makanan & Katering' ? 'selected' : '' }}>Makanan & Katering</option>
                                    <option value="Minuman" {{ old('kategori') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                                    <option value="Kerajinan" {{ old('kategori') == 'Kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                                    <option value="Jasa" {{ old('kategori') == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                                    <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('kategori') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Tahun Berdiri -->
                            <div>
                                <label for="tahun_berdiri" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Tahun Berdiri <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="tahun_berdiri" name="tahun_berdiri" value="{{ old('tahun_berdiri') }}" placeholder="Contoh: 2018" min="1900" max="{{ date('Y') }}" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('tahun_berdiri') border-red-300 @enderror">
                                @error('tahun_berdiri') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Deskripsi Singkat <span class="text-red-500">*</span>
                            </label>
                            <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Jelaskan secara singkat tentang usaha ini..." class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors resize-none @error('deskripsi') border-red-300 @enderror">{{ old('deskripsi') }}</textarea>
                            <p class="mt-1 text-xs text-gray-400">Maksimal 200 karakter. Akan tampil di kartu utama.</p>
                            @error('deskripsi') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <!-- Alamat Detail -->
                        <div>
                            <label for="alamat_detail" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Detail (Jalan, RT/RW)</label>
                            <input type="text" id="alamat_detail" name="alamat"
                                value="{{ old('alamat') }}" placeholder="Jl. Raya Kreyongan No. 12, RT 03/RW 02" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors">@error('alamat')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Upload Gambar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Gambar Utama <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-djajan-primary transition-colors bg-gray-50 cursor-pointer group">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-djajan-primary transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-djajan-primary hover:text-djajan-primary/80 focus-within:outline-none">
                                            <span>Upload file</span>
                                            <input id="file-upload" name="gambar" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                            @error('gambar') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>


                    </div>
                </div>

                <!-- Card 3: Produk UMKM (REPEATER) -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Produk UMKM</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tambahkan produk atau layanan yang ditawarkan.</p>
                        </div>
                        <span id="produk-count" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">0 produk</span>
                    </div>

                    <div class="p-6">
                        <!-- Container Produk -->
                        <div id="produk-container" class="space-y-4">
                            <!-- Produk item akan ditambahkan di sini via JS -->
                        </div>

                        <!-- Empty State -->
                        <div id="produk-empty" class="py-8 text-center border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="mx-auto h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Belum ada produk ditambahkan</p>
                            <p class="text-xs text-gray-400">Klik tombol di bawah untuk menambah produk pertama</p>
                        </div>

                        <!-- Tombol Tambah Produk -->
                        <button type="button" id="btn-tambah-produk" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:border-djajan-primary hover:text-djajan-primary hover:bg-djajan-primary/5 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Tambah Produk
                        </button>

                        <p class="mt-2 text-xs text-gray-400 text-center">Produk dapat diedit dan dihapus sebelum disimpan.</p>
                    </div>

                </div>

            </div>

            <!-- RIGHT COLUMN (Settings & Owner) -->
            <div class="space-y-6">

                <!-- Card 4: Pengaturan Tampilan -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Pengaturan Tampilan</h3>
                    </div>
                    <div class="p-6 space-y-5">

                        <!-- Toggle Unggulan -->
                        <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input
                                type="checkbox"
                                name="unggulan"
                                value="1"
                                {{ old('unggulan') ? 'checked' : '' }} class="mt-1 w-4 h-4 text-djajan-primary border-gray-300 rounded focus:ring-djajan-primary">
                            <div>
                                <p class="text-sm font-medium text-gray-900 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-djajan-tertiary">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                    UMKM Unggulan
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">Tampilkan badge khusus di halaman utama.</p>
                            </div>
                        </label>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status Publikasi</label>
                            <select id="status_publikasi"
                                name="status_publikasi" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors appearance-none cursor-pointer @error('status') border-red-300 @enderror">
                                <option value="aktif"
                                    {{ old('status_publikasi', 'aktif') == 'aktif' ? 'selected' : '' }}>
                                    Aktif (Tampil di publik)
                                </option>

                                <option value="nonaktif"
                                    {{ old('status_publikasi') == 'nonaktif' ? 'selected' : '' }}>
                                    Tidak Aktif (Draft)
                                </option>
                            </select>
                            @error('status_publikasi')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Card 5: Data Pemilik -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Data Pemilik</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="pemilik" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pemilik <span class="text-red-500">*</span></label>
                            <input type="text" id="pemilik" name="nama_pemilik" value="{{ old('nama_pemilik') }}" placeholder="Nama lengkap" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('pemilik') border-red-300 @enderror">
                            @error('nama_pemilik')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp / Telepon</label>
                            <input type="tel" id="telepon" name="no_telepon" value="{{ old('no_telepon') }}" placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors">  
                             @error('telepon')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- FORM FOOTER (Sticky bottom - di luar grid) -->
        <div class="mt-6 bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-end gap-3 sticky bottom-4 z-10">
            <a href="{{ route('admin.umkm.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-djajan-primary text-white text-sm font-medium rounded-lg hover:bg-djajan-primary/90 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Simpan UMKM
            </button>
        </div>

    </form>

</main>

<!-- Template Produk (Hidden) -->
<template id="produk-template">
    <div class="produk-item bg-gray-50 border border-gray-200 rounded-lg p-4 relative group" data-produk-index="__INDEX__">
        <!-- Header Row -->
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-md bg-djajan-primary/10 text-djajan-primary flex items-center justify-center text-xs font-bold font-heading produk-number">
                    1
                </div>
                <span class="text-sm font-medium text-gray-700">Produk</span>
            </div>
            <button type="button" class="produk-hapus p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Hapus produk">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </button>
        </div>

        <!-- Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="produk[__INDEX__][nama]" placeholder="Contoh: Nasi Kotak Spesial" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Harga (Rp)</label>
                <input type="number" name="produk[__INDEX__][harga]" placeholder="25000" min="0" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Satuan</label>
                <select name="produk[__INDEX__][satuan]" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors appearance-none cursor-pointer">
                    <option value="Porsi">Porsi</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Paket">Paket</option>
                    <option value="Box">Box</option>
                    <option value="Kg">Kg</option>
                    <option value="Liter">Liter</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi Singkat</label>
                <input type="text" name="produk[__INDEX__][deskripsi]" placeholder="Deskripsi singkat produk (opsional)" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors">
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('produk-container');
        const emptyState = document.getElementById('produk-empty');
        const btnTambah = document.getElementById('btn-tambah-produk');
        const template = document.getElementById('produk-template');
        const countLabel = document.getElementById('produk-count');
        let index = 0;

        function updateCount() {
            const count = container.querySelectorAll('.produk-item').length;
            countLabel.textContent = count + ' produk';
            emptyState.style.display = count === 0 ? 'block' : 'none';
        }

        function updateNumbers() {
            const items = container.querySelectorAll('.produk-item');
            items.forEach((item, i) => {
                const num = item.querySelector('.produk-number');
                if (num) num.textContent = i + 1;
            });
        }

        btnTambah.addEventListener('click', function() {
            const clone = template.content.cloneNode(true);
            const html = clone.querySelector('.produk-item').outerHTML;
            const div = document.createElement('div');
            div.innerHTML = html.replace(/__INDEX__/g, index);
            container.appendChild(div.firstElementChild);
            index++;
            updateCount();
            updateNumbers();
        });

        container.addEventListener('click', function(e) {
            const btn = e.target.closest('.produk-hapus');
            if (btn) {
                const item = btn.closest('.produk-item');
                if (item) {
                    item.remove();
                    updateCount();
                    updateNumbers();
                }
            }
        });

        updateCount();
    });
</script>
@endpush

@endsection