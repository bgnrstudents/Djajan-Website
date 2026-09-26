@extends('layouts.admin')

@section('title', $umkm->nama_usaha . ' - Detail UMKM')

@section('content')

<!-- BREADCRUMB -->
<nav class="mb-4">
    <ol class="flex items-center gap-2 text-sm">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Dashboard</a></li>
        <li class="text-gray-400">/</li>
        <li><a href="{{ route('admin.umkm.index') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Data UMKM</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-djajan-neutral font-semibold">Detail UMKM</li>
    </ol>
</nav>

<!-- PAGE HEADER -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">{{ $umkm->nama_usaha }}</h1>
                @if($umkm->status_verifikasi === 'terverifikasi')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Terverifikasi
                </span>
                @elseif($umkm->status_verifikasi === 'ditolak')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu Verifikasi
                </span>
                @endif
            </div>
            <p class="text-sm text-gray-500">Informasi lengkap profil usaha, data kontak, dan daftar produk terdaftar.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.umkm.edit', $umkm->id_umkm) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-djajan-primary text-white text-sm font-medium rounded-lg hover:bg-djajan-primary-hover transition-colors shadow-sm">
                Edit UMKM
            </a>
            <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="space-y-6">

    {{-- Alert --}}
    @if(session('success'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    {{-- PANEL SETUJUI / TOLAK -- hanya untuk yang masih pending --}}
    @if($umkm->status_verifikasi === 'pending')
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <h2 class="text-sm font-semibold text-amber-900 mb-1">Pendaftaran Menunggu Peninjauan</h2>
        <p class="text-xs text-amber-800 mb-4">Pastikan UMKM ini benar berasal dari Desa Kreyongan Atas sebelum menyetujui. Verifikasi lapangan disarankan untuk usaha baru.</p>

        <div class="flex flex-col sm:flex-row gap-3">
            <form action="{{ route('admin.umkm.approve', $umkm->id_umkm) }}" method="POST" class="flex-1 flex gap-2">
                @csrf
                <input type="text" name="catatan" maxlength="255" placeholder="Catatan (opsional)" class="flex-1 px-3.5 py-2.5 bg-white border border-amber-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors whitespace-nowrap">Setujui</button>
            </form>
            <form action="{{ route('admin.umkm.reject', $umkm->id_umkm) }}" method="POST" class="flex-1 flex gap-2" onsubmit="return confirm('Yakin ingin menolak pendaftaran UMKM ini?');">
                @csrf
                <input type="text" name="catatan" required maxlength="255" placeholder="Alasan penolakan (wajib)" class="flex-1 px-3.5 py-2.5 bg-white border border-amber-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                <button type="submit" class="px-4 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors whitespace-nowrap">Tolak</button>
            </form>
        </div>
        @error('catatan')<p class="mt-2 text-xs text-red-700 font-medium">{{ $message }}</p>@enderror
    </div>
    @endif

    <!-- INFORMASI UTAMA HERO CARD -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3">
            <div class="bg-gray-50 border-b lg:border-b-0 lg:border-r border-gray-200 p-6 flex items-center justify-center">
                @if($umkm->foto_profil)
                <img src="{{ $umkm->urlFotoProfil() }}" alt="{{ $umkm->nama_usaha }}" class="w-full max-h-64 object-cover rounded-xl border border-gray-200/80 shadow-sm">
                @else
                <div class="w-full h-56 rounded-xl bg-gray-100 flex flex-col items-center justify-center text-gray-400 border border-gray-200/60">
                    <span class="text-xs font-medium text-gray-500">Foto belum diunggah</span>
                </div>
                @endif
            </div>

            <div class="lg:col-span-2 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-4 mb-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-djajan-primary/10 text-djajan-primary">
                            {{ $umkm->spesialis ?: 'Belum ada spesialisasi' }}
                        </span>
                        <span class="text-xs text-gray-400 font-mono">UMKM-{{ str_pad($umkm->id_umkm, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 font-heading mb-2">{{ $umkm->nama_usaha }}</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $umkm->deskripsi_usaha ?: 'Belum ada deskripsi profil untuk UMKM ini.' }}</p>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading">Status Toko</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $umkm->toko_aktif ? 'Aktif / Buka' : 'Nonaktif' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading">Total Produk</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $umkm->produk->count() }} Produk</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider font-heading">Alamat Usaha</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5 truncate" title="{{ $umkm->alamat }}">{{ $umkm->alamat }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KONTAK & PENGATURAN USAHA -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900 font-heading">Kontak</h2>
            </div>
            <div class="p-6 space-y-4 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">No. WhatsApp</span><span class="font-medium text-gray-900">{{ $umkm->no_telp_usaha }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Email</span><span class="font-medium text-gray-900">{{ $umkm->email_usaha ?: '-' }}</span></div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900 font-heading">Pengaturan Transaksi</h2>
            </div>
            <div class="p-6 space-y-4 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">COD</span><span class="font-medium {{ $umkm->cod_aktif ? 'text-emerald-600' : 'text-gray-400' }}">{{ $umkm->cod_aktif ? 'Aktif' : 'Nonaktif' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Transfer Bank</span><span class="font-medium {{ $umkm->transfer_bank_aktif ? 'text-emerald-600' : 'text-gray-400' }}">{{ $umkm->transfer_bank_aktif ? "$umkm->bank_nama ($umkm->no_rekening a.n. $umkm->atas_nama)" : 'Nonaktif' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Pickup</span><span class="font-medium {{ $umkm->pickup_aktif ? 'text-emerald-600' : 'text-gray-400' }}">{{ $umkm->pickup_aktif ? 'Aktif' : 'Nonaktif' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Kurir Warga</span><span class="font-medium {{ $umkm->kurir_warga_aktif ? 'text-emerald-600' : 'text-gray-400' }}">{{ $umkm->kurir_warga_aktif ? 'Aktif' : 'Nonaktif' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Skema PO</span><span class="font-medium text-gray-900 text-right">{{ $umkm->skema_po }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Biaya Antar</span><span class="font-medium text-gray-900">Rp {{ number_format($umkm->biaya_pengantaran, 0, ',', '.') }}</span></div>
            </div>
        </div>
    </div>

    @if($umkm->riwayatVerifikasi->isNotEmpty())
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900 font-heading">Riwayat Verifikasi</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($umkm->riwayatVerifikasi as $riwayat)
            <div class="px-6 py-3 flex items-center justify-between text-sm">
                <div>
                    <span class="font-medium {{ $riwayat->status === 'terverifikasi' ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $riwayat->status === 'terverifikasi' ? 'Disetujui' : 'Ditolak' }}
                    </span>
                    @if($riwayat->catatan)
                    <span class="text-gray-500"> — {{ $riwayat->catatan }}</span>
                    @endif
                </div>
                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($riwayat->tanggal_verifikasi)->format('d M Y H:i') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- DAFTAR PRODUK CARD -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-semibold text-gray-900 font-heading">Daftar Produk UMKM</h2>
                <p class="text-xs text-gray-500 mt-0.5">Seluruh produk yang dipasarkan oleh {{ $umkm->nama_usaha }}.</p>
            </div>
            <a href="{{ route('admin.products.create', $umkm->id_umkm) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-djajan-primary bg-djajan-primary/10 hover:bg-djajan-primary/20 rounded-lg transition-colors">
                Tambah Produk
            </a>
        </div>

        @if($umkm->produk->count())
        <div class="divide-y divide-gray-100">
            @foreach($umkm->produk as $product)
            <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors">
                <div class="w-14 h-14 flex-shrink-0 rounded-lg border border-gray-200 overflow-hidden bg-gray-50">
                    @if($product->gambar_produk)
                    <img src="{{ $product->urlGambar() }}" alt="{{ $product->nama_produk }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Img</div>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 font-heading">{{ $product->nama_produk }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $product->kategori ?: 'Tanpa Kategori' }} &middot; Stok {{ $product->stok }}</p>
                </div>

                <div class="text-right">
                    <p class="text-sm font-semibold text-djajan-primary">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium {{ $product->status_ketersediaan ? 'text-emerald-600' : 'text-gray-400' }} mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $product->status_ketersediaan ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        {{ $product->status_ketersediaan ? 'Tersedia' : 'Nonaktif' }}
                    </span>
                </div>

                <a href="{{ route('admin.products.edit', $product->id_produk) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-djajan-primary hover:bg-djajan-primary/10 transition-colors" title="Edit Produk">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-10 text-center">
            <p class="text-sm font-semibold text-gray-900 font-heading">Belum Ada Produk</p>
            <p class="text-xs text-gray-500 mt-0.5 mb-3">UMKM ini belum memiliki produk yang terdaftar.</p>
            <a href="{{ route('admin.products.create', $umkm->id_umkm) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-djajan-primary hover:bg-djajan-primary-hover rounded-lg transition-colors shadow-sm">
                + Tambah Produk Pertama
            </a>
        </div>
        @endif
    </div>

</div>

@endsection
