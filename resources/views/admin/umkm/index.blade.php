@extends('layouts.admin')

@section('title', 'Data UMKM - Djajan')

@section('content')

<!-- PAGE HEADER -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Data UMKM</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data usaha dan UMKM yang terdaftar di Djajan.</p>
        </div>
        <a href="{{ route('admin.umkm.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-djajan-primary text-white text-sm font-medium rounded-lg hover:bg-djajan-primary-hover transition-colors shadow-sm">
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
            <input type="text" placeholder="Cari nama UMKM atau pemilik..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors">
        </div>

        <!-- Category Filter -->
        <div class="w-full md:w-48">
            <select class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary cursor-pointer">
                <option value="">Semua Kategori</option>
                <option value="makanan">Makanan & Katering</option>
                <option value="minuman">Minuman</option>
                <option value="kerajinan">Kerajinan</option>
                <option value="jasa">Jasa</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div class="w-full md:w-48">
            <select class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary cursor-pointer">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="tidak_aktif">Tidak Aktif</option>
            </select>
        </div>
    </div>
</div>

<!-- UMKM TABLE CONTAINER -->
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- Success Alert --}}
    @if(session('success'))
    <div class="p-4 border-b border-gray-100 bg-emerald-50/60">
        <div class="flex items-center justify-between gap-3 text-emerald-800 text-sm">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 flex-shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="py-3.5 px-5 w-16 text-center">No.</th>
                    <th class="py-3.5 px-5">Nama UMKM</th>
                    <th class="py-3.5 px-5">Pemilik</th>
                    <th class="py-3.5 px-5">Kategori</th>
                    <th class="py-3.5 px-5 hidden lg:table-cell">Alamat</th>
                    <th class="py-3.5 px-5">Status</th>
                    <th class="py-3.5 px-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">

                @forelse($umkms as $index => $umkm)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <!-- No -->
                    <td class="py-4 px-5 text-center text-gray-500 font-mono text-xs">
                        {{ $umkms->firstItem() + $index }}
                    </td>

                    <!-- Nama UMKM -->
                    <td class="py-4 px-5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-djajan-primary/10 text-djajan-primary flex items-center justify-center text-xs font-bold flex-shrink-0 font-heading">
                                {{ strtoupper(substr($umkm->nama_umkm, 0, 2)) }}
                            </div>
                            <div>
                                <a href="{{ route('admin.umkm.show', $umkm->id) }}" class="font-medium text-gray-900 hover:text-djajan-primary transition-colors">
                                    {{ $umkm->nama_umkm }}
                                </a>
                                <p class="text-xs text-gray-400">
                                    ID: UMKM-{{ str_pad($umkm->id, 3, '0', STR_PAD_LEFT) }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <!-- Pemilik -->
                    <td class="py-4 px-5 text-gray-700 font-medium">
                        {{ $umkm->nama_pemilik }}
                    </td>

                    <!-- Kategori -->
                    <td class="py-4 px-5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                            {{ $umkm->kategori }}
                        </span>
                    </td>

                    <!-- Alamat -->
                    <td class="py-4 px-5 text-gray-500 hidden lg:table-cell max-w-xs truncate">
                        {{ $umkm->alamat }}
                    </td>

                    <!-- Status -->
                    <td class="py-4 px-5 whitespace-nowrap">
                        @if($umkm->status_publikasi === 'aktif')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Tidak Aktif
                        </span>
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td class="py-4 px-5 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            {{-- Lihat --}}
                            <a href="{{ route('admin.umkm.show', $umkm->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-djajan-primary hover:bg-djajan-primary/10 transition-colors" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('admin.umkm.edit', $umkm->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-djajan-primary hover:bg-djajan-primary/10 transition-colors" title="Edit Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                            </a>

                            {{-- Hapus --}}
                            <form action="{{ route('admin.umkm.destroy', $umkm->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus UMKM {{ $umkm->nama_umkm }}? Data yang sudah dihapus tidak dapat dikembalikan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus UMKM">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 px-4 text-center">
                        <div class="max-w-sm mx-auto">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1 font-heading">Belum Ada Data UMKM</h3>
                            <p class="text-sm text-gray-500 mb-5">Tambahkan UMKM pertama untuk mulai mengelola data usaha desa.</p>
                            <a href="{{ route('admin.umkm.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-djajan-primary text-white text-sm font-medium rounded-lg hover:bg-djajan-primary-hover transition-colors shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Tambah UMKM
                            </a>
                        </div>
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
            <span class="font-semibold text-gray-900">{{ $umkms->firstItem() }}</span>
            sampai
            <span class="font-semibold text-gray-900">{{ $umkms->lastItem() }}</span>
            dari
            <span class="font-semibold text-gray-900">{{ $umkms->total() }}</span>
            data
        </div>

        {{-- Navigasi --}}
        <div class="flex items-center gap-1.5">
            {{-- Sebelumnya --}}
            @if($umkms->onFirstPage())
            <span class="px-3 py-1.5 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                Sebelumnya
            </span>
            @else
            <a href="{{ $umkms->previousPageUrl() }}" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Sebelumnya
            </a>
            @endif

            {{-- Nomor Halaman --}}
            @foreach($umkms->getUrlRange(1, $umkms->lastPage()) as $page => $url)
            @if($page == $umkms->currentPage())
            <span class="px-3 py-1.5 text-xs font-semibold text-white bg-djajan-primary border border-djajan-primary rounded-lg">
                {{ $page }}
            </span>
            @else
            <a href="{{ $url }}" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                {{ $page }}
            </a>
            @endif
            @endforeach

            {{-- Berikutnya --}}
            @if($umkms->hasMorePages())
            <a href="{{ $umkms->nextPageUrl() }}" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Berikutnya
            </a>
            @else
            <span class="px-3 py-1.5 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                Berikutnya
            </span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection