<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_returns_expected_collections(): void
    {
        $this->getJson('/api/katalog')
            ->assertOk()
            ->assertJsonPath('sukses', true)
            ->assertJsonStructure([
                'data' => ['umkm', 'produk', 'kategori'],
            ]);
    }
}
