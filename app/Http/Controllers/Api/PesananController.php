<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Services\MidtransService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Pengganti backend/api/pesanan_list.php, pesanan_detail.php, pesanan_status.php.
 *
 * PERUBAHAN DIBANDING VERSI LAMA: id_umkm diambil dari token, dan
 * pesanan_detail/pesanan_status sekarang memverifikasi pesanan itu benar
 * milik UMKM yang login (versi PHP Native lama tidak mengecek ini).
 */
class PesananController extends Controller
{
    use ApiResponse;

    public function index(Request $request, MidtransService $midtransService): JsonResponse
    {
        $idUmkm = (int) $request->user()->id_umkm;

        // Auto-sync hingga 5 pesanan Midtrans pending terbaru milik UMKM ini
        $pendingOrders = Pesanan::where('id_umkm', $idUmkm)
            ->where('metode_pembayaran', 'midtrans')
            ->where(function ($q) {
                $q->where('payment_status', 'pending')
                  ->orWhere('status_pembayaran', 'pending')
                  ->orWhere('status_pembayaran', 'belum_bayar');
            })
            ->whereNotIn('status_pesanan', ['selesai', 'ditolak'])
            ->orderByDesc('tanggal_pesan')
            ->take(5)
            ->get();

        foreach ($pendingOrders as $pendingOrder) {
            $this->syncPaymentStatus($pendingOrder, $midtransService);
        }

        $pesanan = Pesanan::with(['items.produk', 'pelanggan'])
            ->where('id_umkm', $idUmkm)
            ->orderByRaw("FIELD(status_pesanan, 'po_baru', 'diterima', 'diproses', 'diantar', 'selesai', 'ditolak')")
            ->orderByDesc('tanggal_pesan')
            ->get();

        $data = $pesanan->map(fn (Pesanan $p) => $p->keFormatJson())->all();

        return $this->sukses(count($data) . ' pesanan dimuat.', $data);
    }

    public function show(Request $request, MidtransService $midtransService): JsonResponse
    {
        $idPesanan = trim((string) $request->query('id_pesanan', ''));
        if ($idPesanan === '') {
            return $this->gagal('Parameter id_pesanan wajib diisi.', 400);
        }

        $pesanan = Pesanan::with(['items.produk', 'pelanggan'])
            ->where('id_pesanan', $idPesanan)
            ->where('id_umkm', (int) $request->user()->id_umkm)
            ->first();

        if (!$pesanan) {
            return $this->gagal('Pesanan tidak ditemukan.', 404);
        }

        $this->syncPaymentStatus($pesanan, $midtransService);

        return $this->sukses('Detail pesanan dimuat.', $pesanan->keFormatJson());
    }

