@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">
                Manajemen Data
            </p>

            <h1 class="text-2xl font-semibold text-gray-900">
                Data Produk
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Kelola seluruh produk UMKM yang terdaftar di Djajan.
            </p>
        </div>

        <a
            href="{{ route('admin.products.createStandalone') }}"
            class="inline-flex items-center rounded-lg bg-[#0F623F] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#0c5134]">
            Tambah Produk
        </a>
    </div>


    {{-- Success Message --}}
    @if(session('success'))
    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
    @endif


    {{-- Search & Filter --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5">

        <form
            method="GET"
            action="{{ route('admin.products.index') }}"
            class="grid grid-cols-1 gap-4 md:grid-cols-4">

            {{-- Search --}}
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Cari Produk
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama produk..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-[#0F623F] focus:ring-1 focus:ring-[#0F623F]">
            </div>


            {{-- UMKM --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    UMKM
                </label>

                <select
                    name="umkm_id"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-[#0F623F] focus:ring-1 focus:ring-[#0F623F]">
                    <option value="">Semua UMKM</option>

                    @foreach($umkms as $umkm)
                    <option
                        value="{{ $umkm->id }}"
                        {{ request('umkm_id') == $umkm->id ? 'selected' : '' }}>
                        {{ $umkm->nama_umkm }}
                    </option>
                    @endforeach
                </select>
            </div>


            {{-- Status --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-[#0F623F] focus:ring-1 focus:ring-[#0F623F]">
                    <option value="">Semua Status</option>

                    <option
                        value="aktif"
                        {{ request('status') === 'aktif' ? 'selected' : '' }}>
                        Aktif
                    </option>

                    <option
                        value="nonaktif"
                        {{ request('status') === 'nonaktif' ? 'selected' : '' }}>
                        Nonaktif
                    </option>
                </select>
            </div>


            {{-- Buttons --}}
            <div class="md:col-span-4 flex gap-2">
                <button
                    type="submit"
                    class="rounded-lg bg-[#0F623F] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#0c5134]">
                    Terapkan Filter
                </button>

                <a
                    href="{{ route('admin.products.index') }}"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Reset
                </a>
            </div>

        </form>

    </div>


    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-gray-600">
                            No
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-600">
                            Produk
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-600">
                            UMKM
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-600">
                            Kategori
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-600">
                            Harga
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-600">
                            Status
                        </th>

                        <th class="px-5 py-3 text-center font-medium text-gray-600">
                            Aksi
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse($products as $index => $product)

                    <tr class="hover:bg-gray-50">

                        <td class="px-5 py-4 text-gray-500">
                            {{ $products->firstItem() + $index }}
                        </td>


                        {{-- Product --}}
                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                @if($product->gambar)

                                <img
                                    src="{{ asset('storage/' . $product->gambar) }}"
                                    alt="{{ $product->nama_produk }}"
                                    class="h-12 w-12 rounded-lg object-cover">

                                @else

                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400">
                                    No Image
                                </div>

                                @endif

                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $product->nama_produk }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        ID #{{ $product->id }}
                                    </p>
                                </div>

                            </div>

                        </td>


                        {{-- UMKM --}}
                        <td class="px-5 py-4 text-gray-700">
                            {{ $product->umkm->nama_umkm ?? '-' }}
                        </td>


                        {{-- Category --}}
                        <td class="px-5 py-4 text-gray-700">
                            {{ $product->kategori ?: '-' }}
                        </td>


                        {{-- Price --}}
                        <td class="px-5 py-4 font-medium text-gray-900">
                            Rp {{ number_format($product->harga, 0, ',', '.') }}
                        </td>


                        {{-- Status --}}
                        <td class="px-5 py-4">

                            @if($product->status === 'aktif')

                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                Aktif
                            </span>

                            @else

                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                Nonaktif
                            </span>

                            @endif

                        </td>


                        {{-- Actions --}}
                        <td class="px-5 py-4">

                            <div class="flex items-center justify-center gap-2">

                                <a
                                    href="{{ route('admin.products.show', $product) }}"
                                    class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">
                                    Detail
                                </a>

                                <a
                                    href="{{ route('admin.products.edit', $product) }}"
                                    class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">
                                    Edit
                                </a>

                                <form
                                    action="{{ route('admin.products.destroy', $product) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus produk ini? Data yang sudah dihapus tidak dapat dikembalikan.')">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">

                            <p class="text-sm font-medium text-gray-700">
                                Belum ada produk
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Data produk akan muncul setelah produk ditambahkan.
                            </p>

                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if($products->hasPages())

        <div class="flex items-center justify-between border-t border-gray-200 px-5 py-4">

            <p class="text-sm text-gray-500">
                Menampilkan
                {{ $products->firstItem() }}
                sampai
                {{ $products->lastItem() }}
                dari
                {{ $products->total() }}
                produk
            </p>

            <div class="flex items-center gap-1">

                @if($products->onFirstPage())

                <span class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-300">
                    Sebelumnya
                </span>

                @else

                <a
                    href="{{ $products->previousPageUrl() }}"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    Sebelumnya
                </a>

                @endif


                @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)

                @if($page == $products->currentPage())

                <span class="rounded-lg bg-[#0F623F] px-3 py-2 text-sm font-medium text-white">
                    {{ $page }}
                </span>

                @else

                <a
                    href="{{ $url }}"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    {{ $page }}
                </a>

                @endif

                @endforeach


                @if($products->hasMorePages())

                <a
                    href="{{ $products->nextPageUrl() }}"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    Berikutnya
                </a>

                @else

                <span class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-300">
                    Berikutnya
                </span>

                @endif

            </div>

        </div>

        @endif

    </div>

</div>

@endsection