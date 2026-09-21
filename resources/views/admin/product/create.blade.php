@extends('layouts.admin')

@section('title', 'Tambah Produk | Djajan')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="mb-6">

        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">

            <a
                href="{{ route('admin.umkm.edit', $umkm->id) }}"
                class="hover:text-djajan-primary transition-colors"
            >
                {{ $umkm->nama_umkm }}
            </a>

            <span>/</span>

            <span>Tambah Produk</span>

        </div>


        <h1 class="text-2xl font-bold text-djajan-neutral font-heading">
            Tambah Produk
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Tambahkan produk baru untuk
            <span class="font-medium text-gray-700">
                {{ $umkm->nama_umkm }}
            </span>.
        </p>

    </div>


    {{-- ========================================================= --}}
    {{-- VALIDATION ERROR --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">

            <p class="text-sm font-semibold text-red-700">
                Terdapat kesalahan pada form:
            </p>

            <ul class="mt-2 text-sm text-red-600 list-disc list-inside">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}

    <form
        action="{{ route('admin.products.store', $umkm->id) }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf


        {{-- ===================================================== --}}
        {{-- INFORMASI PRODUK --}}
        {{-- ===================================================== --}}

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">

            {{-- Section Header --}}
            <div class="px-6 py-4 border-b border-gray-100">

                <h2 class="text-sm font-semibold text-djajan-neutral font-heading">
                    Informasi Produk
                </h2>

                <p class="text-xs text-gray-500 mt-0.5">
                    Masukkan informasi dasar produk yang akan ditampilkan.
                </p>

            </div>


            <div class="p-6 space-y-5">

                {{-- ================================================= --}}
                {{-- NAMA PRODUK --}}
                {{-- ================================================= --}}

                <div>

                    <label
                        for="nama_produk"
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                    >
                        Nama Produk
                        <span class="text-red-500">*</span>
                    </label>


                    <input
                        type="text"
                        id="nama_produk"
                        name="nama_produk"
                        value="{{ old('nama_produk') }}"
                        required
                        maxlength="255"
                        placeholder="Contoh: Nasi Pecel"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-djajan-primary focus:ring-2 focus:ring-djajan-primary/10 outline-none transition"
                    >


                    @error('nama_produk')

                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- ================================================= --}}
                {{-- KATEGORI + HARGA --}}
                {{-- ================================================= --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Kategori --}}
                    <div>

                        <label
                            for="kategori"
                            class="block text-sm font-medium text-gray-700 mb-1.5"
                        >
                            Kategori
                        </label>


                        <select
                            id="kategori"
                            name="kategori"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm bg-white focus:border-djajan-primary focus:ring-2 focus:ring-djajan-primary/10 outline-none transition"
                        >

                            <option value="">
                                Pilih kategori
                            </option>

                            <option
                                value="Makanan"
                                {{ old('kategori') == 'Makanan' ? 'selected' : '' }}
                            >
                                Makanan
                            </option>

                            <option
                                value="Minuman"
                                {{ old('kategori') == 'Minuman' ? 'selected' : '' }}
                            >
                                Minuman
                            </option>

                            <option
                                value="Kerajinan"
                                {{ old('kategori') == 'Kerajinan' ? 'selected' : '' }}
                            >
                                Kerajinan
                            </option>

                            <option
                                value="Jasa"
                                {{ old('kategori') == 'Jasa' ? 'selected' : '' }}
                            >
                                Jasa
                            </option>

                            <option
                                value="Lainnya"
                                {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}
                            >
                                Lainnya
                            </option>

                        </select>


                        @error('kategori')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Harga --}}
                    <div>

                        <label
                            for="harga"
                            class="block text-sm font-medium text-gray-700 mb-1.5"
                        >
                            Harga
                            <span class="text-red-500">*</span>
                        </label>


                        <div class="relative">

                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">
                                Rp
                            </span>


                            <input
                                type="number"
                                id="harga"
                                name="harga"
                                value="{{ old('harga') }}"
                                required
                                min="0"
                                step="100"
                                placeholder="10000"
                                class="w-full rounded-lg border border-gray-300 pl-10 pr-3 py-2.5 text-sm focus:border-djajan-primary focus:ring-2 focus:ring-djajan-primary/10 outline-none transition"
                            >

                        </div>


                        @error('harga')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- DESKRIPSI --}}
                {{-- ================================================= --}}

                <div>

                    <div class="flex items-center justify-between mb-1.5">

                        <label
                            for="deskripsi"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Deskripsi
                        </label>


                        <span class="text-xs text-gray-400">

                            <span id="char-count">
                                0
                            </span>/300

                        </span>

                    </div>


                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        rows="5"
                        maxlength="300"
                        placeholder="Jelaskan produk secara singkat..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-djajan-primary focus:ring-2 focus:ring-djajan-primary/10 outline-none transition resize-none"
                    >{{ old('deskripsi') }}</textarea>


                    @error('deskripsi')

                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- GAMBAR PRODUK --}}
        {{-- ========================================================= --}}

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">

            {{-- Section Header --}}
            <div class="px-6 py-4 border-b border-gray-100">

                <h2 class="text-sm font-semibold text-djajan-neutral font-heading">
                    Gambar Produk
                </h2>

                <p class="text-xs text-gray-500 mt-0.5">
                    Tambahkan gambar produk agar lebih mudah dikenali pelanggan.
                </p>

            </div>


            <div class="p-6">

                {{-- Upload Area --}}
                <div
                    id="upload-area"
                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-djajan-primary transition-colors bg-gray-50 cursor-pointer group"
                >

                    <div class="space-y-1 text-center pointer-events-none">

                        {{-- Icon --}}
                        <svg
                            class="mx-auto h-10 w-10 text-gray-400 group-hover:text-djajan-primary transition-colors"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-10h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />

                        </svg>


                        <div class="text-sm text-gray-600">

                            <span class="font-medium text-djajan-primary">
                                Pilih gambar
                            </span>

                            <span>
                                atau drag and drop
                            </span>

                        </div>


                        <p class="text-xs text-gray-500">
                            PNG, JPG, WEBP up to 2MB
                        </p>

                    </div>


                    {{-- File Input --}}
                    <input
                        id="gambar"
                        name="gambar"
                        type="file"
                        class="hidden"
                        accept="image/jpeg,image/png,image/webp"
                    >

                </div>


                {{-- ================================================= --}}
                {{-- IMAGE PREVIEW --}}
                {{-- ================================================= --}}

                <div
                    id="image-preview"
                    class="hidden mt-4"
                >

                    <p class="text-sm font-medium text-gray-700 mb-2">
                        Preview Gambar
                    </p>


                    <div class="relative max-w-md rounded-lg overflow-hidden border border-gray-200">

                        <img
                            id="preview-img"
                            src=""
                            alt="Preview gambar produk"
                            class="w-full h-56 object-cover"
                        >


                        {{-- Remove Preview --}}
                        <button
                            type="button"
                            id="btn-remove-image"
                            class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors shadow-sm"
                            title="Hapus gambar"
                        >

                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />

                            </svg>

                        </button>

                    </div>

                </div>


                @error('gambar')

                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- STATUS PRODUK --}}
        {{-- ========================================================= --}}

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">

            {{-- Section Header --}}
            <div class="px-6 py-4 border-b border-gray-100">

                <h2 class="text-sm font-semibold text-djajan-neutral font-heading">
                    Status Produk
                </h2>

                <p class="text-xs text-gray-500 mt-0.5">
                    Tentukan apakah produk langsung ditampilkan kepada pelanggan.
                </p>

            </div>


            <div class="p-6">

                <label
                    for="status"
                    class="block text-sm font-medium text-gray-700 mb-1.5"
                >
                    Status
                    <span class="text-red-500">*</span>
                </label>


                <select
                    id="status"
                    name="status"
                    required
                    class="w-full md:w-1/2 rounded-lg border border-gray-300 px-3 py-2.5 text-sm bg-white focus:border-djajan-primary focus:ring-2 focus:ring-djajan-primary/10 outline-none transition"
                >

                    <option
                        value="aktif"
                        {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}
                    >
                        Aktif
                    </option>

                    <option
                        value="nonaktif"
                        {{ old('status') == 'nonaktif' ? 'selected' : '' }}
                    >
                        Nonaktif
                    </option>

                </select>


                @error('status')

                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- ACTION BUTTON --}}
        {{-- ========================================================= --}}

        <div class="flex items-center justify-between pb-8">

            {{-- Batal --}}
            <a
                href="{{ route('admin.umkm.edit', $umkm->id) }}"
                class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition"
            >
                Batal
            </a>


            {{-- Simpan --}}
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-djajan-primary text-white text-sm font-medium hover:bg-djajan-primary/90 transition shadow-sm"
            >

                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />

                </svg>

                Simpan Produk

            </button>

        </div>

    </form>