    private function syncPaymentStatus(Pesanan $order, MidtransService $midtransService): void
    {
        if ($order->metode_pembayaran !== 'midtrans'
            || in_array($order->payment_status, ['paid', 'failed'], true)) {
            return;
        }

        $midtransOrderId = $order->midtrans_order_id ?: ($order->id_checkout ?: $order->id_pesanan);

        try {
            $payload = $midtransService->getTransactionStatus($midtransOrderId);
        } catch (\Throwable $exception) {
            $payload = $midtransService->verifyViaApi($midtransOrderId);
            if (empty($payload)) {
                Log::warning('Midtrans status sync failed in UMKM PesananController', [
                    'order_id' => $order->id_pesanan,
                    'message' => $exception->getMessage(),
                ]);
                return;
            }
        }

        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $newStatus = match ($transactionStatus) {
            'capture', 'settlement' => 'paid',
            'deny', 'cancel', 'expire' => 'failed',
            default => 'pending',
        };
        $newOrderStatus = match ($newStatus) {
            'failed' => 'ditolak',
            default => $order->status_pesanan,
        };

        if ($newStatus !== ($order->payment_status ?? 'pending')
            || $newOrderStatus !== $order->status_pesanan) {
            $changes = [
                'status_pembayaran' => $newStatus,
                'payment_status' => $newStatus,
                'status_pesanan' => $newOrderStatus,
            ];
            if ($newStatus === 'paid') {
                $changes['dibayar_pada'] = now();
            } elseif ($newOrderStatus === 'ditolak' && $order->status_pesanan !== 'ditolak') {
                if ($order->status_pesanan === 'diterima') {
                    foreach ($order->items as $item) {
                        if ($item->id_produk !== null) {
                            Produk::where('id_produk', $item->id_produk)->increment('stok', (int) $item->jumlah);
                        }
                    }
                }
                $changes['alasan_batal'] = 'Pembayaran dibatalkan atau kedaluwarsa (' . $transactionStatus . ').';
                $changes['dibatalkan_oleh'] = 'sistem';
                $changes['dibatalkan_pada'] = now();
            }
            $order->update($changes);
            $order->refresh();
        }
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $idPesanan = trim((string) $request->input('id_pesanan', ''));
        $status    = (string) $request->input('status', '');

        if ($idPesanan === '') {
            return $this->gagal('Parameter id_pesanan wajib diisi.', 400);
        }
        $allowedTransitions = [
            'po_baru' => ['diterima', 'ditolak'],
            'menunggu_konfirmasi' => ['diterima', 'ditolak'],
            'reviewing' => ['diterima', 'ditolak'],
            'diterima' => ['diproses', 'ditolak'],
            'diproses' => ['diantar'],
            'diantar' => ['selesai'],
            'ditolak' => [],
            'selesai' => [],
        ];

        if (!isset($allowedTransitions[$status])) {
            return $this->gagal('Status pesanan tidak valid.', 400);
        }

        $pesanan = Pesanan::with('items')
            ->where('id_pesanan', $idPesanan)
            ->where('id_umkm', (int) $request->user()->id_umkm)
            ->first();

        if (!$pesanan) {
            return $this->gagal('Pesanan tidak ditemukan.', 404);
        }
        if (!in_array($status, $allowedTransitions[$pesanan->status_pesanan] ?? [], true)) {
            return $this->gagal(
                'Transisi status tidak valid dari "' . $pesanan->status_pesanan
                    . '" ke "' . $status . '".',
                409
            );
        }

        try {
            DB::transaction(function () use ($pesanan, $status, $request) {
                $statusLama = $pesanan->status_pesanan;
                $pesanan->status_pesanan = $status;

                if ($status === 'diterima') {
                    $driver = DB::connection()->getDriverName();
                    foreach ($pesanan->items as $item) {
                        if ($item->id_produk !== null) {
                            $rawExpr = $driver === 'sqlite'
                                ? 'MAX(stok - ' . (int) $item->jumlah . ', 0)'
                                : 'GREATEST(stok - ' . (int) $item->jumlah . ', 0)';
                            Produk::where('id_produk', $item->id_produk)
                                ->update(['stok' => DB::raw($rawExpr)]);
                        }
                    }
                } elseif ($status === 'ditolak') {
                    // Jika sebelumnya sudah 'diterima', kembalikan stok
                    if ($statusLama === 'diterima') {
                        foreach ($pesanan->items as $item) {
                            if ($item->id_produk !== null) {
                                Produk::where('id_produk', $item->id_produk)
                                    ->increment('stok', (int) $item->jumlah);
                            }
                        }
                    }

                    $pesanan->dibatalkan_oleh = 'umkm';
                    $pesanan->dibatalkan_pada = now();
                    $pesanan->alasan_batal = trim((string) $request->input('alasan', 'Pesanan ditolak oleh penjual.'));

                    // Jika pesanan sudah dibayar, tandai perlu refund
                    if (in_array($pesanan->payment_status, ['paid', 'settlement', 'capture'], true) ||
                        in_array($pesanan->status_pembayaran, ['lunas', 'paid'], true)) {
                        $pesanan->status_refund = 'perlu_refund';
                    } else {
                        $pesanan->status_refund = 'none';
                    }
                }

                $pesanan->save();
            });
        } catch (\Throwable $e) {
            return $this->gagal('Gagal memperbarui status pesanan: ' . $e->getMessage(), 500);
        }

        $pesan = $status === 'diterima'
            ? 'Pesanan ' . $idPesanan . ' diterima. Stok produk sudah disesuaikan.'
            : 'Status pesanan ' . $idPesanan . ' berhasil diubah menjadi ' . $status . '.';

        $baru = $pesanan->fresh(['items.produk', 'pelanggan']);

        return $this->sukses($pesan, $baru->keFormatJson());
    }
}
