@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a
            href="{{ route('admin.products.index') }}"
            class="hover:text-[#0F623F]"
        >
            Data Produk
        </a>

        <span>/</span>

        <span class="text-gray-700">
            Tambah Produk
        </span>
    </div>


    {{-- Header --}}
    <div>
        <p class="text-sm text-gray-500">
            Manajemen Data
        </p>

        <h1 class="text-2xl font-semibold text-gray-900">
            Tambah Produk
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Tambahkan produk baru ke salah satu UMKM yang terdaftar.
        </p>
    </div>


    {{-- Validation Error --}}
    @if($errors->any())

        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3">

            <p class="text-sm font-medium text-red-700">
                Terdapat kesalahan pada data yang dimasukkan.
            </p>

            <ul class="mt-2 list-disc pl-5 text-sm text-red-600">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    {{-- Form --}}
    <form
        action="{{ route('admin.products.storeStandalone') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf


        {{-- Informasi Produk --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">

            <div class="mb-6">
                <h2 class="text-base font-semibold text-gray-900">
                    Informasi Produk
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Masukkan informasi utama produk.
                </p>
            </div>


            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                {{-- UMKM --}}
                <div class="md:col-span-2">

                    <label
                        for="umkm_id"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        UMKM <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="umkm_id"
                        name="umkm_id"
                        required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-[#0F623F] focus:ring-1 focus:ring-[#0F623F]"
                    >

                        <option value="">
                            Pilih UMKM
                        </option>

                        @foreach($umkms as $umkm)

                            <option
                                value="{{ $umkm->id }}"
                                {{ old('umkm_id') == $umkm->id ? 'selected' : '' }}
                            >
                                {{ $umkm->nama_umkm }}
                            </option>

                        @endforeach

                    </select>

                    @error('umkm_id')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Nama Produk --}}
                <div>

                    <label
                        for="nama_produk"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Nama Produk <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="nama_produk"
                        name="nama_produk"
                        value="{{ old('nama_produk') }}"
                        placeholder="Contoh: Nasi Goreng"
                        required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-[#0F623F] focus:ring-1 focus:ring-[#0F623F]"
                    >

                    @error('nama_produk')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Kategori --}}
                <div>

                    <label
                        for="kategori"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Kategori
                    </label>

                    <input
                        type="text"
                        id="kategori"
                        name="kategori"
                        value="{{ old('kategori') }}"
                        placeholder="Contoh: Makanan"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-[#0F623F] focus:ring-1 focus:ring-[#0F623F]"
                    >

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
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Harga <span class="text-red-500">*</span>
                    </label>

                    <div class="flex">

                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">
                            Rp
                        </span>

                        <input
                            type="number"
                            id="harga"
                            name="harga"
                            value="{{ old('harga') }}"
                            min="0"
                            step="1"
                            placeholder="15000"
                            required
                            class="w-full rounded-r-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-[#0F623F] focus:ring-1 focus:ring-[#0F623F]"
                        >

                    </div>

                    @error('harga')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Status --}}
                <div>

                    <label
                        for="status"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Status <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-[#0F623F] focus:ring-1 focus:ring-[#0F623F]"
                    >

                        <option
                            value="aktif"
                            {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}
                        >
                            Aktif
                        </option>

                        <option
                            value="nonaktif"
                            {{ old('status') === 'nonaktif' ? 'selected' : '' }}
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


                {{-- Deskripsi --}}
                <div class="md:col-span-2">

                    <label
                        for="deskripsi"
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Deskripsi
                    </label>

                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        rows="5"
                        placeholder="Jelaskan produk secara singkat..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-[#0F623F] focus:ring-1 focus:ring-[#0F623F]"
                    >{{ old('deskripsi') }}</textarea>

                    @error('deskripsi')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>


        {{-- Gambar --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">

            <div class="mb-6">

                <h2 class="text-base font-semibold text-gray-900">
                    Gambar Produk
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Gunakan gambar JPG, JPEG, PNG, atau WEBP maksimal 2 MB.
                </p>

            </div>


            <input
                type="file"
                name="gambar"
                accept=".jpg,.jpeg,.png,.webp"
                class="block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-600 file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200"
            >

            @error('gambar')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- Action --}}
        <div class="flex items-center justify-end gap-3">

            <a
                href="{{ route('admin.products.index') }}"
                class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Batal
            </a>

            <button
                type="submit"
                class="rounded-lg bg-[#0F623F] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#0c5134]"
            >
                Simpan Produk
            </button>

        </div>

    </form>

</div>

@endsection