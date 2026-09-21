@extends('layouts.admin')

@section('title', 'Tambah UMKM - Djajan')

@section('content')

<!-- BREADCRUMB -->
<nav class="mb-4">
    <ol class="flex items-center gap-2 text-sm">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Dashboard</a></li>
        <li class="text-gray-400">/</li>
        <li><a href="{{ route('admin.umkm.index') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Data UMKM</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-djajan-neutral font-semibold">Tambah UMKM</li>
    </ol>
</nav>

<!-- PAGE HEADER -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Tambah UMKM Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Isi data usaha baru untuk didaftarkan ke sistem pengelolaan UMKM Djajan.</p>
        </div>
        <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
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
                    <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Informasi Dasar UMKM</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Data utama yang akan ditampilkan pada sistem dan platform publik.</p>
                </div>

                <div class="p-6 space-y-5">

                    <!-- Nama UMKM -->
                    <div>
                        <label for="nama_umkm" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama UMKM <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_umkm" name="nama_umkm"
                            value="{{ old('nama_umkm') }}" placeholder="Contoh: Dapur Kreyongan" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('nama_umkm') border-red-300 focus:ring-red-200 focus:border-red-400 @enderror">
                        @error('nama_umkm')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Kategori -->
                        <div>
                            <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select id="kategori" name="kategori" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors cursor-pointer @error('kategori') border-red-300 @enderror">
                                <option value="">Pilih Kategori</option>
                                <option value="Makanan & Katering" {{ old('kategori') == 'Makanan & Katering' ? 'selected' : '' }}>Makanan & Katering</option>
                                <option value="Minuman" {{ old('kategori') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                                <option value="Kerajinan" {{ old('kategori') == 'Kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                                <option value="Jasa" {{ old('kategori') == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                                <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('kategori') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Tahun Berdiri -->
                        <div>
                            <label for="tahun_berdiri" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Tahun Berdiri
                            </label>
                            <input type="number" id="tahun_berdiri" name="tahun_berdiri" value="{{ old('tahun_berdiri') }}" placeholder="Contoh: 2018" min="1900" max="{{ date('Y') }}" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('tahun_berdiri') border-red-300 @enderror">
                            @error('tahun_berdiri') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Deskripsi Singkat
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Jelaskan secara singkat tentang produk atau usaha ini..." class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors resize-none @error('deskripsi') border-red-300 @enderror">{{ old('deskripsi') }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Teks deskripsi akan tampil pada kartu informasi UMKM.</p>
                        @error('deskripsi') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Alamat Detail -->
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="alamat" name="alamat" value="{{ old('alamat') }}" placeholder="Jl. Raya Kreyongan No. 12, RT 03/RW 02" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('alamat') border-red-300 @enderror">
                        @error('alamat')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Gambar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Foto / Logo UMKM
                        </label>

                        <!-- Preview Container -->
                        <div id="gambar-preview-container" class="hidden mb-3 space-y-3">
                            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-gray-50 max-w-sm">
                                <img id="gambar-preview" src="" alt="Preview Logo / Foto UMKM" class="w-full h-48 object-cover">
                            </div>
                            <div class="flex items-center gap-2">
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

                        <!-- Upload Container -->
                        <div id="gambar-upload-container">
                            <label for="file-upload" id="dropzone-label"
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-djajan-primary transition-colors bg-gray-50/50 cursor-pointer group">

                                <div class="space-y-1 text-center">

                                    <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-djajan-primary transition-colors"
                                        stroke="currentColor"
                                        fill="none"
                                        viewBox="0 0 48 48"
                                        aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>

                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium text-djajan-primary">
                                            Pilih berkas foto
                                        </span>
                                        <span class="pl-1">
                                            atau tarik ke sini
                                        </span>
                                    </div>

                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, WEBP hingga 2MB
                                    </p>

                                    <input
                                        id="file-upload"
                                        name="gambar"
                                        type="file"
                                        class="sr-only"
                                        accept="image/png,image/jpeg,image/webp">
                                </div>
                            </label>
                        </div>

                        @error('gambar')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN (Settings & Owner) -->
        <div class="space-y-6">

            <!-- Card: Data Pemilik -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Data Pemilik</h3>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label for="nama_pemilik" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Pemilik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_pemilik" name="nama_pemilik" value="{{ old('nama_pemilik') }}" placeholder="Nama lengkap pemilik" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('nama_pemilik') border-red-300 @enderror">
                        @error('nama_pemilik')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nomor Telepon / WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('no_telepon') border-red-300 @enderror">
                        @error('no_telepon')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Card: Pengaturan Tampilan -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Pengaturan & Status</h3>
                </div>
                <div class="p-6 space-y-5">

                    <!-- Toggle Unggulan -->
                    <label class="flex items-start gap-3 p-3.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50/80 transition-colors">
                        <input type="checkbox" name="unggulan" value="1" {{ old('unggulan') ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-djajan-primary border-gray-300 rounded focus:ring-djajan-primary">
                        <div>
                            <p class="text-sm font-medium text-gray-900 flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-djajan-tertiary">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                Set sebagai UMKM Unggulan
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">Menampilkan rekomendasi khusus pada halaman utama.</p>
                        </div>
                    </label>

                    <!-- Status -->
                    <div>
                        <label for="status_publikasi" class="block text-sm font-medium text-gray-700 mb-1.5">Status Publikasi <span class="text-red-500">*</span></label>
                        <select id="status_publikasi" name="status_publikasi" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors cursor-pointer @error('status_publikasi') border-red-300 @enderror">
                            <option value="aktif" {{ old('status_publikasi', 'aktif') == 'aktif' ? 'selected' : '' }}>
                                Aktif (Tampil di Publik)
                            </option>
                            <option value="nonaktif" {{ old('status_publikasi') == 'nonaktif' ? 'selected' : '' }}>
                                Tidak Aktif (Draft)
                            </option>
                        </select>
                        @error('status_publikasi')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Submit Button Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-3">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-djajan-primary text-white text-sm font-semibold rounded-lg hover:bg-djajan-primary-hover transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Simpan Data UMKM
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
    document.addEventListener('DOMContentLoaded', function () {
        const fileUpload = document.getElementById('file-upload');
        const gambarPreview = document.getElementById('gambar-preview');
        const gambarPreviewContainer = document.getElementById('gambar-preview-container');
        const gambarUploadContainer = document.getElementById('gambar-upload-container');
        const dropzoneLabel = document.getElementById('dropzone-label');
        const btnGantiGambar = document.getElementById('btn-ganti-gambar');
        const btnHapusGambar = document.getElementById('btn-hapus-gambar');

        function handleFile(file) {
            if (!file) return;

            if (!file.type.match('image.*')) {
                alert('Silakan pilih file gambar (PNG, JPG, WEBP).');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                gambarPreview.src = e.target.result;
                gambarPreviewContainer.classList.remove('hidden');
                gambarUploadContainer.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        if (fileUpload) {
            fileUpload.addEventListener('change', function (e) {
                const file = e.target.files[0];
                handleFile(file);
            });
        }

        if (dropzoneLabel) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzoneLabel.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneLabel.classList.add('border-djajan-primary', 'bg-djajan-primary/5');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzoneLabel.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneLabel.classList.remove('border-djajan-primary', 'bg-djajan-primary/5');
                }, false);
            });

            dropzoneLabel.addEventListener('drop', function (e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length > 0) {
                    fileUpload.files = files;
                    handleFile(files[0]);
                }
            }, false);
        }

        if (btnGantiGambar) {
            btnGantiGambar.addEventListener('click', function () {
                fileUpload.click();
            });
        }

        if (btnHapusGambar) {
            btnHapusGambar.addEventListener('click', function () {
                fileUpload.value = '';
                gambarPreview.src = '';
                gambarPreviewContainer.classList.add('hidden');
                gambarUploadContainer.classList.remove('hidden');
            });
        }
    });
</script>
@endpush
