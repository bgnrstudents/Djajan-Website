<?php

namespace Tests\Unit;

use App\Support\ValidasiProduk;
use PHPUnit\Framework\TestCase;

class ValidasiProdukTest extends TestCase
{
    public function test_product_names_can_include_numbers_and_common_product_markers(): void
    {
        $this->assertNull(ValidasiProduk::nama('Air Mineral 1L'));
        $this->assertNull(ValidasiProduk::nama('Beras Premium 5 kg'));
        $this->assertNotNull(ValidasiProduk::nama('Air Mineral @ 1L'));
    }

    public function test_product_prices_can_use_the_database_integer_range(): void
    {
        $this->assertNull(ValidasiProduk::harga(1_500_000));
        $this->assertNull(ValidasiProduk::harga(ValidasiProduk::HARGA_MAKSIMAL));
        $this->assertNotNull(ValidasiProduk::harga(ValidasiProduk::HARGA_MAKSIMAL + 1));
    }
}
