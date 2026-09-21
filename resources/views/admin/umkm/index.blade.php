@extends('layouts.admin')

@section('title', 'Data UMKM - Djajan')

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

        <!-- Search Bar (Global) -->
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

    <!-- Right Actions -->
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
            <div class="w-9 h-9 bg-djajan-primary text-white rounded-full flex items-center justify-center font-semibold text-sm ring-2 ring-white shadow-sm">
                A
            </div>
        </div>
    </div>
</header>

<!-- MAIN CONTENT -->
<main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">

    <!-- PAGE HEADER -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Data UMKM</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola data usaha dan UMKM yang terdaftar di Djajan.</p>
            </div>
            <a href="{{ route('admin.umkm.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-djajan-primary text-white text-sm font-medium rounded-lg hover:bg-djajan-primary/90 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah UMKM
            </a>
        </div>
    </div>

    <!-- SEARCH AND FILTER TOOLBAR -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6 shadow-sm">
        <div class="flex flex-col md:flex-row gap-3">

            <!-- Search Field -->
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" placeholder="Cari nama UMKM atau pemilik..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary">
            </div>

            <!-- Category Filter -->
            <div class="w-full md:w-48">
                <select class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary appearance-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                    <option value="kerajinan">Kerajinan</option>
                    <option value="jasa">Jasa</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-48">
                <select class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="tidak_aktif">Tidak Aktif</option>
                </select>
            </div>

        </div>
    </div>

    <!-- UMKM TABLE -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            {{-- Success Alert --}}
            @if(session('success'))
            <div class="px-5 pt-5">
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                    <p class="text-sm font-medium text-green-700">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
            @endif

            <div class="overflow-x-auto">

                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200">
                            <th class="text-left py-3.5 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">No.</th>
                            <th class="text-left py-3.5 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama UMKM</th>
                            <th class="text-left py-3.5 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemilik</th>
                            <th class="text-left py-3.5 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="text-left py-3.5 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Alamat</th>
                            <th class="text-left py-3.5 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-right py-3.5 px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                        @forelse($umkms as $index => $umkm)

                        <tr class="hover:bg-gray-50/50 transition-colors">

                            <!-- No -->
                            <td class="py-4 px-5 text-gray-500">
                                {{ $umkms->firstItem() + $index }}
                            </td>

                            <!-- Nama UMKM -->
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">

                                    <div class="w-9 h-9 rounded-lg bg-djajan-primary/10 text-djajan-primary flex items-center justify-center text-xs font-bold flex-shrink-0 font-heading">
                                        {{ strtoupper(substr($umkm->nama_umkm, 0, 2)) }}
                                    </div>

                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $umkm->nama_umkm }}
                                        </p>

                                        <p class="text-xs text-gray-400">
                                            ID: UMKM-{{ str_pad($umkm->id, 3, '0', STR_PAD_LEFT) }}
                                        </p>
                                    </div>

                                </div>
                            </td>

                            <!-- Pemilik -->
                            <td class="py-4 px-5 text-gray-700">
                                {{ $umkm->nama_pemilik }}
                            </td>

                            <!-- Kategori -->
                            <td class="py-4 px-5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $umkm->kategori }}
                                </span>
                            </td>

                            <!-- Alamat -->
                            <td class="py-4 px-5 text-gray-600 hidden lg:table-cell">
                                {{ $umkm->alamat }}
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-5">

                                @if($umkm->status_publikasi === 'aktif')

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200/50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Aktif
                                </span>

                                @else

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Tidak Aktif
                                </span>

                                @endif

                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">

                                    {{-- Lihat --}}
                                    <a
                                        href="{{ route('admin.umkm.show', $umkm->id) }}"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-[#0F623F] hover:bg-green-50 transition-colors"
                                        title="Lihat Detail">
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M2.25 12s3.5-6.75 9.75-6.75S21.75 12 21.75 12 18.25 18.75 12 18.75 2.25 12 2.25 12z"
                                                stroke-width="2" />
                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="2.75"
                                                stroke-width="2" />
                                        </svg>
                                    </a>


                                    {{-- Edit --}}
                                    <a
                                        href="{{ route('admin.umkm.edit', $umkm->id) }}"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-[#0F623F] hover:bg-green-50 transition-colors"
                                        title="Edit">
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </a>


                                    {{-- Hapus --}}
                                    <form
                                        action="{{ route('admin.umkm.destroy', $umkm->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus UMKM ini? Data yang sudah dihapus tidak dapat dikembalikan.');">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors"
                                            title="Hapus">
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6h14z" />
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="7" class="py-12 text-center">

                                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="text-gray-400">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                    </svg>
                                </div>

                                <h3 class="text-base font-semibold text-gray-900 mb-1">
                                    Belum ada data UMKM
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Tambahkan UMKM pertama untuk mulai mengelola data usaha.
                                </p>

                            </td>
                        </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            @if($umkms->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">

                {{-- Informasi jumlah data --}}
                <div class="text-sm text-gray-500">
                    Menampilkan
                    <span class="font-medium text-gray-900">
                        {{ $umkms->firstItem() }}
                    </span>
                    sampai
                    <span class="font-medium text-gray-900">
                        {{ $umkms->lastItem() }}
                    </span>
                    dari
                    <span class="font-medium text-gray-900">
                        {{ $umkms->total() }}
                    </span>
                    data
                </div>


                {{-- Navigasi --}}
                <div class="flex items-center gap-1">

                    {{-- Sebelumnya --}}
                    @if($umkms->onFirstPage())

                    <span class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                        Sebelumnya
                    </span>

                    @else

                    <a
                        href="{{ $umkms->previousPageUrl() }}"
                        class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        Sebelumnya
                    </a>

                    @endif


                    {{-- Nomor Halaman --}}
                    @foreach($umkms->getUrlRange(1, $umkms->lastPage()) as $page => $url)

                    @if($page == $umkms->currentPage())

                    <span class="px-3 py-1.5 text-sm font-medium text-white bg-djajan-primary border border-djajan-primary rounded-lg">
                        {{ $page }}
                    </span>

                    @else

                    <a
                        href="{{ $url }}"
                        class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ $page }}
                    </a>

                    @endif

                    @endforeach


                    {{-- Berikutnya --}}
                    @if($umkms->hasMorePages())

                    <a
                        href="{{ $umkms->nextPageUrl() }}"
                        class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        Berikutnya
                    </a>

                    @else

                    <span class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                        Berikutnya
                    </span>

                    @endif

                </div>

            </div>
            @endif
        </div>

        <!-- EMPTY STATE (Hidden by default, shown when no data exists) -->
        <!-- 
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada data UMKM.</h3>
            <p class="text-sm text-gray-500 mb-6">Tambahkan UMKM pertama untuk mulai mengelola data usaha.</p>
            <a href="#" class="inline-flex items-center gap-2 px-4 py-2.5 bg-djajan-primary text-white text-sm font-medium rounded-lg hover:bg-djajan-primary/90 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah UMKM
            </a>
        </div>
        -->

</main>

@endsection