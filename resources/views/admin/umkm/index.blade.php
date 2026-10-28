@extends('layouts.admin')

@section('title', 'Data UMKM - Djajan')

@section('content')

<!-- PAGE HEADER -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Data UMKM</h1>
    <p class="text-sm text-gray-500 mt-1">Tinjau, setujui, atau tolak pendaftaran UMKM yang masuk lewat aplikasi mobile.</p>
</div>

<!-- STATUS TABS -->
<div class="flex flex-wrap gap-2 mb-6">
    @php
        $tabs = [
            'pending' => 'Menunggu',
            'terverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak',
        ];
    @endphp
    @foreach($tabs as $key => $label)
        <a href="{{ route('admin.umkm.index', ['status' => $key]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border transition-colors
           {{ $status === $key ? 'bg-djajan-primary text-white border-djajan-primary' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            {{ $label }}
            <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-xs font-semibold
                {{ $status === $key ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600' }}">
                {{ $jumlah[$key] }}
            </span>
        </a>
    @endforeach
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
                    <th class="py-3.5 px-5">Kontak</th>
                    <th class="py-3.5 px-5 hidden lg:table-cell">Alamat</th>
                    <th class="py-3.5 px-5">Status</th>
                    <th class="py-3.5 px-5">Toko</th>
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
                        <p class="text-gray-700 font-medium">{{ $umkm->no_telp_usaha ?: '-' }}</p>
                        <p class="text-xs text-gray-500 truncate max-w-[200px]" title="{{ $umkm->email_usaha }}">{{ $umkm->email_usaha ?: '-' }}</p>
                    </td>

                    <td class="py-4 px-5 text-gray-500 hidden lg:table-cell max-w-xs truncate">
                        {{ $umkm->alamat }}
                    </td>

                    <td class="py-4 px-5 whitespace-nowrap">
                        @if($umkm->status_verifikasi === 'terverifikasi')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Terverifikasi
                        </span>
                        @elseif($umkm->status_verifikasi === 'ditolak')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                        </span>
                        @endif
                    </td>

                    <td class="py-4 px-5 whitespace-nowrap">
                        @if($umkm->status_verifikasi !== 'terverifikasi')
                        <span class="text-xs text-gray-400" title="Status toko baru bisa diatur setelah terverifikasi">-</span>
                        @elseif($umkm->toko_aktif)
                        <span class="text-xs font-medium text-emerald-700">Buka</span>
                        @else
                        <span class="text-xs font-medium text-gray-500">Tutup</span>
                        @endif
                    </td>

                    <td class="py-4 px-5 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            @if(in_array($umkm->status_verifikasi, ['pending', 'menunggu'], true))
                            <form action="{{ route('admin.umkm.approve', $umkm->id_umkm) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors"
                                    onclick="return confirm('Setujui UMKM {{ $umkm->nama_usaha }}?');">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" />
                                    </svg>
                                    Setujui
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('admin.umkm.show', $umkm->id_umkm) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-djajan-primary hover:bg-djajan-primary/10 transition-colors" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.umkm.destroy', $umkm->id_umkm) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus UMKM {{ $umkm->nama_usaha }}? Data yang sudah dihapus tidak dapat dikembalikan.');">
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
                            <h3 class="text-base font-semibold text-gray-900 mb-1 font-heading">Belum Ada Data</h3>
                            <p class="text-sm text-gray-500">Belum ada UMKM dengan status "{{ $tabs[$status] ?? $status }}" saat ini.</p>
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
