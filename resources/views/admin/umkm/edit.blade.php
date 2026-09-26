@extends('layouts.admin')

@section('title', 'Edit UMKM - Djajan')

@section('content')

<!-- BREADCRUMB -->
<nav class="mb-4">
    <ol class="flex items-center gap-2 text-sm">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Dashboard</a></li>
        <li class="text-gray-400">/</li>
        <li><a href="{{ route('admin.umkm.index') }}" class="text-gray-500 hover:text-djajan-primary transition-colors">Data UMKM</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-djajan-neutral font-semibold">Edit UMKM</li>
    </ol>
</nav>

<!-- PAGE HEADER -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-djajan-neutral font-heading tracking-tight">Edit Data UMKM</h1>
            <p class="text-sm text-gray-500">Mengubah informasi <span class="font-medium text-gray-700">{{ $umkm->nama_usaha }}</span> (UMKM-{{ str_pad($umkm->id_umkm, 3, '0', STR_PAD_LEFT) }})</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.umkm.show', $umkm->id_umkm) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Lihat Detail
            </a>
            <a href="{{ route('admin.umkm.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Kembali
            </a>
        </div>
    </div>
</div>

@if ($errors->any())
<div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
    <p class="text-sm font-semibold text-red-800 mb-1">Terdapat kesalahan pada input Anda:</p>
    <ul class="text-sm text-red-700 list-disc list-inside space-y-0.5">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- FORM -->
<form action="{{ route('admin.umkm.update', $umkm->id_umkm) }}" method="POST" class="max-w-3xl space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Informasi Dasar UMKM</h3>
            <p class="text-xs text-gray-500 mt-0.5">Data ini juga tampil untuk pemilik saat login di aplikasi Flutter, jadi pastikan tetap akurat.</p>
        </div>

        <div class="p-6 space-y-5">
            <!-- Nama Usaha -->
            <div>
                <label for="nama_usaha" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Usaha <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_usaha" name="nama_usaha" value="{{ old('nama_usaha', $umkm->nama_usaha) }}" minlength="3" maxlength="120" required class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('nama_usaha') border-red-300 @enderror">
                @error('nama_usaha')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <!-- Spesialis -->
            <div>
                <label for="spesialis" class="block text-sm font-medium text-gray-700 mb-1.5">Spesialisasi</label>
                <input type="text" id="spesialis" name="spesialis" value="{{ old('spesialis', $umkm->spesialis) }}" maxlength="150" placeholder="Contoh: Spesialis nasi kotak & tumpeng" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('spesialis') border-red-300 @enderror">
                @error('spesialis')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi_usaha" class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Usaha</label>
                <textarea id="deskripsi_usaha" name="deskripsi_usaha" rows="4" maxlength="1000" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors resize-none @error('deskripsi_usaha') border-red-300 @enderror">{{ old('deskripsi_usaha', $umkm->deskripsi_usaha) }}</textarea>
                @error('deskripsi_usaha')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea id="alamat" name="alamat" rows="2" maxlength="255" required class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors resize-none @error('alamat') border-red-300 @enderror">{{ old('alamat', $umkm->alamat) }}</textarea>
                @error('alamat')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <!-- No Telp + Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="no_telp_usaha" class="block text-sm font-medium text-gray-700 mb-1.5">
                        No. WhatsApp Usaha <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="no_telp_usaha" name="no_telp_usaha" value="{{ old('no_telp_usaha', $umkm->no_telp_usaha) }}" placeholder="08xxxxxxxxxx" required class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('no_telp_usaha') border-red-300 @enderror">
                    @error('no_telp_usaha')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email_usaha" class="block text-sm font-medium text-gray-700 mb-1.5">Email Usaha</label>
                    <input type="email" id="email_usaha" name="email_usaha" value="{{ old('email_usaha', $umkm->email_usaha) }}" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary/20 focus:border-djajan-primary transition-colors @error('email_usaha') border-red-300 @enderror">
                    @error('email_usaha')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- STATUS TOKO -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-djajan-neutral font-heading">Status Toko</h3>
        </div>
        <div class="p-6">
            <label class="flex items-start gap-3 p-3.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="toko_aktif" value="1" {{ old('toko_aktif', $umkm->toko_aktif) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-djajan-primary border-gray-300 rounded focus:ring-djajan-primary">
                <div>
                    <p class="text-sm font-medium text-gray-900">Toko Aktif</p>
                    <p class="text-xs text-gray-500 mt-0.5">Jika dinonaktifkan, UMKM ini tidak tampil di halaman publik & aplikasi.</p>
                </div>
            </label>
            <p class="text-xs text-gray-400 mt-3">Pengaturan pembayaran (COD, transfer bank, skema PO, kurir) diatur langsung oleh pemilik UMKM lewat aplikasi mobile.</p>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex gap-3">
        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-djajan-primary text-white text-sm font-semibold rounded-lg hover:bg-djajan-primary-hover transition-colors shadow-sm">
            Perbarui Data UMKM
        </button>
        <a href="{{ route('admin.umkm.show', $umkm->id_umkm) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            Batal
        </a>
    </div>
</form>

@endsection
