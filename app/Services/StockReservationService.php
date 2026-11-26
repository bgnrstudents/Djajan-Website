<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class StockReservationService
{
    /**
     * Ready-stock quantities in orders not yet accepted or closed.
     *
     * @param array<int, int> $productIds
     * @return array<int, int>
     */
    public function reservedReadyStock(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        return DB::table('detail_pesanan as detail')
            ->join('pesanan as orders', 'orders.id_pesanan', '=', 'detail.id_pesanan')
            ->join('produk as products', 'products.id_produk', '=', 'detail.id_produk')
            ->whereIn('orders.status_pesanan', ['po_baru', 'menunggu_konfirmasi', 'reviewing'])
            ->whereIn('detail.id_produk', $productIds)
            ->where(function ($query): void {
                $query->where('detail.jenis_pesanan', 'ready')
                    ->orWhere(function ($legacyQuery): void {
                        $legacyQuery->whereNull('detail.jenis_pesanan')
                            ->whereRaw(
                                "LOWER(REPLACE(REPLACE(REPLACE(COALESCE(products.mode_penjualan, ''), '-', ''), '_', ''), ' ', '')) != ?",
                                ['preorder']
                            );
                    });
            })
            ->groupBy('detail.id_produk')
            ->select('detail.id_produk', DB::raw('SUM(detail.jumlah) as reserved_quantity'))
            ->pluck('reserved_quantity', 'id_produk')
            ->map(static fn ($quantity): int => (int) $quantity)
            ->all();
    }
}
