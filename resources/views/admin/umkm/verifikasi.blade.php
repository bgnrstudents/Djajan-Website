@extends('layouts.admin')

@section('title', 'Verifikasi UMKM - Djajan')

@section('content')

<!-- PAGE HEADER -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Verifikasi UMKM</h1>
    <p class="text-sm text-gray-500 mt-1">Daftar pendaftaran akun UMKM yang masih menunggu persetujuan Anda.</p>
</div>

<!-- UMKM TABLE CONTAINER -->
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- Success / Info Alert --}}
    @if(session('success'))
    <div class="p-4 border-b border-gray-100 bg-emerald-50/60">
        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('info'))
    <div class="p-4 border-b border-gray-100 bg-blue-50/60">
        <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="py-3.5 px-5 w-16 text-center">No.</th>
                    <th class="py-3.5 px-5">Nama Usaha</th>
                    <th class="py-3.5 px-5">Kontak & Pemilik</th>
                    <th class="py-3.5 px-5 hidden lg:table-cell">Waktu Daftar</th>
                    <th class="py-3.5 px-5">Status</th>
                    <th class="py-3.5 px-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">

                @forelse($umkms as $index => $umkm)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="py-4 px-5 text-center text-gray-500 font-mono text-xs">
                        {{ $umkms->firstItem() + $index }}
                    </td>

                    <td class="py-4 px-5">
                        <div class="flex items-center gap-3">
                            <div class="relative w-10 h-10 rounded-lg bg-djajan-primary/10 text-djajan-primary flex items-center justify-center text-xs font-bold flex-shrink-0 font-heading overflow-hidden">
                                {{ strtoupper(substr($umkm->nama_usaha, 0, 2)) }}
                                @if($umkm->foto_profil)
                                <img src="{{ $umkm->urlFotoProfil() }}" alt="{{ $umkm->nama_usaha }}" loading="lazy"
                                     class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'">
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('admin.umkm.show', $umkm->id_umkm) }}" class="font-medium text-gray-900 hover:text-djajan-primary transition-colors">
                                    {{ $umkm->nama_usaha }}
                                </a>
                                <p class="text-xs text-gray-400">
                                    {{ $umkm->spesialis ?: 'UMKM-' . str_pad($umkm->id_umkm, 3, '0', STR_PAD_LEFT) }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td class="py-4 px-5">
                        <p class="text-gray-700 font-medium">{{ $umkm->no_telp_usaha }}</p>
                        <p class="text-xs text-gray-500 truncate max-w-[150px]">{{ $umkm->email_usaha ?? '-' }}</p>
                    </td>

                    <td class="py-4 px-5 text-gray-500 hidden lg:table-cell">
                        {{ optional($umkm->created_at)->format('d M Y, H:i') }}
                    </td>

                    <td class="py-4 px-5 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                        </span>
                    </td>

                    <td class="py-4 px-5 whitespace-nowrap text-right">
                        <a href="{{ route('admin.umkm.show', $umkm->id_umkm) }}" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 bg-djajan-primary text-white text-xs font-medium rounded-lg hover:bg-djajan-primary-hover transition-colors shadow-sm">
                            Tinjau
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 px-4 text-center">
                        <div class="max-w-sm mx-auto">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1 font-heading">Semua Pengajuan Selesai</h3>
                            <p class="text-sm text-gray-500">Saat ini tidak ada pendaftaran UMKM yang perlu diverifikasi.</p>
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
        <div class="text-sm text-gray-500">
            Menampilkan
            <span class="font-semibold text-gray-900">{{ $umkms->firstItem() }}</span>
            sampai
            <span class="font-semibold text-gray-900">{{ $umkms->lastItem() }}</span>
            dari
            <span class="font-semibold text-gray-900">{{ $umkms->total() }}</span>
            data
        </div>
        <div class="flex items-center gap-1.5">
            {{ $umkms->onEachSide(1)->links() }}
        </div>
    </div>
    @endif
</div>

@endsection