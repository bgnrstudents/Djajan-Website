@extends('layouts.admin')

@section('title', 'Edit UMKM - Djajan')

@section('content')

<!-- BREADCRUMB -->
<nav class="mb-4">
    <ol class="flex items-center gap-2 text-sm">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Dashboard</a></li>
        <li class="text-gray-400">/</li>
        <li><a href="{{ route('admin.umkm.index') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Data UMKM</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-djajan-neutral font-semibold">Edit UMKM</li>
    </ol>
</nav>

<!-- PAGE HEADER -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Edit Data UMKM</h1>
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
            </div>
            <p class="text-sm text-gray-500">Mengubah informasi <span class="font-medium text-gray-700">{{ $umkm->nama_umkm }}</span> (ID: UMKM-{{ str_pad($umkm->id, 3, '0', STR_PAD_LEFT) }})</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.umkm.show', $umkm->id) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Lihat Detail
            </a>
            <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali
            </a>
        </div>
    </div>
</div>

<!-- FORM -->
<form action="{{ route('admin.umkm.update', $umkm->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <input type="hidden" name="hapus_gambar" id="hapus_gambar" value="0">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN -->
        <div class="lg:col-span-2 space-y-6">

            <!-- INFORMASI DASAR -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Informasi Dasar UMKM</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data utama usaha yang terdaftar.</p>
                </div>

                <div class="p-6 space-y-5">
                    <!-- Nama UMKM -->
                    <div>
                        <label for="nama_umkm" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama UMKM <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_umkm" name="nama_umkm" value="{{ old('nama_umkm', $umkm->nama_umkm) }}" placeholder="Contoh: Dapur Kreyongan" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('nama_umkm') border-red-300 @enderror">
                        @error('nama_umkm')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori + Tahun -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select id="kategori" name="kategori" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors cursor-pointer @error('kategori') border-red-300 @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach(['Makanan & Katering', 'Minuman', 'Kerajinan', 'Jasa', 'Lainnya'] as $kategori)
                                <option value="{{ $kategori }}" {{ old('kategori', $umkm->kategori) === $kategori ? 'selected' : '' }}>
                                    {{ $kategori }}
                                </option>
                                @endforeach
                            </select>
                            @error('kategori')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tahun_berdiri" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Tahun Berdiri
                            </label>
                            <input type="number" id="tahun_berdiri" name="tahun_berdiri" value="{{ old('tahun_berdiri', $umkm->tahun_berdiri) }}" min="1900" max="{{ date('Y') }}" placeholder="Contoh: 2018" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('tahun_berdiri') border-red-300 @enderror">
                            @error('tahun_berdiri')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Deskripsi Singkat
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" maxlength="2000" placeholder="Jelaskan secara singkat tentang usaha ini..." class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors resize-none @error('deskripsi') border-red-300 @enderror">{{ old('deskripsi', $umkm->deskripsi) }}</textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-xs text-gray-400">Maksimal 2000 karakter.</p>
                            <p class="text-xs text-gray-400"><span id="char-count">0</span>/2000</p>
                        </div>
                        @error('deskripsi')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap UMKM..." class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors resize-none @error('alamat') border-red-300 @enderror">{{ old('alamat', $umkm->alamat) }}</textarea>
                        @error('alamat')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gambar Utama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Foto / Logo UMKM
                        </label>

                        <!-- Preview Container -->
                        <div id="gambar-preview-container" class="{{ $umkm->gambar ? '' : 'hidden' }} mb-3">
                            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-gray-50 max-w-sm">
                                <img id="gambar-preview" src="{{ $umkm->gambar ? asset('storage/' . $umkm->gambar) : '' }}" alt="{{ $umkm->nama_umkm }}" class="w-full h-48 object-cover">
                            </div>
                            <div class="flex items-center gap-2 mt-3">
                                <button type="button" id="btn-ganti-gambar" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-djajan-primary bg-djajan-primary/10 hover:bg-djajan-primary/20 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    Ganti Gambar
                                </button>
                                <button type="button" id="btn-hapus-gambar" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Upload Area -->
                        <div id="gambar-upload-container" class="{{ $umkm->gambar ? 'hidden' : '' }}">
                            <label for="gambar" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50/50 hover:bg-gray-100 transition-colors">
                                <div class="flex flex-col items-center justify-center text-center px-4">
                                    <div class="w-9 h-9 rounded-full bg-djajan-primary/10 text-djajan-primary flex items-center justify-center mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <polyline points="17 8 12 3 7 8" />
                                            <line x1="12" y1="3" x2="12" y2="15" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">Klik untuk memilih gambar</p>
                                    <p class="text-xs text-gray-400 mt-0.5">JPG, JPEG, PNG, atau WebP (Maks 2 MB)</p>
                                </div>
                                <input type="file" name="gambar" id="gambar" accept=".jpg,.jpeg,.png,.webp" class="hidden">
                            </label>
                        </div>
                        @error('gambar')
                        <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- DATA PEMILIK -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Data Pemilik</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Informasi kontak pemilik usaha.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="nama_pemilik" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Pemilik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_pemilik" name="nama_pemilik" value="{{ old('nama_pemilik', $umkm->nama_pemilik) }}" placeholder="Nama lengkap pemilik" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('nama_pemilik') border-red-300 @enderror">
                        @error('nama_pemilik')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nomor WhatsApp / Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $umkm->no_telepon) }}" placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('no_telepon') border-red-300 @enderror">
                        @error('no_telepon')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- PRODUK UMKM SECTION -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Daftar Produk UMKM</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Produk yang terdaftar di bawah UMKM ini.</p>
                    </div>
                    <a href="{{ route('admin.products.create', $umkm->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-djajan-primary bg-djajan-primary/10 hover:bg-djajan-primary/20 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah Produk
                    </a>
                </div>

                <div class="p-6">
                    @forelse($umkm->products as $product)
                    <div class="flex items-center justify-between gap-4 py-3.5 border-b border-gray-100 last:border-b-0">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900">{{ $product->nama_produk }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                @if($product->kategori)
                                <span class="text-xs text-gray-500">{{ $product->kategori }}</span>
                                <span class="text-gray-300">•</span>
                                @endif
                                <span class="text-xs font-semibold text-djajan-primary">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            @if($product->status === 'aktif')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                Nonaktif
                            </span>
                            @endif

                            <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-djajan-primary bg-djajan-primary/10 hover:bg-djajan-primary/20 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                </svg>
                                Edit
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        </div>
                        <p class="text-sm font-medium text-gray-700 font-heading">Belum Ada Produk</p>
                        <p class="text-xs text-gray-400 mt-0.5 mb-3">Produk UMKM ini belum ditambahkan.</p>
                        <a href="{{ route('admin.products.create', $umkm->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-djajan-primary bg-djajan-primary/10 hover:bg-djajan-primary/20 rounded-lg transition-colors">
                            + Tambah Produk Pertama
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-6">

            <!-- INFORMASI SISTEM -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Informasi Sistem</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-100 text-xs">
                        <span class="text-gray-500">ID UMKM</span>
                        <span class="font-mono font-medium text-gray-900">UMKM-{{ str_pad($umkm->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-100 text-xs">
                        <span class="text-gray-500">Dibuat</span>
                        <span class="font-medium text-gray-900">{{ $umkm->created_at?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 text-xs">
                        <span class="text-gray-500">Terakhir Diperbarui</span>
                        <span class="font-medium text-gray-900">{{ $umkm->updated_at?->format('d M Y') ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- PENGATURAN TAMPILAN -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Pengaturan & Status</h3>
                </div>

                <div class="p-6 space-y-5">
                    <!-- UMKM UNGGULAN -->
                    <label class="flex items-start gap-3 p-3.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="checkbox" name="unggulan" value="1" {{ old('unggulan', $umkm->unggulan) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-djajan-primary border-gray-300 rounded focus:ring-djajan-primary">
                        <div>
                            <p class="text-sm font-medium text-gray-900 flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-djajan-tertiary">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                Set sebagai UMKM Unggulan
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">Tampilkan badge rekomendasi pada publik.</p>
                        </div>
                    </label>

                    <!-- STATUS -->
                    <div>
                        <label for="status_publikasi" class="block text-sm font-medium text-gray-700 mb-1.5">Status Publikasi <span class="text-red-500">*</span></label>
                        <select id="status_publikasi" name="status_publikasi" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors cursor-pointer @error('status_publikasi') border-red-300 @enderror">
                            <option value="aktif" {{ old('status_publikasi', $umkm->status_publikasi) === 'aktif' ? 'selected' : '' }}>
                                Aktif (Tampil di Publik)
                            </option>
                            <option value="nonaktif" {{ old('status_publikasi', $umkm->status_publikasi) === 'nonaktif' ? 'selected' : '' }}>
                                Tidak Aktif (Draft)
                            </option>
                        </select>
                        @error('status_publikasi')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-3">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-djajan-primary text-white text-sm font-semibold rounded-lg hover:bg-djajan-primary-hover transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Perbarui Data UMKM
                </button>
                <a href="{{ route('admin.umkm.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deskripsi = document.getElementById('deskripsi');
        const charCount = document.getElementById('char-count');
        if (deskripsi && charCount) {
            function updateCharCount() {
                const length = deskripsi.value.length;
                charCount.textContent = length;
                if (length > 2000) {
                    charCount.classList.add('text-red-600');
                } else {
                    charCount.classList.remove('text-red-600');
                }
            }
            deskripsi.addEventListener('input', updateCharCount);
            updateCharCount();
        }

        const gambarInput = document.getElementById('gambar');
        const gambarPreview = document.getElementById('gambar-preview');
        const gambarPreviewContainer = document.getElementById('gambar-preview-container');
        const gambarUploadContainer = document.getElementById('gambar-upload-container');
        const btnGantiGambar = document.getElementById('btn-ganti-gambar');
        const btnHapusGambar = document.getElementById('btn-hapus-gambar');

        if (btnGantiGambar) {
            btnGantiGambar.addEventListener('click', function() {
                gambarInput.click();
            });
        }

        if (gambarInput) {
            gambarInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (!file) return;

                document.getElementById('hapus_gambar').value = '0';
                const reader = new FileReader();
                reader.onload = function(e) {
                    gambarPreview.src = e.target.result;
                    gambarPreviewContainer.classList.remove('hidden');
                    gambarUploadContainer.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            });
        }

        if (btnHapusGambar) {
            btnHapusGambar.addEventListener('click', function() {
                gambarInput.value = '';
                document.getElementById('hapus_gambar').value = '1';
                gambarPreviewContainer.classList.add('hidden');
                gambarUploadContainer.classList.remove('hidden');
            });
        }
    });
</script>
@endpush