</div>

@endsection


{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Elemen upload
    |--------------------------------------------------------------------------
    */

    const uploadArea = document.getElementById('upload-area');
    const gambarInput = document.getElementById('gambar');

    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const btnRemoveImage = document.getElementById('btn-remove-image');


    /*
    |--------------------------------------------------------------------------
    | Klik area upload
    |--------------------------------------------------------------------------
    |
    | Karena input file disembunyikan, kita membuat seluruh area upload
    | menjadi tombol untuk membuka file picker.
    |
    */

    if (uploadArea && gambarInput) {

        uploadArea.addEventListener('click', function () {

            gambarInput.click();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Pilih gambar
    |--------------------------------------------------------------------------
    |
    | Setelah user memilih file:
    | 1. Ambil file
    | 2. Baca file menggunakan FileReader
    | 3. Tampilkan preview
    |
    */

    if (gambarInput) {

        gambarInput.addEventListener('change', function (event) {

            const file = event.target.files[0];


            if (!file) {
                return;
            }


            const reader = new FileReader();


            reader.onload = function (event) {

                previewImg.src = event.target.result;

                imagePreview.classList.remove('hidden');

            };


            reader.readAsDataURL(file);

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Hapus gambar yang dipilih
    |--------------------------------------------------------------------------
    */

    if (btnRemoveImage) {

        btnRemoveImage.addEventListener('click', function (event) {

            event.stopPropagation();

            gambarInput.value = '';

            previewImg.src = '';

            imagePreview.classList.add('hidden');

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Character Counter
    |--------------------------------------------------------------------------
    */

    const deskripsi = document.getElementById('deskripsi');
    const charCount = document.getElementById('char-count');


    if (deskripsi && charCount) {

        function updateCharCount() {

            charCount.textContent = deskripsi.value.length;

        }


        deskripsi.addEventListener(
            'input',
            updateCharCount
        );


        updateCharCount();

    }

});

</script>

@endpush