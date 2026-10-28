<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Services\MidtransService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }

        $orders = Pesanan::with('items.produk', 'umkm')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderByDesc('tanggal_pesan')
            ->get();

        return $this->sukses('Riwayat pesanan dimuat.', $orders->map(fn (Pesanan $order) => $order->keFormatJson())->all());
    }

    public function show(Request $request, string $idPesanan, MidtransService $midtransService): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }

        $order = Pesanan::with(['items.produk', 'umkm'])
            ->where('id_pesanan', $idPesanan)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();

        if (!$order) {
            return $this->gagal('Pesanan tidak ditemukan.', 404);
        }

        $this->syncPaymentStatus($order, $midtransService);

        return $this->sukses('Detail pesanan dimuat.', $order->keFormatJson());
    }

    public function store(Request $request, MidtransService $midtransService): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }

        $items = $request->input('items', []);
        $metodePembayaran = strtolower((string) $request->input('metode_pembayaran', 'midtrans'));
        $alamat = trim((string) $request->input('alamat_tujuan', ''));
        $catatan = trim((string) $request->input('catatan', ''));
        $pengiriman = trim((string) $request->input('metode_pengiriman', 'kurir'));
        $waktuDibutuhkan = $request->input('waktu_dibutuhkan');
        $keperluanAcara = trim((string) $request->input('keperluan_acara', ''));
        $biayaPengantaran = (int) $request->input('biaya_pengantaran', 0);
        $biayaLayanan = (int) $request->input('biaya_layanan', 0);

        if (!is_array($items) || count($items) === 0) {
            return $this->gagal('Item pesanan wajib diisi.', 422);
        }

        if (!in_array($metodePembayaran, ['midtrans', 'cod'], true)) {
            return $this->gagal('Metode pembayaran tidak valid.', 422);
        }

        $produkMap = [];
        $totalQty = 0;
        $subtotal = 0;
        $umkmIds = [];

        foreach ($items as $item) {
            $idProduk = (int) ($item['id_produk'] ?? 0);
            $jumlah = max(1, (int) ($item['jumlah'] ?? 1));

            if ($idProduk <= 0) {
                return $this->gagal('Data produk tidak valid.', 422);
            }

            $produk = Produk::find($idProduk);
            if (!$produk) {
                return $this->gagal('Produk tidak ditemukan.', 404);
            }

            if ((int) $produk->id_umkm <= 0) {
                return $this->gagal('Produk tidak terhubung ke UMKM.', 422);
            }

            if ((bool) $produk->status_ketersediaan === false) {
                return $this->gagal('Produk "' . $produk->nama_produk . '" sedang tidak tersedia.', 409);
            }

            $modePenjualan = strtolower(str_replace(['-', '_', ' '], '', (string) $produk->mode_penjualan));
            $isPreorder = $modePenjualan === 'preorder';

            if ($isPreorder && $produk->stok < 0) {
                return $this->gagal('Produk pre-order tidak dapat dipesan pada stok yang tidak valid.', 422);
            }

            if (!$isPreorder && $produk->stok < $jumlah) {
                return $this->gagal('Stok produk "' . $produk->nama_produk . '" tidak mencukupi.', 409);
            }

            $harga = (int) ($item['harga_satuan'] ?? $produk->harga);
            $subtotalItem = $harga * $jumlah;
            $subtotal += $subtotalItem;
            $totalQty += $jumlah;
            $umkmIds[] = (int) $produk->id_umkm;
            $produkMap[] = [
                'produk' => $produk,
                'jumlah' => $jumlah,
                'harga_satuan' => $harga,
                'subtotal' => $subtotalItem,
            ];
        }

        $umkmIds = array_values(array_unique($umkmIds));
        if (count($umkmIds) !== 1) {
            return $this->gagal('Checkout saat ini hanya mendukung satu UMKM per transaksi.', 422);
        }

        if (trim($alamat) === '') {
            return $this->gagal('Alamat tujuan wajib diisi.', 422);
        }

        $totalHarga = $subtotal + $biayaPengantaran + $biayaLayanan;
        $orderId = $this->buildOrderId();

        $statusPesanan = 'po_baru';
        $statusPembayaran = $metodePembayaran === 'midtrans' ? 'pending' : 'bayar_di_tempat';

        return DB::transaction(function () use (
            $midtransService,
            $pelanggan,
            $orderId,
            $metodePembayaran,
            $statusPesanan,
            $statusPembayaran,
            $alamat,
            $catatan,
            $pengiriman,
            $waktuDibutuhkan,
            $keperluanAcara,
            $biayaPengantaran,
            $biayaLayanan,
            $totalHarga,
            $umkmIds,
            $produkMap,
            $request,
        ) {
            $order = Pesanan::create([
                'id_pesanan' => $orderId,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_umkm' => $umkmIds[0],
                'tanggal_pesan' => now()->toDateString(),
                'waktu_dibutuhkan' => $waktuDibutuhkan ?? now()->addDay()->format('Y-m-d H:i:s'),
                'batas_konfirmasi' => now()->addHours(24)->format('Y-m-d H:i:s'),
                'keperluan_acara' => $keperluanAcara,
                'metode_pengiriman' => $pengiriman,
                'alamat_tujuan' => $alamat,
                'catatan' => $catatan,
                'metode_pembayaran' => $metodePembayaran,
                'status_pembayaran' => $statusPembayaran,
                'biaya_pengantaran' => $biayaPengantaran,
                'biaya_layanan' => $biayaLayanan,
                'total_harga' => $totalHarga,
                'status_pesanan' => $statusPesanan,
            ]);

            foreach ($produkMap as $entry) {
                $produk = $entry['produk'];
                $jumlah = (int) $entry['jumlah'];
                $harga = (int) $entry['harga_satuan'];
                $subtotalItem = (int) $entry['subtotal'];

                DetailPesanan::create([
                    'id_pesanan' => $order->id_pesanan,
                    'id_produk' => $produk->id_produk,
                    'nama_produk' => $produk->nama_produk,
                    'satuan' => $produk->satuan ?? 'pcs',
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga,
                    'subtotal' => $subtotalItem,
                ]);

                if ($metodePembayaran === 'cod') {
                    // Stok tetap dipotong setelah UMKM konfirmasi, demi keamanan dan
                    // agar tidak mengubah stok sebelum pembayaran final diproses.
                    continue;
                }
            }

            $payload = [
                'token' => '',
                'redirect_url' => '',
                'transaction_id' => '',
                'order_id' => $order->id_pesanan,
                'total_harga' => $totalHarga,
                'status_pembayaran' => $statusPembayaran,
            ];

            if ($metodePembayaran === 'midtrans') {
                try {
                    $midtransItems = array_map(function ($entry) {
                        $produk = $entry['produk'];

                        return [
                            'id' => (string) $produk->id_produk,
                            'price' => (int) $entry['harga_satuan'],
                            'quantity' => (int) $entry['jumlah'],
                            'name' => $produk->nama_produk,
                        ];
                    }, $produkMap);

                    if ($biayaPengantaran > 0) {
                        $midtransItems[] = [
                            'id' => 'delivery-fee',
                            'price' => $biayaPengantaran,
                            'quantity' => 1,
                            'name' => 'Biaya pengantaran',
                        ];
                    }

                    if ($biayaLayanan > 0) {
                        $midtransItems[] = [
                            'id' => 'service-fee',
                            'price' => $biayaLayanan,
                            'quantity' => 1,
                            'name' => 'Biaya layanan',
                        ];
                    }

                    $transaksi = $midtransService->createTransaction(
                        $order->id_pesanan,
                        $totalHarga,
                        $midtransItems,
                        [
                            'first_name' => $pelanggan->nama,
                            'email' => $pelanggan->email ?? 'customer@djajan.id',
                            'phone' => $pelanggan->no_telp ?? '',
                        ]
                    );

                    $order->update([
                        'midtrans_order_id' => $transaksi['transaction_id'] ?: null,
                        'snap_token' => $transaksi['token'] ?: null,
                        'payment_url' => $transaksi['redirect_url'] ?: null,
                        'payment_status' => 'pending',
                        'status_pembayaran' => 'pending',
                    ]);

                    $payload['token'] = $transaksi['token'] ?? '';
                    $payload['redirect_url'] = $transaksi['redirect_url'] ?? '';
                    $payload['transaction_id'] = $transaksi['transaction_id'] ?? '';
                } catch (\Throwable $e) {
                    $order->update([
                        'status_pembayaran' => 'pending',
                        'payment_status' => 'pending',
                    ]);
                    throw $e;
                }
            }

            return $this->sukses('Pesanan berhasil dibuat.', [
                'id_pesanan' => $order->id_pesanan,
                'metode_pembayaran' => $metodePembayaran,
                'status_pembayaran' => $order->status_pembayaran,
                'status_pesanan' => $order->status_pesanan,
                'total_harga' => (int) $order->total_harga,
                'snap_token' => $order->snap_token,
                'payment_url' => $order->payment_url,
                'midtrans_order_id' => $order->midtrans_order_id,
                'items' => $order->items()->get()->map(fn (DetailPesanan $detail) => $detail->keFormatJson())->all(),
            ]);
        });
    }

    public function callback(Request $request, MidtransService $midtransService): JsonResponse
    {
        $payload = $request->all();
        $orderId = (string) ($payload['order_id'] ?? '');
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');

        \Log::info('Midtrans payment notification received', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'status_code' => $payload['status_code'] ?? null,
            'gross_amount' => $payload['gross_amount'] ?? null,
        ]);

        if ($orderId === '') {
            return response()->json(['status' => 'invalid'], 400);
        }

        $serverKey = (string) config('services.midtrans.server_key');
        if ($serverKey !== '' && !$midtransService->verifyCallback($payload, $serverKey)) {
            \Log::warning('Midtrans payment notification signature rejected', [
                'order_id' => $orderId,
            ]);
            return response()->json(['status' => 'invalid_signature'], 403);
        }

        $order = Pesanan::find($orderId);
        if (!$order) {
            \Log::warning('Midtrans payment notification order not found', [
                'order_id' => $orderId,
            ]);
            return response()->json(['status' => 'not_found'], 404);
        }

        $newStatus = $order->status_pembayaran;
        $newOrderStatus = $order->status_pesanan;

        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            $newStatus = 'paid';
            $newOrderStatus = 'diterima';
        } elseif (in_array($transactionStatus, ['pending'], true)) {
            $newStatus = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true)) {
            $newStatus = 'failed';
            $newOrderStatus = 'ditolak';
        }

        $order->update([
            'status_pembayaran' => $newStatus,
            'payment_status' => $newStatus,
            'status_pesanan' => $newOrderStatus,
        ]);

        \Log::info('Midtrans payment notification applied', [
            'order_id' => $order->id_pesanan,
            'payment_status' => $newStatus,
            'order_status' => $newOrderStatus,
        ]);

        return response()->json([
            'status' => 'success',
            'order_id' => $order->id_pesanan,
            'payment_status' => $newStatus,
        ]);
    }

    private function syncPaymentStatus(Pesanan $order, MidtransService $midtransService): void
    {
        if ($order->metode_pembayaran !== 'midtrans'
            || in_array($order->payment_status, ['paid', 'failed'], true)) {
            return;
        }

        try {
            $payload = $midtransService->getTransactionStatus($order->id_pesanan);
        } catch (\Throwable $exception) {
            \Log::warning('Midtrans status sync failed', [
                'order_id' => $order->id_pesanan,
                'message' => $exception->getMessage(),
            ]);
            return;
        }

        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $newStatus = match ($transactionStatus) {
            'capture', 'settlement' => 'paid',
            'deny', 'cancel', 'expire' => 'failed',
            default => 'pending',
        };
        $newOrderStatus = match ($newStatus) {
            'paid' => 'diterima',
            'failed' => 'ditolak',
            default => $order->status_pesanan,
        };

        if ($newStatus !== ($order->payment_status ?? 'pending')
            || $newOrderStatus !== $order->status_pesanan) {
            $order->update([
                'status_pembayaran' => $newStatus,
                'payment_status' => $newStatus,
                'status_pesanan' => $newOrderStatus,
            ]);
            $order->refresh();
        }
    }

    private function buildOrderId(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(6));

        return 'PO-' . $date . '-' . $random;
    }
}
