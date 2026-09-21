@extends('layouts.admin')

@section('title', 'Edit Produk | Djajan')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="mb-6">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2 flex-wrap">

            <a
                href="{{ route('admin.umkm.edit', $product->umkm_id) }}"
                class="hover:text-djajan-primary transition-colors"
            >
                {{ $product->umkm->nama_umkm }}
            </a>

            <span>/</span>

            <span class="text-gray-700">
                Edit Produk
            </span>

        </div>


        {{-- Title --}}
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

            <div>

                <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">
                    Edit Produk
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi produk dari
                    <span class="font-medium text-gray-700">
                        {{ $product->umkm->nama_umkm }}
                    </span>.
                </p>

            </div>


            {{-- Kembali --}}
            <a
                href="{{ route('admin.umkm.edit', $product->umkm_id) }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>

                Kembali ke UMKM

            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- VALIDATION ERROR --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">

            <div class="flex items-start gap-3">

                <svg
                    class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>

                <div>

                    <p class="text-sm font-semibold text-red-700">
                        Terdapat kesalahan pada form.
                    </p>

                    <ul class="mt-2 text-sm text-red-600 list-disc list-inside space-y-1">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}

    <form
        action="{{ route('admin.products.update', $product->id) }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')


        {{-- ===================================================== --}}
        {{-- INFORMASI PRODUK --}}
        {{-- ===================================================== --}}

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">

            {{-- Header Section --}}
            <div class="px-6 py-4 border-b border-gray-100">

                <h2 class="text-sm font-semibold text-djajan-neutral font-heading">
                    Informasi Produk
                </h2>

                <p class="text-xs text-gray-500 mt-0.5">
                    Perbarui informasi dasar produk.
                </p>

            </div>


            {{-- Content --}}
            <div class="p-6 space-y-5">


                {{-- Nama Produk --}}
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
                        value="{{ old('nama_produk', $product->nama_produk) }}"
                        required
                        maxlength="255"
                        placeholder="Contoh: Nasi Pecel"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-900 focus:border-djajan-primary focus:ring-2 focus:ring-djajan-primary/10 outline-none transition @error('nama_produk') border-red-300 @enderror"
                    >


                    @error('nama_produk')

                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Kategori + Harga --}}
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
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm bg-white text-gray-900 focus:border-djajan-primary focus:ring-2 focus:ring-djajan-primary/10 outline-none transition @error('kategori') border-red-300 @enderror"
                        >

                            <option value="">
                                Pilih kategori
                            </option>


                            <option
                                value="Makanan"
                                {{ old('kategori', $product->kategori) === 'Makanan' ? 'selected' : '' }}
                            >
                                Makanan
                            </option>


                            <option
                                value="Minuman"
                                {{ old('kategori', $product->kategori) === 'Minuman' ? 'selected' : '' }}
                            >
                                Minuman
                            </option>


                            <option
                                value="Kerajinan"
                                {{ old('kategori', $product->kategori) === 'Kerajinan' ? 'selected' : '' }}
                            >
                                Kerajinan
                            </option>


                            <option
                                value="Jasa"
                                {{ old('kategori', $product->kategori) === 'Jasa' ? 'selected' : '' }}
                            >
                                Jasa
                            </option>


                            <option
                                value="Lainnya"
                                {{ old('kategori', $product->kategori) === 'Lainnya' ? 'selected' : '' }}
                            >
                                Lainnya
                            </option>

                        </select>


                        @error('kategori')

                            <p class="mt-1.5 text-xs text-red-600">
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
                                value="{{ old('harga', $product->harga) }}"
                                min="0"
                                step="100"
                                required
                                placeholder="10000"
                                class="w-full rounded-lg border border-gray-300 pl-10 pr-3 py-2.5 text-sm text-gray-900 focus:border-djajan-primary focus:ring-2 focus:ring-djajan-primary/10 outline-none transition @error('harga') border-red-300 @enderror"
                            >

                        </div>


                        @error('harga')

                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>


                {{-- Deskripsi --}}
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
                                {{ strlen(old('deskripsi', $product->deskripsi ?? '')) }}
                            </span>

                            /300

                        </span>

                    </div>


                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        rows="5"
                        maxlength="300"
                        placeholder="Jelaskan produk secara singkat..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-900 focus:border-djajan-primary focus:ring-2 focus:ring-djajan-primary/10 outline-none transition resize-none @error('deskripsi') border-red-300 @enderror"
                    >{{ old('deskripsi', $product->deskripsi) }}</textarea>


                    @error('deskripsi')

                        <p class="mt-1.5 text-xs text-red-600">
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


            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100">

                <h2 class="text-sm font-semibold text-djajan-neutral font-heading">
                    Gambar Produk
                </h2>

                <p class="text-xs text-gray-500 mt-0.5">
                    Gunakan gambar yang jelas dan sesuai dengan produk.
                </p>

            </div>


            <div class="p-6">


                {{-- ================================================= --}}
                {{-- GAMBAR SAAT INI --}}
                {{-- ================================================= --}}

                @if ($product->gambar)

                    <div
                        id="current-image-wrapper"
                        class="mb-5"
                    >

                        <p class="text-sm font-medium text-gray-700 mb-2">
                            Gambar Saat Ini
                        </p>


                        <div class="relative max-w-md rounded-lg overflow-hidden border border-gray-200 bg-gray-50">

                            <img
                                src="{{ asset('storage/' . $product->gambar) }}"
                                alt="{{ $product->nama_produk }}"
                                id="current-image"
                                class="w-full h-56 object-cover"
                            >

                        </div>


                        {{-- Checkbox hapus --}}
                        <label class="flex items-center gap-2 mt-3 cursor-pointer">

                            <input
                                type="checkbox"
                                name="hapus_gambar"
                                value="1"
                                id="hapus_gambar"
                                class="w-4 h-4 rounded border-gray-300 text-djajan-primary focus:ring-djajan-primary"
                            >

                            <span class="text-sm text-gray-600">
                                Hapus gambar saat ini
                            </span>

                        </label>

                    </div>

                @endif


                {{-- ================================================= --}}
                {{-- UPLOAD GAMBAR BARU --}}
                {{-- ================================================= --}}

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
                                Pilih gambar baru
                            </span>

                            <span>
                                atau drag and drop
                            </span>

                        </div>


                        <p class="text-xs text-gray-500">
                            PNG, JPG, WEBP hingga 2MB
                        </p>

                    </div>


                    <input
                        id="gambar"
                        name="gambar"
                        type="file"
                        class="hidden"
                        accept="image/jpeg,image/png,image/webp"
                    >

                </div>


                {{-- ================================================= --}}
                {{-- PREVIEW GAMBAR BARU --}}
                {{-- ================================================= --}}

                <div
                    id="image-preview"
                    class="hidden mt-4"
                >

                    <p class="text-sm font-medium text-gray-700 mb-2">
                        Gambar Baru
                    </p>


                    <div class="relative max-w-md rounded-lg overflow-hidden border border-gray-200">

                        <img
                            id="preview-img"
                            src=""
                            alt="Preview gambar baru"
                            class="w-full h-56 object-cover"
                        >


                        <button
                            type="button"
                            id="btn-remove-image"
                            class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors shadow-sm"
                            title="Batalkan gambar baru"
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
                                    d="M6 18L18 6M6 6l12-12"
                                />

                            </svg>

                        </button>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- STATUS PRODUK --}}
        {{-- ========================================================= --}}

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">


            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100">

                <h2 class="text-sm font-semibold text-djajan-neutral font-heading">
                    Status Produk
                </h2>

                <p class="text-xs text-gray-500 mt-0.5">
                    Atur apakah produk dapat ditampilkan kepada pengguna.
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
                    class="w-full md:w-1/2 rounded-lg border border-gray-300 px-3 py-2.5 text-sm bg-white text-gray-900 focus:border-djajan-primary focus:ring-2 focus:ring-djajan-primary/10 outline-none transition @error('status') border-red-300 @enderror"
                >

                    <option
                        value="aktif"
                        {{ old('status', $product->status) === 'aktif' ? 'selected' : '' }}
                    >
                        Aktif
                    </option>


                    <option
                        value="nonaktif"
                        {{ old('status', $product->status) === 'nonaktif' ? 'selected' : '' }}
                    >
                        Nonaktif
                    </option>

                </select>


                <p class="mt-1.5 text-xs text-gray-500">
                    Produk nonaktif tidak ditampilkan pada halaman publik.
                </p>


                @error('status')

                    <p class="mt-1.5 text-xs text-red-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FORM ACTION --}}
        {{-- ========================================================= --}}

        <div class="flex items-center justify-between pb-6">


            {{-- Batal --}}
            <a
                href="{{ route('admin.umkm.edit', $product->umkm_id) }}"
                class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
            >
                Batal
            </a>


            {{-- Simpan --}}
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-djajan-primary text-white text-sm font-medium hover:bg-djajan-primary/90 transition-colors shadow-sm"
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

                Simpan Perubahan

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
    | ELEMENT UPLOAD
    |--------------------------------------------------------------------------
    */

    const gambarInput = document.getElementById('gambar');
    const uploadArea = document.getElementById('upload-area');

    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    const btnRemoveImage = document.getElementById('btn-remove-image');

    const hapusGambar = document.getElementById('hapus_gambar');


    /*
    |--------------------------------------------------------------------------
    | KLIK AREA UPLOAD
    |--------------------------------------------------------------------------
    */

    if (uploadArea && gambarInput) {

        uploadArea.addEventListener('click', function () {

            gambarInput.click();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | PREVIEW GAMBAR BARU
    |--------------------------------------------------------------------------
    */

    if (gambarInput) {

        gambarInput.addEventListener('change', function (event) {

            const file = event.target.files[0];


            if (!file) {

                return;

            }


            /*
            | Validasi ukuran file di sisi client
            */

            if (file.size > 2 * 1024 * 1024) {

                alert('Ukuran gambar maksimal 2MB.');

                gambarInput.value = '';

                return;

            }


            /*
            | Preview gambar
            */

            const reader = new FileReader();


            reader.onload = function (event) {

                previewImg.src = event.target.result;

                imagePreview.classList.remove('hidden');

            };


            reader.readAsDataURL(file);


            /*
            | Jika upload gambar baru,
            | jangan hapus gambar lama.
            */

            if (hapusGambar) {

                hapusGambar.checked = false;

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS PILIHAN GAMBAR BARU
    |--------------------------------------------------------------------------
    */

    if (btnRemoveImage) {

        btnRemoveImage.addEventListener('click', function (event) {

            event.stopPropagation();


            if (gambarInput) {

                gambarInput.value = '';

            }


            imagePreview.classList.add('hidden');

            previewImg.src = '';

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CHARACTER COUNTER
    |--------------------------------------------------------------------------
    */

    const deskripsi = document.getElementById('deskripsi');
    const charCount = document.getElementById('char-count');


    if (deskripsi && charCount) {


        function updateCharCount() {

            const length = deskripsi.value.length;

            charCount.textContent = length;


            if (length >= 300) {

                charCount.classList.add('text-red-600');

            } else {

                charCount.classList.remove('text-red-600');

            }

        }


        deskripsi.addEventListener(
            'input',
            updateCharCount
        );


        updateCharCount();

    }


    /*
    |--------------------------------------------------------------------------
    | CHECKBOX HAPUS GAMBAR
    |--------------------------------------------------------------------------
    |
    | Jika user memilih "Hapus gambar saat ini",
    | kemudian memilih gambar baru, checkbox otomatis dibatalkan.
    |
    */

    if (gambarInput && hapusGambar) {

        gambarInput.addEventListener('change', function () {

            if (gambarInput.files.length > 0) {

                hapusGambar.checked = false;

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | DRAG & DROP
    |--------------------------------------------------------------------------
    */

    if (uploadArea && gambarInput) {


        uploadArea.addEventListener('dragover', function (event) {

            event.preventDefault();

            uploadArea.classList.add(
                'border-djajan-primary',
                'bg-green-50'
            );

        });


        uploadArea.addEventListener('dragleave', function () {

            uploadArea.classList.remove(
                'border-djajan-primary',
                'bg-green-50'
            );

        });


        uploadArea.addEventListener('drop', function (event) {

            event.preventDefault();


            uploadArea.classList.remove(
                'border-djajan-primary',
                'bg-green-50'
            );


            const files = event.dataTransfer.files;


            if (!files || files.length === 0) {

                return;

            }


            const file = files[0];


            /*
            | Pastikan file berupa gambar
            */

            if (!file.type.startsWith('image/')) {

                alert('File yang dipilih harus berupa gambar.');

                return;

            }


            /*
            | Validasi ukuran
            */

            if (file.size > 2 * 1024 * 1024) {

                alert('Ukuran gambar maksimal 2MB.');

                return;

            }


            /*
            | Masukkan file hasil drag & drop
            | ke input file.
            */

            try {

                const dataTransfer = new DataTransfer();

                dataTransfer.items.add(file);

                gambarInput.files = dataTransfer.files;

            } catch (error) {

                console.error(
                    'Gagal memasukkan file hasil drag & drop:',
                    error
                );

                return;

            }


            /*
            | Preview
            */

            const reader = new FileReader();


            reader.onload = function (event) {

                previewImg.src = event.target.result;

                imagePreview.classList.remove('hidden');

            };


            reader.readAsDataURL(file);


            /*
            | Batalkan checkbox hapus gambar lama
            */

            if (hapusGambar) {

                hapusGambar.checked = false;

            }

        });

    }

});

</script>

@endpush