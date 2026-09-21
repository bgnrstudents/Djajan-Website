@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.products.index') }}" class="hover:text-[#0F623F]">
            Data Produk
        </a>

        <span>/</span>

        <span class="text-gray-700">
            Detail Produk
        </span>
    </div>


    {{-- Header --}}
    <div class="flex items-center justify-between">

        <div>
            <p class="text-sm text-gray-500">
                Informasi Produk
            </p>

            <h1 class="text-2xl font-semibold text-gray-900">
                Detail Produk
            </h1>
        </div>

        <div class="flex items-center gap-2">

            <a
                href="{{ route('admin.products.edit', $product) }}"
                class="rounded-lg bg-[#0F623F] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#0c5134]"
            >
                Edit Produk
            </a>

            <a
                href="{{ route('admin.products.index') }}"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Kembali
            </a>

        </div>

    </div>


    {{-- Main Information --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Image --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5">

            <div class="aspect-square overflow-hidden rounded-lg bg-gray-100">

                @if($product->gambar)

                    <img
                        src="{{ asset('storage/' . $product->gambar) }}"
                        alt="{{ $product->nama_produk }}"
                        class="h-full w-full object-cover"
                    >

                @else

                    <div class="flex h-full items-center justify-center text-sm text-gray-400">
                        Tidak ada gambar
                    </div>

                @endif

            </div>

        </div>


        {{-- Product Info --}}
        <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-6">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-sm text-gray-500">
                        Nama Produk
                    </p>

                    <h2 class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ $product->nama_produk }}
                    </h2>
                </div>


                @if($product->status === 'aktif')

                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                        Aktif
                    </span>

                @else

                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                        Nonaktif
                    </span>

                @endif

            </div>


            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Harga
                    </p>

                    <p class="mt-1 text-lg font-semibold text-[#0F623F]">
                        Rp {{ number_format($product->harga, 0, ',', '.') }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Kategori
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        {{ $product->kategori ?: '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        UMKM
                    </p>

                    <a
                        href="{{ route('admin.umkm.show', $product->umkm) }}"
                        class="mt-1 inline-block text-sm font-medium text-[#0F623F] hover:underline"
                    >
                        {{ $product->umkm->nama_umkm ?? '-' }}
                    </a>
                </div>


                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        ID Produk
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        #{{ $product->id }}
                    </p>
                </div>

            </div>


            {{-- Description --}}
            <div class="mt-7 border-t border-gray-100 pt-6">

                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                    Deskripsi
                </p>

                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600">
                    {{ $product->deskripsi ?: 'Belum ada deskripsi produk.' }}
                </p>

            </div>

        </div>

    </div>


    {{-- UMKM Information --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-sm text-gray-500">
                    Pemilik Produk
                </p>

                <h2 class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $product->umkm->nama_umkm ?? '-' }}
                </h2>
            </div>

            @if($product->umkm)

                <a
                    href="{{ route('admin.umkm.show', $product->umkm) }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Lihat UMKM
                </a>

            @endif

        </div>


        @if($product->umkm)

            <div class="mt-5 grid grid-cols-1 gap-5 border-t border-gray-100 pt-5 sm:grid-cols-2">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Nama Pemilik
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        {{ $product->umkm->nama_pemilik }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Nomor Telepon
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        {{ $product->umkm->no_telepon }}
                    </p>
                </div>


                <div class="sm:col-span-2">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Alamat UMKM
                    </p>

                    <p class="mt-1 text-sm leading-6 text-gray-700">
                        {{ $product->umkm->alamat }}
                    </p>
                </div>

            </div>

        @endif

    </div>

</div>

@endsection