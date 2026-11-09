<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class OrderCancellationService
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    /**
     * Membatalkan satu pesanan dengan alasan tertentu dan penanganan refund hybrid.
     */
    public function cancelOrder(Pesanan $order, string $cancelledBy, string $reason): Pesanan
    {
        return DB::transaction(function () use ($order, $cancelledBy, $reason) {
            $statusLama = $order->status_pesanan;

            // Kembalikan stok jika sebelumnya sudah diterima UMKM
            if ($statusLama === 'diterima') {
                foreach ($order->items as $item) {
                    if ($item->id_produk !== null) {
                        Produk::where('id_produk', $item->id_produk)
                            ->increment('stok', (int) $item->jumlah);
                    }
                }
            }

            $isPaid = in_array($order->payment_status, ['paid', 'settlement', 'capture'], true) ||
                in_array($order->status_pembayaran, ['lunas', 'paid'], true);

            $order->status_pesanan = 'ditolak';
            $order->alasan_batal = $reason;
            $order->dibatalkan_oleh = $cancelledBy;
            $order->dibatalkan_pada = now();

            if ($isPaid) {
                $order->status_refund = 'perlu_refund';
                if ($order->metode_pembayaran === 'midtrans') {
                    $targetId = $order->id_checkout ?? $order->id_pesanan;
                    $refundRes = $this->midtransService->refundTransaction(
                        $targetId,
                        (int) $order->total_harga,
                        $cancelledBy . ': ' . $reason
                    );
                    if (!empty($refundRes) && empty($refundRes['error'])) {
                        $order->status_refund = 'refund_berhasil';
                    }
                }
            } else {
                $order->status_refund = 'none';
                if ($order->metode_pembayaran === 'midtrans') {
                    $order->payment_status = 'cancel';
                    $order->status_pembayaran = 'batal';
                    $targetId = $order->id_checkout ?? $order->id_pesanan;
                    $this->midtransService->cancelTransaction($targetId);
                }
            }

            $order->save();
            return $order;
        });
    }

    /**
     * Auto-cancel semua pesanan po_baru yang telah melewati batas konfirmasi.
     * Mengembalikan jumlah pesanan yang dibatalkan.
     */
    public function cancelExpiredOrders(?string $simulateOrderId = null): int
    {
        $query = Pesanan::with('items')->where('status_pesanan', 'po_baru');

        if ($simulateOrderId) {
            $query->where('id_pesanan', $simulateOrderId);
        } else {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('batas_konfirmasi')
                        ->where('batas_konfirmasi', '<=', now());
                })->orWhere(function ($sub) {
                    // Fallback jika batas_konfirmasi null (misal data lama)
                    // Ready stock lewat 30 menit
                    $sub->whereNull('batas_konfirmasi')
                        ->whereNull('waktu_dibutuhkan')
                        ->where('tanggal_pesan', '<=', now()->subMinutes(30)->toDateString());
                });
            });
        }

        $orders = $query->get();
        $count = 0;

        foreach ($orders as $order) {
            $this->cancelOrder(
                $order,
                'sistem',
                'Batas waktu konfirmasi UMKM telah berakhir.'
            );
            $count++;
        }

        return $count;
    }
}

