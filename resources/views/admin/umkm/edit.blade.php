@extends('layouts.admin')

@section('content')
<h1 class="h3 mb-3">Edit UMKM</h1>

<form method="POST" action="{{ route('admin.umkm.update', $umkm) }}" class="card p-4">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="nama_usaha" class="form-label">Nama usaha</label>
        <input id="nama_usaha" name="nama_usaha" value="{{ old('nama_usaha', $umkm->nama_usaha) }}" class="form-control @error('nama_usaha') is-invalid @enderror" required>
        @error('nama_usaha') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label for="spesialis" class="form-label">Spesialis</label>
        <input id="spesialis" name="spesialis" value="{{ old('spesialis', $umkm->spesialis) }}" class="form-control">
    </div>
    <div class="mb-3">
        <label for="deskripsi_usaha" class="form-label">Deskripsi</label>
        <textarea id="deskripsi_usaha" name="deskripsi_usaha" class="form-control">{{ old('deskripsi_usaha', $umkm->deskripsi_usaha) }}</textarea>
    </div>
    <div class="mb-3">
        <label for="alamat" class="form-label">Alamat</label>
        <textarea id="alamat" name="alamat" class="form-control">{{ old('alamat', $umkm->alamat) }}</textarea>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="no_telp_usaha" class="form-label">Nomor telepon</label>
            <input id="no_telp_usaha" name="no_telp_usaha" value="{{ old('no_telp_usaha', $umkm->no_telp_usaha) }}" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label for="email_usaha" class="form-label">Email usaha</label>
            <input id="email_usaha" type="email" name="email_usaha" value="{{ old('email_usaha', $umkm->email_usaha) }}" class="form-control">
        </div>
    </div>
    <div class="mb-3">
        <label for="status_verifikasi" class="form-label">Status verifikasi</label>
        <select id="status_verifikasi" name="status_verifikasi" class="form-select">
            @foreach(['menunggu', 'pending', 'terverifikasi', 'ditolak'] as $status)
                <option value="{{ $status }}" @selected(old('status_verifikasi', $umkm->status_verifikasi) === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-check mb-3">
        <input type="hidden" name="toko_aktif" value="0">
        <input id="toko_aktif" name="toko_aktif" value="1" type="checkbox" class="form-check-input" @checked(old('toko_aktif', $umkm->toko_aktif))>
        <label for="toko_aktif" class="form-check-label">Toko aktif</label>
    </div>
    <div>
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.umkm.show', $umkm) }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection
