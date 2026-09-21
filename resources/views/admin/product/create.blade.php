@extends('layouts.admin')

@section('title', 'Tambah Produk | Djajan')

@section('content')

<!-- BREADCRUMB -->
<nav class="mb-4">
    <ol class="flex items-center gap-2 text-sm">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Dashboard</a></li>
        <li class="text-gray-400">/</li>
        <li><a href="{{ route('admin.umkm.index') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Data UMKM</a></li>
        <li class="text-gray-400">/</li>
        <li><a href="{{ route('admin.umkm.edit', $umkm->id) }}" class="text-gray-500 hover:text-djajan-primary transition-colors">{{ $umkm->nama_umkm }}</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-djajan-neutral font-semibold">Tambah Produk</li>
    </ol>
</nav>

<!-- PAGE HEADER -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Tambah Produk Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Tambahkan produk baru untuk usaha <span class="font-semibold text-gray-700">{{ $umkm->nama_umkm }}</span>.</p>
        </div>
        <a href="{{ route('admin.umkm.edit', $umkm->id) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke UMKM
        </a>
    </div>
</div>

<!-- VALIDATION ERROR -->
@if ($errors->any())
<div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <div>
            <p class="text-sm font-semibold text-red-800">Terdapat kesalahan pada input Anda:</p>
            <ul class="mt-1 text-sm text-red-700 list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<!-- FORM CONTAINER -->
<form action="{{ route('admin.products.store', $umkm->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-4xl">
    @csrf

    <!-- INFORMASI PRODUK -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-djajan-neutral font-heading">Informasi Produk</h2>
            <p class="text-xs text-gray-500 mt-0.5">Masukkan nama, kategori, dan harga produk.</p>
        </div>

        <div class="p-6 space-y-5">
            <!-- NAMA PRODUK -->
            <div>
                <label for="nama_produk" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Produk <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" required maxlength="255" placeholder="Contoh: Nasi Pecel Spesial" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('nama_produk') border-red-300 @enderror">
                @error('nama_produk')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- KATEGORI + HARGA -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1.5">Kategori Produk</label>
                    <select id="kategori" name="kategori" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors cursor-pointer @error('kategori') border-red-300 @enderror">
                        <option value="">Pilih Kategori</option>
                        <option value="Makanan" {{ old('kategori') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="Minuman" {{ old('kategori') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                        <option value="Kerajinan" {{ old('kategori') == 'Kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                        <option value="Jasa" {{ old('kategori') == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                        <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('kategori')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Harga Produk (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-400">Rp</span>
                        <input type="number" id="harga" name="harga" value="{{ old('harga') }}" required min="0" step="100" placeholder="15000" class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('harga') border-red-300 @enderror">
                    </div>
                    @error('harga')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- DESKRIPSI -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Singkat</label>
                    <span class="text-xs text-gray-400"><span id="char-count">0</span>/300</span>
                </div>
                <textarea id="deskripsi" name="deskripsi" rows="4" maxlength="300" placeholder="Jelaskan porsi, varian, atau bahan utama produk..." class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors resize-none @error('deskripsi') border-red-300 @enderror">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- GAMBAR PRODUK -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-djajan-neutral font-heading">Gambar Produk</h2>
            <p class="text-xs text-gray-500 mt-0.5">Unggah foto produk yang menarik dan jelas.</p>
        </div>

        <div class="p-6">
            <div id="upload-area" class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-djajan-primary transition-colors bg-gray-50/50 cursor-pointer group">
                <div class="space-y-1 text-center pointer-events-none">
                    <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-djajan-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-10h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div class="text-sm text-gray-600">
                        <span class="font-medium text-djajan-primary">Pilih gambar produk</span>
                        <span>atau tarik file ke sini</span>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG, WEBP hingga 2 MB</p>
                </div>
                <input id="gambar" name="gambar" type="file" class="hidden" accept="image/jpeg,image/png,image/webp">
            </div>

            <!-- PREVIEW GAMBAR -->
            <div id="image-preview" class="hidden mt-4">
                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider font-heading mb-2">Preview Gambar</p>
                <div class="relative max-w-xs rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                    <img id="preview-img" src="" alt="Preview gambar produk" class="w-full h-44 object-cover">
                    <button type="button" id="btn-remove-image" class="absolute top-2 right-2 p-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors shadow-sm" title="Hapus gambar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            @error('gambar')
            <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- STATUS PRODUK -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-djajan-neutral font-heading">Status Publikasi Produk</h2>
        </div>

        <div class="p-6">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status Produk <span class="text-red-500">*</span></label>
            <select id="status" name="status" required class="w-full md:w-72 px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors cursor-pointer @error('status') border-red-300 @enderror">
                <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif (Ditampilkan)</option>
                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif (Draft)</option>
            </select>
            @error('status')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="flex items-center justify-end gap-3 pt-2">
        <a href="{{ route('admin.umkm.edit', $umkm->id) }}" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-djajan-primary text-white text-sm font-semibold hover:bg-djajan-primary-hover transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Produk
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const uploadArea = document.getElementById('upload-area');
    const gambarInput = document.getElementById('gambar');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const btnRemoveImage = document.getElementById('btn-remove-image');

    if (uploadArea && gambarInput) {
        uploadArea.addEventListener('click', function () {
            gambarInput.click();
        });
    }

    if (gambarInput) {
        gambarInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (event) {
                previewImg.src = event.target.result;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    }

    if (btnRemoveImage) {
        btnRemoveImage.addEventListener('click', function (event) {
            event.stopPropagation();
            gambarInput.value = '';
            previewImg.src = '';
            imagePreview.classList.add('hidden');
        });
    }

    const deskripsi = document.getElementById('deskripsi');
    const charCount = document.getElementById('char-count');
    if (deskripsi && charCount) {
        function updateCharCount() {
            charCount.textContent = deskripsi.value.length;
        }
        deskripsi.addEventListener('input', updateCharCount);
        updateCharCount();
    }
});
</script>
@endpush