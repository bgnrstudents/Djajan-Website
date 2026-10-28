@extends('layouts.admin')

@section('title', 'Dashboard - Djajan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-djajan-neutral font-heading">Dashboard</h1>
    <p class="text-sm text-gray-500 mt-1">Ringkasan data aktual dari database.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.umkm.index', ['status' => 'terverifikasi']) }}" class="bg-djajan-primary rounded-xl p-5 text-white shadow-sm">
        <p class="text-sm text-white/80">UMKM Aktif</p>
        <p class="text-3xl font-bold mt-2">{{ $totalUmkmAktif }}</p>
        <p class="text-xs text-white/70 mt-2">Terverifikasi dan toko aktif</p>
    </a>
    <a href="{{ route('admin.products.index') }}" class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:border-djajan-primary/40 transition-colors">
        <p class="text-sm text-gray-600">Total Produk</p>
        <p class="text-3xl font-bold text-djajan-neutral mt-2">{{ $totalProduk }}</p>
        <p class="text-xs text-gray-500 mt-2">Produk terdaftar</p>
    </a>
    <a href="{{ route('admin.umkm.index') }}" class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <p class="text-sm text-gray-600">Total UMKM Terdaftar</p>
        <p class="text-3xl font-bold text-djajan-neutral mt-2">{{ $totalPengguna }}</p>
        <p class="text-xs text-gray-500 mt-2">Bukan data pengguna dummy</p>
    </a>
    <a href="{{ route('admin.umkm.index', ['status' => 'pending']) }}" class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <p class="text-sm text-gray-600">Menunggu Verifikasi</p>
        <p class="text-3xl font-bold text-djajan-neutral mt-2">{{ $totalPengajuanBaru }}</p>
        <p class="text-xs text-gray-500 mt-2">Perlu ditinjau admin</p>
    </a>
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold text-djajan-neutral">Pengajuan Terbaru</h2>
            <p class="text-xs text-gray-500 mt-1">Data aktual dari pendaftaran UMKM.</p>
        </div>
        <a href="{{ route('admin.umkm.index', ['status' => 'pending']) }}" class="text-sm text-djajan-primary">Lihat semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs text-gray-500 uppercase"><th class="px-5 py-3">Nama Usaha</th><th class="px-5 py-3">Kontak</th><th class="px-5 py-3">Tanggal</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($pengajuanTerbaru as $umkm)
                <tr><td class="px-5 py-4 font-medium">{{ $umkm->nama_usaha }}</td><td class="px-5 py-4">{{ $umkm->no_telp_usaha }}</td><td class="px-5 py-4 text-gray-500">{{ optional($umkm->created_at)->format('d/m/Y H:i') }}</td><td class="px-5 py-4 text-right"><a class="text-djajan-primary" href="{{ route('admin.umkm.show', $umkm->id_umkm) }}">Detail</a></td></tr>
            @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Belum ada pengajuan UMKM.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
