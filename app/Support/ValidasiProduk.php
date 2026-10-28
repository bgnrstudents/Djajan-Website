<?php

namespace App\Support;

use App\Models\Produk;

/**
 * Port langsung dari backend/config/validasi_produk.php (PHP Native).
 * Aturan disamakan persis (bukan ditulis ulang pakai gaya Laravel Validator)
 * supaya perilakunya identik dengan yang sudah dites di versi lama.
 */
class ValidasiProduk
{
    public const HARGA_MINIMAL = 100;              // Rp100 — di bawah ini kemungkinan salah ketik
    public const HARGA_MAKSIMAL = 1000000;          // Rp1.000.000 / porsi
    public const NAMA_MIN_PANJANG = 3;
    public const NAMA_MAKS_PANJANG = 80;
    public const DESKRIPSI_MAKS_PANJANG = 500;
    public const STOK_MAKSIMAL = 9999;
    public const MIN_ORDER_PO_MAKSIMAL = 9999;

    /** @return string|null null kalau valid, atau pesan error kalau tidak valid. */
    public static function nama(string $nama): ?string
    {
        $nama = trim($nama);

        if ($nama === '') {
            return 'Nama produk wajib diisi.';
        }
        if (mb_strlen($nama) < self::NAMA_MIN_PANJANG) {
            return 'Nama produk minimal ' . self::NAMA_MIN_PANJANG . ' karakter.';
        }
        if (mb_strlen($nama) > self::NAMA_MAKS_PANJANG) {
            return 'Nama produk maksimal ' . self::NAMA_MAKS_PANJANG . ' karakter.';
        }
        if (!preg_match('/[A-Za-z\x{00C0}-\x{024F}]/u', $nama)) {
            return 'Nama produk harus mengandung huruf, tidak boleh hanya angka/simbol.';
        }
        if (preg_match('/\d/u', $nama)) {
            return 'Nama produk tidak boleh mengandung angka.';
        }
        if (!preg_match("/^[A-Za-z\x{00C0}-\x{024F} .,'&()\/-]+$/u", $nama)) {
            return "Nama produk hanya boleh huruf, spasi, dan tanda & - ' ( ) , .";
        }
        return null;
    }

    /** @return string|null null kalau valid, atau pesan error kalau tidak valid. */
    public static function harga(int $harga): ?string
    {
        if ($harga < self::HARGA_MINIMAL) {
            return 'Harga produk minimal ' . self::rupiah(self::HARGA_MINIMAL) . '.';
        }
        if ($harga > self::HARGA_MAKSIMAL) {
            return 'Harga produk maksimal ' . self::rupiah(self::HARGA_MAKSIMAL)
                . '. Periksa lagi, sepertinya kelebihan angka nol.';
        }
        return null;
    }

    /**
     * Samakan bentuk nama sebelum dibandingkan: huruf kecil, spasi di
     * pinggir dibuang, dan spasi ganda dijadikan satu. Dengan begitu
     * "Nasi Kotak", "nasi kotak" dan "  NASI   KOTAK " dianggap SAMA.
     */
    public static function normalisasiNama(string $nama): string
    {
        $nama = preg_replace('/\s+/u', ' ', trim($nama)) ?? $nama;

        return mb_strtolower($nama);
    }

    /**
     * Cek apakah UMKM ini sudah punya produk dengan nama (tanpa peduli
     * huruf besar/kecil) DAN harga yang sama persis.
     *
     * @param int|null $kecualiIdProduk isi saat EDIT, supaya produk yang
     *                                  sedang diedit tidak dianggap duplikat
     *                                  dirinya sendiri.
     * @return string|null pesan error bila duplikat, null bila aman.
     */
    public static function duplikat(int $idUmkm, string $nama, int $harga, ?int $kecualiIdProduk = null): ?string
    {
        $target = self::normalisasiNama($nama);

        $ada = Produk::where('id_umkm', $idUmkm)
            ->where('harga', $harga)
            ->when($kecualiIdProduk, fn ($q) => $q->where('id_produk', '!=', $kecualiIdProduk))
            ->pluck('nama_produk')
            ->contains(fn ($n) => self::normalisasiNama((string) $n) === $target);

        if (!$ada) {
            return null;
        }

        return 'Produk "' . trim($nama) . '" dengan harga ' . self::rupiah($harga)
            . ' sudah ada. Ubah nama atau harganya, atau edit produk yang sudah ada.';
    }

    public static function rupiah(int $angka): string
    {
        return 'Rp' . number_format($angka, 0, ',', '.');
    }
}
