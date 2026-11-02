<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\CheckoutGroup;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Umkm;
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
        $biayaLayanan = 0;

        if (!is_array($items) || count($items) === 0) {
            return $this->gagal('Item pesanan wajib diisi.', 422);
        }

        if (!in_array($metodePembayaran, ['midtrans', 'cod'], true)) {
            return $this->gagal('Metode pembayaran tidak valid.', 422);
        }

        if (!in_array($pengiriman, ['pickup', 'kurir'], true)) {
            return $this->gagal('Metode pengiriman tidak valid.', 422);
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

            // Harga dan biaya tidak boleh dipercaya dari client karena menjadi
            // dasar total pembayaran Midtrans maupun COD.
            $harga = (int) $produk->harga;
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

        $umkm = Umkm::find($umkmIds[0]);
        if (!$umkm || !(bool) $umkm->toko_aktif) {
            return $this->gagal('Toko sedang tidak menerima pesanan.', 409);
        }

        if ($metodePembayaran === 'cod' && !(bool) $umkm->cod_aktif) {
            return $this->gagal('Pembayaran COD tidak tersedia di toko ini.', 409);
        }

        if ($pengiriman === 'pickup' && !(bool) $umkm->pickup_aktif) {
            return $this->gagal('Pickup tidak tersedia di toko ini.', 409);
        }

        if ($pengiriman === 'kurir' && !(bool) $umkm->kurir_warga_aktif) {
            return $this->gagal('Pengantaran kurir tidak tersedia di toko ini.', 409);
        }

        if ($pengiriman === 'kurir' && (int) $umkm->biaya_pengantaran < 0) {
            return $this->gagal('Biaya pengantaran toko tidak valid.', 422);
        }

        if ($pengiriman === 'pickup') {
            $biayaPengantaran = 0;
        } else {
            $biayaPengantaran = (int) $umkm->biaya_pengantaran;
        }

        if ($pengiriman === 'kurir' && trim($alamat) === '') {
            return $this->gagal('Alamat tujuan wajib diisi.', 422);
        }

        $biayaLayanan = max(0, $biayaLayanan);
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
                'payment_status' => $metodePembayaran === 'cod' ? 'cod_pending' : 'pending',
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
                        'midtrans_order_id' => $transaksi['transaction_id'] ?: $order->id_pesanan,
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

    public function storeMulti(Request $request, MidtransService $midtransService): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }

        $groups = $request->input('groups', []);
        if (!is_array($groups) || $groups === []) {
            $groups = [[
                'id_umkm' => null,
                'metode_pengiriman' => $request->input('metode_pengiriman', 'kurir'),
                'waktu_dibutuhkan' => $request->input('waktu_dibutuhkan'),
                'keperluan_acara' => $request->input('keperluan_acara'),
                'catatan' => $request->input('catatan'),
                'items' => $request->input('items', []),
            ]];
        }

        $payment = strtolower((string) $request->input('metode_pembayaran', 'midtrans'));
        if (!in_array($payment, ['midtrans', 'cod'], true)) {
            return $this->gagal('Metode pembayaran tidak valid.', 422);
        }

        $alamat = trim((string) $request->input('alamat_tujuan', ''));
        $prepared = [];
        $grandTotal = 0;
        foreach ($groups as $group) {
            $items = $group['items'] ?? [];
            if (!is_array($items) || $items === []) {
                return $this->gagal('Item setiap UMKM wajib diisi.', 422);
            }

            $produkMap = [];
            $umkmId = (int) ($group['id_umkm'] ?? 0);
            $subtotal = 0;
            foreach ($items as $item) {
                $produk = Produk::find((int) ($item['id_produk'] ?? 0));
                $jumlah = max(1, (int) ($item['jumlah'] ?? 1));
                if (!$produk) {
                    return $this->gagal('Produk tidak ditemukan.', 404);
                }
                if ($umkmId > 0 && (int) $produk->id_umkm !== $umkmId) {
                    return $this->gagal('Produk tidak sesuai dengan UMKM checkout.', 422);
                }
                $umkmId = (int) $produk->id_umkm;
                if (!$produk->status_ketersediaan) {
                    return $this->gagal('Produk "' . $produk->nama_produk . '" sedang tidak tersedia.', 409);
                }
                $mode = strtolower(str_replace(['-', '_', ' '], '', (string) $produk->mode_penjualan));
                if ($mode !== 'preorder' && $produk->stok < $jumlah) {
                    return $this->gagal('Stok produk "' . $produk->nama_produk . '" tidak mencukupi.', 409);
                }
                $harga = (int) $produk->harga;
                $lineTotal = $harga * $jumlah;
                $subtotal += $lineTotal;
                $produkMap[] = compact('produk', 'jumlah', 'harga', 'lineTotal');
            }

            $umkm = Umkm::find($umkmId);
            $delivery = trim((string) ($group['metode_pengiriman'] ?? 'kurir'));
            if (!$umkm || !$umkm->toko_aktif) {
                return $this->gagal('Toko sedang tidak menerima pesanan.', 409);
            }
            if (!in_array($delivery, ['pickup', 'kurir'], true)) {
                return $this->gagal('Metode pengiriman tidak valid.', 422);
            }
            if ($payment === 'cod' && !$umkm->cod_aktif) {
                return $this->gagal('Pembayaran COD tidak tersedia di toko ini.', 409);
            }
            if ($delivery === 'pickup' && !$umkm->pickup_aktif) {
                return $this->gagal('Pickup tidak tersedia di toko ini.', 409);
            }
            if ($delivery === 'kurir' && !$umkm->kurir_warga_aktif) {
                return $this->gagal('Pengantaran kurir tidak tersedia di toko ini.', 409);
            }
            if ($delivery === 'kurir' && $alamat === '') {
                return $this->gagal('Alamat tujuan wajib diisi.', 422);
            }

            $ongkir = $delivery === 'kurir' ? max(0, (int) $umkm->biaya_pengantaran) : 0;
            $total = $subtotal + $ongkir;
            $grandTotal += $total;
            $prepared[] = compact('umkmId', 'produkMap', 'delivery', 'ongkir', 'total', 'group');
        }

        $checkoutId = 'CHK-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));
        $orders = DB::transaction(function () use ($prepared, $checkoutId, $pelanggan, $payment, $grandTotal, $alamat) {
            CheckoutGroup::create([
                'id_checkout' => $checkoutId,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'metode_pembayaran' => $payment,
                'total_harga' => $grandTotal,
                'payment_status' => $payment === 'cod' ? 'cod_pending' : 'pending',
            ]);

            $orders = [];
            foreach ($prepared as $entry) {
                $order = Pesanan::create([
                    'id_pesanan' => $this->buildOrderId(),
                    'id_checkout' => $checkoutId,
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'id_umkm' => $entry['umkmId'],
                    'tanggal_pesan' => now()->toDateString(),
                    'waktu_dibutuhkan' => $entry['group']['waktu_dibutuhkan'] ?? now()->addDay()->format('Y-m-d H:i:s'),
                    'batas_konfirmasi' => now()->addHours(24)->format('Y-m-d H:i:s'),
                    'keperluan_acara' => trim((string) ($entry['group']['keperluan_acara'] ?? '')),
                    'metode_pengiriman' => $entry['delivery'],
                    'alamat_tujuan' => $alamat,
                    'catatan' => trim((string) ($entry['group']['catatan'] ?? '')),
                    'metode_pembayaran' => $payment,
                    'status_pembayaran' => $payment === 'cod' ? 'bayar_di_tempat' : 'pending',
                    'payment_status' => $payment === 'cod' ? 'cod_pending' : 'pending',
                    'biaya_pengantaran' => $entry['ongkir'],
                    'biaya_layanan' => 0,
                    'total_harga' => $entry['total'],
                    'status_pesanan' => 'po_baru',
                ]);
                foreach ($entry['produkMap'] as $line) {
                    DetailPesanan::create([
                        'id_pesanan' => $order->id_pesanan,
                        'id_produk' => $line['produk']->id_produk,
                        'nama_produk' => $line['produk']->nama_produk,
                        'satuan' => $line['produk']->satuan ?? 'pcs',
                        'jumlah' => $line['jumlah'],
                        'harga_satuan' => $line['harga'],
                        'subtotal' => $line['lineTotal'],
                    ]);
                }
                $orders[] = $order;
            }
            return $orders;
        });

        $paymentData = ['token' => '', 'redirect_url' => '', 'transaction_id' => ''];
        if ($payment === 'midtrans') {
            $items = [];
            foreach ($prepared as $entry) {
                foreach ($entry['produkMap'] as $line) {
                    $items[] = [
                        'id' => (string) $line['produk']->id_produk,
                        'price' => $line['harga'],
                        'quantity' => $line['jumlah'],
                        'name' => $line['produk']->nama_produk,
                    ];
                }
                if ($entry['ongkir'] > 0) {
                    $items[] = ['id' => 'delivery-' . $entry['umkmId'], 'price' => $entry['ongkir'], 'quantity' => 1, 'name' => 'Biaya pengantaran'];
                }
            }
            $paymentData = $midtransService->createTransaction(
                $checkoutId,
                $grandTotal,
                $items,
                ['first_name' => $pelanggan->nama, 'email' => $pelanggan->email ?? 'customer@djajan.id', 'phone' => $pelanggan->no_telp ?? ''],
            );
            CheckoutGroup::whereKey($checkoutId)->update([
                'midtrans_order_id' => $paymentData['transaction_id'] ?: $checkoutId,
                'snap_token' => $paymentData['token'] ?: null,
                'payment_url' => $paymentData['redirect_url'] ?: null,
            ]);
            Pesanan::where('id_checkout', $checkoutId)->update([
                'midtrans_order_id' => $paymentData['transaction_id'] ?: $checkoutId,
                'snap_token' => $paymentData['token'] ?: null,
                'payment_url' => $paymentData['redirect_url'] ?: null,
            ]);
        }

        return $this->sukses('Pesanan berhasil dibuat.', [
            'id_checkout' => $checkoutId,
            'metode_pembayaran' => $payment,
            'total_harga' => $grandTotal,
            'snap_token' => $paymentData['token'],
            'payment_url' => $paymentData['redirect_url'],
            'midtrans_order_id' => $paymentData['transaction_id'] ?: $checkoutId,
            'orders' => collect($orders)->map(fn (Pesanan $order) => $order->fresh(['items.produk', 'umkm'])->keFormatJson())->all(),
        ], 201);
    }

    public function group(Request $request, string $idCheckout, MidtransService $midtransService): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }
        $group = CheckoutGroup::with('pesanan.items.produk', 'pesanan.umkm')
            ->where('id_checkout', $idCheckout)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();
        if (!$group) {
            return $this->gagal('Checkout tidak ditemukan.', 404);
        }
        $this->syncGroupPaymentStatus($group, $midtransService);
        return $this->sukses('Checkout dimuat.', [
            'id_checkout' => $group->id_checkout,
            'payment_status' => $group->payment_status,
            'orders' => $group->pesanan->map(fn (Pesanan $order) => $order->keFormatJson())->all(),
        ]);
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

        $serverKey = trim((string) config('services.midtrans.server_key'));
        if ($serverKey === '') {
            \Log::error('Midtrans callback rejected because server key is not configured', [
                'order_id' => $orderId,
            ]);
            return response()->json(['status' => 'configuration_error'], 500);
        }

        if (!$midtransService->verifyCallback($payload, $serverKey)) {
            \Log::warning('Midtrans payment notification signature rejected', [
                'order_id' => $orderId,
            ]);
            return response()->json(['status' => 'invalid_signature'], 403);
        }

        $checkoutGroup = CheckoutGroup::where('id_checkout', $orderId)
            ->orWhere('midtrans_order_id', $orderId)
            ->first();
        if ($checkoutGroup) {
            $grossAmount = (float) ($payload['gross_amount'] ?? -1);
            if ($grossAmount < 0 || abs($grossAmount - (float) $checkoutGroup->total_harga) > 0.001) {
                return response()->json(['status' => 'amount_mismatch'], 422);
            }
            if (in_array($checkoutGroup->payment_status, ['paid', 'failed'], true)) {
                return response()->json(['status' => 'success', 'payment_status' => $checkoutGroup->payment_status]);
            }
            $newStatus = in_array($transactionStatus, ['capture', 'settlement'], true)
                ? 'paid'
                : (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true) ? 'failed' : 'pending');
            $changes = ['payment_status' => $newStatus];
            if ($newStatus === 'paid') {
                $changes['dibayar_pada'] = now();
            }
            $checkoutGroup->update($changes);
            $orderChanges = [
                'payment_status' => $newStatus,
                'status_pembayaran' => $newStatus,
            ];
            if ($newStatus === 'failed') {
                $orderChanges['status_pesanan'] = 'ditolak';
            }
            if ($newStatus === 'paid') {
                $orderChanges['dibayar_pada'] = now();
            }
            Pesanan::where('id_checkout', $checkoutGroup->id_checkout)->update($orderChanges);
            return response()->json(['status' => 'success', 'payment_status' => $newStatus]);
        }

        $order = Pesanan::where('id_pesanan', $orderId)
            ->orWhere('midtrans_order_id', $orderId)
            ->first();
        if (!$order) {
            \Log::warning('Midtrans payment notification order not found', [
                'order_id' => $orderId,
            ]);
            return response()->json(['status' => 'not_found'], 404);
        }

        $grossAmount = (float) ($payload['gross_amount'] ?? -1);
        if ($grossAmount < 0 || abs($grossAmount - (float) $order->total_harga) > 0.001) {
            \Log::warning('Midtrans payment notification amount mismatch', [
                'order_id' => $order->id_pesanan,
                'expected_amount' => (int) $order->total_harga,
                'received_amount' => $payload['gross_amount'] ?? null,
            ]);
            return response()->json(['status' => 'amount_mismatch'], 422);
        }

        if (!in_array($transactionStatus, ['capture', 'settlement', 'pending', 'deny', 'cancel', 'expire'], true)) {
            \Log::warning('Midtrans payment notification status ignored', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
            ]);

            return response()->json(['status' => 'ignored'], 200);
        }

        if (in_array($order->payment_status, ['paid', 'failed'], true)) {
            return response()->json([
                'status' => 'success',
                'order_id' => $order->id_pesanan,
                'payment_status' => $order->payment_status,
            ]);
        }

        $newStatus = $order->status_pembayaran;
        $newOrderStatus = $order->status_pesanan;

        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            $newStatus = 'paid';
        } elseif (in_array($transactionStatus, ['pending'], true)) {
            $newStatus = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true)) {
            $newStatus = 'failed';
            $newOrderStatus = 'ditolak';
        }

        $changes = [
            'status_pembayaran' => $newStatus,
            'payment_status' => $newStatus,
            'status_pesanan' => $newOrderStatus,
        ];
        if ($newStatus === 'paid') {
            $changes['dibayar_pada'] = now();
        }
        $order->update($changes);

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
            $midtransOrderId = $order->midtrans_order_id ?: $order->id_pesanan;
            $payload = $midtransService->getTransactionStatus($midtransOrderId);
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
            }
            $order->update($changes);
            $order->refresh();
        }
    }

    private function syncGroupPaymentStatus(CheckoutGroup $group, MidtransService $midtransService): void
    {
        if ($group->metode_pembayaran !== 'midtrans'
            || in_array($group->payment_status, ['paid', 'failed'], true)) {
            return;
        }
        try {
            $payload = $midtransService->getTransactionStatus($group->midtrans_order_id ?: $group->id_checkout);
        } catch (\Throwable $exception) {
            \Log::warning('Midtrans checkout group status sync failed', [
                'checkout_id' => $group->id_checkout,
                'message' => $exception->getMessage(),
            ]);
            return;
        }
        $status = (string) ($payload['transaction_status'] ?? '');
        $newStatus = in_array($status, ['capture', 'settlement'], true)
            ? 'paid'
            : (in_array($status, ['deny', 'cancel', 'expire'], true) ? 'failed' : 'pending');
        if ($newStatus === $group->payment_status) {
            return;
        }
        $group->update([
            'payment_status' => $newStatus,
            'dibayar_pada' => $newStatus === 'paid' ? now() : $group->dibayar_pada,
        ]);
        $changes = [
            'payment_status' => $newStatus,
            'status_pembayaran' => $newStatus,
        ];
        if ($newStatus === 'failed') {
            $changes['status_pesanan'] = 'ditolak';
        }
        if ($newStatus === 'paid') {
            $changes['dibayar_pada'] = now();
        }
        Pesanan::where('id_checkout', $group->id_checkout)->update($changes);
    }

    private function buildOrderId(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(6));

        return 'PO-' . $date . '-' . $random;
    }
}
