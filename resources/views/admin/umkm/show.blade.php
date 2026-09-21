@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <a
                    href="{{ route('admin.umkm.index') }}"
                    class="text-sm text-gray-500 hover:text-[#0F623F] transition-colors"
                >
                    Data UMKM
                </a>

                <span class="text-gray-300">/</span>

                <span class="text-sm text-gray-700">
                    Detail
                </span>
            </div>

            <h1 class="text-2xl font-semibold text-gray-900">
                Detail UMKM
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Informasi lengkap mengenai UMKM dan produk yang dimiliki.
            </p>
        </div>

        <a
            href="{{ route('admin.umkm.edit', $umkm->id) }}"
            class="inline-flex items-center gap-2 rounded-lg bg-[#0F623F] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#0B5134] transition-colors"
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
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                />
            </svg>

            Edit UMKM
        </a>
    </div>


    {{-- Informasi Utama --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">

        <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr]">

            {{-- Gambar --}}
            <div class="bg-gray-50 border-b lg:border-b-0 lg:border-r border-gray-200 p-6">

                @if($umkm->gambar)

                    <img
                        src="{{ asset('storage/' . $umkm->gambar) }}"
                        alt="{{ $umkm->nama_umkm }}"
                        class="w-full aspect-square object-cover rounded-xl"
                    >

                @else

                    <div class="w-full aspect-square rounded-xl bg-gray-100 flex items-center justify-center">
                        <span class="text-sm text-gray-400">
                            Tidak ada gambar
                        </span>
                    </div>

                @endif

            </div>


            {{-- Informasi --}}
            <div class="p-6">

                <div class="flex flex-wrap items-start justify-between gap-4">

                    <div>
                        <div class="flex items-center gap-2 flex-wrap">

                            <h2 class="text-xl font-semibold text-gray-900">
                                {{ $umkm->nama_umkm }}
                            </h2>

                            @if($umkm->unggulan)
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                    UMKM Unggulan
                                </span>
                            @endif

                        </div>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $umkm->kategori }}
                        </p>
                    </div>


                    {{-- Status --}}
                    @if($umkm->status_publikasi === 'aktif')

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>

                    @else

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Nonaktif
                        </span>

                    @endif

                </div>


                {{-- Deskripsi --}}
                <div class="mt-6">

                    <h3 class="text-sm font-semibold text-gray-900">
                        Deskripsi
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-gray-600">
                        {{ $umkm->deskripsi ?: 'Belum ada deskripsi UMKM.' }}
                    </p>

                </div>


                {{-- Detail Usaha --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Tahun Berdiri
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-800">
                            {{ $umkm->tahun_berdiri ?: '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Jumlah Produk
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-800">
                            {{ $umkm->products->count() }} Produk
                        </p>
                    </div>


                    <div class="sm:col-span-2">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Alamat
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-800">
                            {{ $umkm->alamat }}
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Data Pemilik --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="px-6 py-5 border-b border-gray-100">

            <h2 class="text-base font-semibold text-gray-900">
                Data Pemilik
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Informasi pemilik UMKM.
            </p>

        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6">

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                    Nama Pemilik
                </p>

                <p class="mt-1 text-sm font-medium text-gray-800">
                    {{ $umkm->nama_pemilik }}
                </p>
            </div>


            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                    Nomor Telepon
                </p>

                <p class="mt-1 text-sm font-medium text-gray-800">
                    {{ $umkm->no_telepon }}
                </p>
            </div>

        </div>

    </div>


    {{-- Produk UMKM --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">

            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    Produk UMKM
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Daftar produk yang dimiliki oleh UMKM ini.
                </p>
            </div>

            <a
                href="{{ route('admin.products.create', $umkm->id) }}"
                class="text-sm font-medium text-[#0F623F] hover:text-[#0B5134]"
            >
                + Tambah Produk
            </a>

        </div>


        @if($umkm->products->count())

            <div class="divide-y divide-gray-100">

                @foreach($umkm->products as $product)

                    <div class="flex items-center gap-4 px-6 py-4">

                        {{-- Gambar Produk --}}
                        <div class="w-16 h-16 flex-shrink-0">

                            @if($product->gambar)

                                <img
                                    src="{{ asset('storage/' . $product->gambar) }}"
                                    alt="{{ $product->nama_produk }}"
                                    class="w-full h-full object-cover rounded-lg"
                                >

                            @else

                                <div class="w-full h-full rounded-lg bg-gray-100 flex items-center justify-center">
                                    <span class="text-xs text-gray-400">
                                        No Image
                                    </span>
                                </div>

                            @endif

                        </div>


                        {{-- Informasi Produk --}}
                        <div class="min-w-0 flex-1">

                            <h3 class="text-sm font-semibold text-gray-900">
                                {{ $product->nama_produk }}
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $product->kategori ?: 'Tanpa kategori' }}
                            </p>

                        </div>


                        {{-- Harga --}}
                        <div class="text-right">

                            <p class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($product->harga, 0, ',', '.') }}
                            </p>

                            @if($product->status === 'aktif')

                                <span class="text-xs text-emerald-600">
                                    Aktif
                                </span>

                            @else

                                <span class="text-xs text-gray-400">
                                    Nonaktif
                                </span>

                            @endif

                        </div>


                        {{-- Edit Produk --}}
                        <a
                            href="{{ route('admin.products.edit', $product->id) }}"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-[#0F623F] hover:bg-green-50 transition-colors"
                            title="Edit Produk"
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
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                                />
                            </svg>
                        </a>

                    </div>

                @endforeach

            </div>

        @else

            <div class="px-6 py-12 text-center">

                <p class="text-sm font-medium text-gray-700">
                    Belum ada produk
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    UMKM ini belum memiliki produk yang terdaftar.
                </p>

                <a
                    href="{{ route('admin.products.create', $umkm->id) }}"
                    class="inline-flex mt-4 text-sm font-medium text-[#0F623F] hover:text-[#0B5134]"
                >
                    Tambah Produk
                </a>

            </div>

        @endif

    </div>

</div>

@endsection