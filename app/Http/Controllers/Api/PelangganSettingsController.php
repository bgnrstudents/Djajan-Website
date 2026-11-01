<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\PelangganAlamat;
use App\Models\PelangganFavorit;
use App\Models\Produk;
use App\Models\Umkm;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PelangganSettingsController extends Controller
{
    use ApiResponse;

    public function profile(Request $request): JsonResponse
    {
        return $this->sukses('Profil pelanggan dimuat.', $this->profileData($request->user()));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'no_telp' => ['required', 'string', 'min:10', 'max:20'],
        ]);
        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 400);
        }

        $pelanggan = $request->user();
        $email = trim((string) $request->input('email'));
        if ($email !== '' && Pelanggan::where('email', $email)
            ->where('id_pelanggan', '!=', $pelanggan->id_pelanggan)->exists()) {
            return $this->gagal('Email sudah digunakan akun lain.', 409, ['field' => 'email']);
        }

        $pelanggan->update([
            'nama' => trim((string) $request->input('nama')),
            'email' => $email !== '' ? mb_strtolower($email) : null,
            'no_telp' => $this->normalizePhone((string) $request->input('no_telp')),
        ]);

        return $this->sukses('Profil berhasil diperbarui.', $this->profileData($pelanggan->fresh()));
    }

    public function addresses(Request $request): JsonResponse
    {
        $data = PelangganAlamat::where('id_pelanggan', $request->user()->id_pelanggan)
            ->orderByDesc('utama')->orderByDesc('updated_at')->get()
            ->map(fn (PelangganAlamat $address) => $this->addressData($address))
            ->values()->all();

        return $this->sukses('Daftar alamat dimuat.', $data);
    }

    public function saveAddress(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_alamat' => ['nullable', 'integer'],
            'label' => ['required', 'string', 'max:50'],
            'nama_penerima' => ['required', 'string', 'max:120'],
            'no_telp' => ['required', 'string', 'min:10', 'max:20'],
            'alamat' => ['required', 'string', 'min:5'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'utama' => ['nullable', 'boolean'],
        ]);
        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 400);
        }

        $pelangganId = $request->user()->id_pelanggan;
        $address = $request->filled('id_alamat')
            ? PelangganAlamat::where('id_pelanggan', $pelangganId)
                ->whereKey($request->integer('id_alamat'))->first()
            : null;
        if ($request->filled('id_alamat') && !$address) {
            return $this->gagal('Alamat tidak ditemukan.', 404);
        }

        $isPrimary = $request->boolean('utama') || !$address
            && !PelangganAlamat::where('id_pelanggan', $pelangganId)->exists();
        if ($isPrimary) {
            PelangganAlamat::where('id_pelanggan', $pelangganId)->update(['utama' => false]);
        }

        $data = [
            'id_pelanggan' => $pelangganId,
            'label' => trim((string) $request->input('label')),
            'nama_penerima' => trim((string) $request->input('nama_penerima')),
            'no_telp' => $this->normalizePhone((string) $request->input('no_telp')),
            'alamat' => trim((string) $request->input('alamat')),
            'catatan' => trim((string) $request->input('catatan', '')) ?: null,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'utama' => $isPrimary,
        ];
        if ($address) {
            $address->update($data);
        } else {
            $address = PelangganAlamat::create($data);
        }

        return $this->sukses('Alamat berhasil disimpan.', $this->addressData($address->fresh()));
    }

    public function deleteAddress(Request $request): JsonResponse
    {
        $address = PelangganAlamat::where('id_pelanggan', $request->user()->id_pelanggan)
            ->whereKey($request->integer('id_alamat'))->first();
        if (!$address) {
            return $this->gagal('Alamat tidak ditemukan.', 404);
        }
        $wasPrimary = $address->utama;
        $address->delete();
        if ($wasPrimary) {
            PelangganAlamat::where('id_pelanggan', $request->user()->id_pelanggan)
                ->latest('updated_at')->first()?->update(['utama' => true]);
        }
        return $this->sukses('Alamat berhasil dihapus.');
    }

    public function setPrimaryAddress(Request $request): JsonResponse
    {
        $address = PelangganAlamat::where('id_pelanggan', $request->user()->id_pelanggan)
            ->whereKey($request->integer('id_alamat'))->first();
        if (!$address) {
            return $this->gagal('Alamat tidak ditemukan.', 404);
        }
        PelangganAlamat::where('id_pelanggan', $request->user()->id_pelanggan)->update(['utama' => false]);
        $address->update(['utama' => true]);
        return $this->sukses('Alamat utama berhasil diubah.', $this->addressData($address->fresh()));
    }

    public function favorites(Request $request): JsonResponse
    {
        $favorites = PelangganFavorit::where('id_pelanggan', $request->user()->id_pelanggan)
            ->get(['tipe', 'id_item']);
        return $this->sukses('Favorit dimuat.', [
            'produk' => $favorites->where('tipe', 'produk')->pluck('id_item')->values(),
            'umkm' => $favorites->where('tipe', 'umkm')->pluck('id_item')->values(),
        ]);
    }

    public function toggleFavorite(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipe' => ['required', 'in:produk,umkm'],
            'id_item' => ['required', 'integer'],
            'favorit' => ['required', 'boolean'],
        ]);
        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 400);
        }
        $model = $request->input('tipe') === 'produk' ? Produk::class : Umkm::class;
        $exists = $model::whereKey($request->integer('id_item'))->exists();
        if (!$exists) {
            return $this->gagal('Item favorit tidak ditemukan.', 404);
        }

        $query = PelangganFavorit::where('id_pelanggan', $request->user()->id_pelanggan)
            ->where('tipe', $request->input('tipe'))->where('id_item', $request->integer('id_item'));
        if ($request->boolean('favorit')) {
            $query->firstOrCreate([
                'id_pelanggan' => $request->user()->id_pelanggan,
                'tipe' => $request->input('tipe'),
                'id_item' => $request->integer('id_item'),
            ]);
        } else {
            $query->delete();
        }
        return $this->sukses('Favorit berhasil diperbarui.');
    }

    private function profileData(Pelanggan $pelanggan): array
    {
        return [
            'id_pelanggan' => (int) $pelanggan->id_pelanggan,
            'nama' => $pelanggan->nama,
            'email' => $pelanggan->email ?? '',
            'no_telp' => $pelanggan->no_telp ?? '',
            'alamat' => $pelanggan->alamat ?? '',
        ];
    }

    private function addressData(PelangganAlamat $address): array
    {
        return [
            'id_alamat' => (int) $address->id_alamat,
            'label' => $address->label,
            'nama_penerima' => $address->nama_penerima,
            'no_telp' => $address->no_telp,
            'alamat' => $address->alamat,
            'catatan' => $address->catatan ?? '',
            'latitude' => $address->latitude,
            'longitude' => $address->longitude,
            'utama' => (bool) $address->utama,
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-.]/', '', trim($phone));
        if (str_starts_with($phone, '+62')) {
            return '0' . substr($phone, 3);
        }
        if (str_starts_with($phone, '62')) {
            return '0' . substr($phone, 2);
        }
        return $phone;
    }
}
