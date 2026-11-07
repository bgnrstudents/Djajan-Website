<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\CheckoutGroup;
use App\Models\Pelanggan;
use App\Models\PelangganAlamat;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Umkm;
use App\Services\MidtransService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CheckoutController extends Controller
{
    use ApiResponse;

    public function index(Request $request, MidtransService $midtransService): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }

        // Sinkronisasi otomatis untuk pesanan midtrans yang masih pending
        $pendingOrders = Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('metode_pembayaran', 'midtrans')
            ->where(function ($q) {
                $q->whereIn('payment_status', ['pending', 'cod_pending'])
                  ->orWhereNull('payment_status')
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

    public function cancel(Request $request, ?string $idPesanan = null): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }

        $id = trim((string) ($idPesanan ?? $request->input('id_pesanan', '')));
        if ($id === '') {
            return $this->gagal('Parameter id_pesanan wajib diisi.', 400);
        }

        $order = Pesanan::with(['items', 'umkm', 'pelanggan'])
            ->where('id_pesanan', $id)
            ->first();

        if (!$order) {
            return $this->gagal('Pesanan tidak ditemukan.', 404);
        }

        if ((int) $order->id_pelanggan !== (int) $pelanggan->id_pelanggan) {
            return $this->gagal('Anda tidak memiliki akses untuk membatalkan pesanan ini.', 403);
        }

        if (in_array($order->status_pesanan, ['selesai', 'ditolak'], true)) {
            return $this->gagal('Pesanan yang sudah ' . $order->status_pesanan . ' tidak dapat dibatalkan.', 409);
        }

        if (in_array($order->status_pesanan, ['diproses', 'diantar'], true)) {
            return $this->gagal('Pesanan yang sedang diproses atau diantar tidak dapat dibatalkan.', 409);
        }

        $alasan = trim((string) $request->input('alasan', ''));
        if ($alasan === '') {
            $alasan = 'Dibatalkan oleh pelanggan.';
        }

        try {
            DB::transaction(function () use ($order, $alasan) {
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
                $order->alasan_batal = $alasan;
                $order->dibatalkan_oleh = 'pelanggan';
                $order->dibatalkan_pada = now();

                if ($isPaid) {
                    $order->status_refund = 'perlu_refund';
                } else {
                    $order->status_refund = 'none';
                    if ($order->metode_pembayaran === 'midtrans') {
                        $order->payment_status = 'cancel';
                        $order->status_pembayaran = 'batal';
                    }
                }

                $order->save();
            });
        } catch (\Throwable $e) {
            return $this->gagal('Gagal membatalkan pesanan: ' . $e->getMessage(), 500);
        }

        $order->refresh();

        return $this->sukses('Pesanan berhasil dibatalkan.', $order->keFormatJson());
    }

    public function complete(Request $request, ?string $id_pesanan = null): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }

        $id = $id_pesanan ?? $request->input('id_pesanan');
        if (!$id) {
            return $this->gagal('ID pesanan wajib diisi.', 422);
        }

        $order = Pesanan::with(['items', 'umkm', 'pelanggan'])
            ->where('id_pesanan', $id)
            ->first();

        if (!$order) {
            return $this->gagal('Pesanan tidak ditemukan.', 404);
        }

        if ((int) $order->id_pelanggan !== (int) $pelanggan->id_pelanggan) {
            return $this->gagal('Anda tidak memiliki akses untuk menyelesaikan pesanan ini.', 403);
        }

        if ($order->status_pesanan === 'selesai') {
            return $this->sukses('Pesanan sudah selesai.', $order->keFormatJson());
        }

        if ($order->status_pesanan === 'ditolak') {
            return $this->gagal('Pesanan yang sudah dibatalkan tidak dapat diselesaikan.', 409);
        }

        if (!in_array($order->status_pesanan, ['diantar', 'diproses'], true)) {
            return $this->gagal(
                'Pesanan belum dapat diselesaikan karena status saat ini masih "' . $order->status_pesanan . '".',
                409
            );
        }

        try {
            DB::transaction(function () use ($order) {
                $order->status_pesanan = 'selesai';
                $order->status_pembayaran = 'lunas';
                $order->waktu_selesai = now();
                if ($order->payment_status === 'cod_pending') {
                    $order->payment_status = 'paid';
                    $order->dibayar_pada = now();
                }
                $order->save();
            });
        } catch (\Throwable $e) {
            return $this->gagal('Gagal menyelesaikan pesanan: ' . $e->getMessage(), 500);
        }

        return $this->sukses(
            'Pesanan berhasil diselesaikan. Terima kasih telah berbelanja!',
            $order->fresh()->keFormatJson()
        );
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
            return $this->gagal("Pembayaran COD tidak tersedia di toko {$umkm->nama_usaha}.", 409);
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
                'nama_umkm_snapshot' => $umkm->nama_usaha ?? 'UMKM',
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
        $deliveryAddress = null;
        if ($request->filled('id_alamat')) {
            $deliveryAddress = PelangganAlamat::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->whereKey($request->integer('id_alamat'))
                ->first();
            if (!$deliveryAddress) {
                return $this->gagal('Alamat pengiriman tidak ditemukan.', 404);
            }
            $alamat = trim((string) $deliveryAddress->alamat);
        } else {
            // Fallback: cari alamat pelanggan yang memiliki koordinat GPS
            $deliveryAddress = PelangganAlamat::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderByDesc('utama')
                ->orderByDesc('updated_at')
                ->first();
            if ($deliveryAddress && empty($alamat)) {
                $alamat = trim((string) $deliveryAddress->alamat);
            }
        }

        // Jika request menyertakan koordinat GPS langsung dan deliveryAddress belum memiliki koordinat
        if ((!$deliveryAddress || $deliveryAddress->latitude === null) && $request->filled('latitude') && $request->filled('longitude')) {
            if (!$deliveryAddress) {
                $deliveryAddress = new PelangganAlamat();
                $deliveryAddress->id_pelanggan = $pelanggan->id_pelanggan;
                $deliveryAddress->alamat = $alamat;
            }
            $deliveryAddress->latitude = (float) $request->input('latitude');
            $deliveryAddress->longitude = (float) $request->input('longitude');
        }
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
                return $this->gagal("Pembayaran COD tidak tersedia di toko {$umkm->nama_usaha}.", 409);
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

            try {
                $ongkirDetails = $this->calculateDeliveryDetails(
                    $umkm,
                    $delivery,
                    $deliveryAddress,
                    $request->filled('latitude') ? (float) $request->input('latitude') : null,
                    $request->filled('longitude') ? (float) $request->input('longitude') : null
                );
            } catch (InvalidArgumentException $error) {
                return $this->gagal($error->getMessage(), 422);
            }
            $ongkir = $ongkirDetails['biaya_ongkir'];
            $jarakKm = $ongkirDetails['jarak_km'];
            $zonaOngkirId = $ongkirDetails['zona_ongkir_id'];
            $total = $subtotal + $ongkir;
            $grandTotal += $total;
            $prepared[] = compact('umkmId', 'produkMap', 'delivery', 'ongkir', 'jarakKm', 'zonaOngkirId', 'total', 'group');
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
                    'nama_umkm_snapshot' => Umkm::find($entry['umkmId'])?->nama_usaha ?? 'UMKM',
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
                    'jarak_km' => $entry['jarakKm'],
                    'zona_ongkir_id' => $entry['zonaOngkirId'],
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
            'order_id'           => $orderId,
            'transaction_status' => $transactionStatus,
            'status_code'        => $payload['status_code'] ?? null,
            'gross_amount'       => $payload['gross_amount'] ?? null,
            'has_signature_key'  => isset($payload['signature_key']),
            'full_payload_keys'  => array_keys($payload),
        ]);

        if ($orderId === '') {
            return response()->json(['status' => 'invalid'], 200);
        }

        $serverKey = trim((string) config('services.midtrans.server_key'));
        if ($serverKey === '') {
            \Log::error('Midtrans callback: server key not configured', compact('orderId'));
            return response()->json(['status' => 'configuration_error'], 200);
        }

        // ---------------------------------------------------------------
        // Verifikasi signature — jika gagal, coba fallback via API
        // ---------------------------------------------------------------
        $signatureValid = $midtransService->verifyCallback($payload, $serverKey);

        if (!$signatureValid) {
            \Log::warning('Midtrans callback: signature mismatch, attempting API verification', [
                'order_id' => $orderId,
            ]);

            // Fallback: tanya langsung ke Midtrans API status transaksi ini
            $apiPayload = $midtransService->verifyViaApi($orderId);

            if (!empty($apiPayload) && isset($apiPayload['transaction_status'])) {
                // API menjawab → notifikasi ini sah, gunakan data langsung dari API
                $transactionStatus = (string) $apiPayload['transaction_status'];
                $payload['gross_amount'] = $apiPayload['gross_amount'] ?? $payload['gross_amount'] ?? '0';
                $payload['status_code'] = $apiPayload['status_code'] ?? $payload['status_code'] ?? '';
                \Log::info('Midtrans callback: API verification succeeded', [
                    'order_id'           => $orderId,
                    'transaction_status' => $transactionStatus,
                ]);
                $signatureValid = true;
            }
        }

        if (!$signatureValid) {
            \Log::warning('Midtrans callback: all verification failed — rejecting', [
                'order_id' => $orderId,
            ]);
            // Tetap return 200 agar Midtrans tidak mengirim ulang terus-menerus
            return response()->json(['status' => 'invalid_signature'], 200);
        }

        // ---------------------------------------------------------------
        // Proses notifikasi — cari CheckoutGroup atau Pesanan
        // ---------------------------------------------------------------
        return $this->applyPaymentNotification($orderId, $transactionStatus, $payload);
    }

    /**
     * Terapkan status pembayaran dari notifikasi Midtrans ke database.
     */
    private function applyPaymentNotification(string $orderId, string $transactionStatus, array $payload): JsonResponse
    {
        // ---- Coba CheckoutGroup dulu (multi-order checkout) ----
        $checkoutGroup = CheckoutGroup::where('id_checkout', $orderId)
            ->orWhere('midtrans_order_id', $orderId)
            ->first();

        if ($checkoutGroup) {
            $grossAmount = (float) ($payload['gross_amount'] ?? -1);
            if ($grossAmount < 0 || abs($grossAmount - (float) $checkoutGroup->total_harga) > 0.001) {
                \Log::warning('Midtrans callback: amount mismatch (group)', [
                    'order_id' => $orderId,
                    'expected' => $checkoutGroup->total_harga,
                    'received' => $payload['gross_amount'] ?? null,
                ]);
                return response()->json(['status' => 'amount_mismatch'], 200);
            }
            if (in_array($checkoutGroup->payment_status, ['paid', 'failed'], true)) {
                return response()->json(['status' => 'success', 'payment_status' => $checkoutGroup->payment_status], 200);
            }
            $newStatus = $this->mapMidtransStatus($transactionStatus);
            $changes = ['payment_status' => $newStatus];
            if ($newStatus === 'paid') {
                $changes['dibayar_pada'] = now();
            }
            $checkoutGroup->update($changes);
            $orderChanges = [
                'payment_status'    => $newStatus,
                'status_pembayaran' => $newStatus,
            ];
            if ($newStatus === 'failed') {
                $orderChanges['status_pesanan'] = 'ditolak';
            }
            if ($newStatus === 'paid') {
                $orderChanges['dibayar_pada'] = now();
            }
            Pesanan::where('id_checkout', $checkoutGroup->id_checkout)->update($orderChanges);

            \Log::info('Midtrans callback: checkout group updated', [
                'checkout_id'    => $checkoutGroup->id_checkout,
                'payment_status' => $newStatus,
            ]);

            return response()->json(['status' => 'success', 'payment_status' => $newStatus], 200);
        }

        // ---- Fallback: cari Pesanan tunggal ----
        $order = Pesanan::where('id_pesanan', $orderId)
            ->orWhere('midtrans_order_id', $orderId)
            ->first();

        if (!$order) {
            \Log::warning('Midtrans callback: order not found', compact('orderId'));
            return response()->json(['status' => 'not_found'], 200);
        }

        $grossAmount = (float) ($payload['gross_amount'] ?? -1);
        if ($grossAmount < 0 || abs($grossAmount - (float) $order->total_harga) > 0.001) {
            \Log::warning('Midtrans callback: amount mismatch (order)', [
                'order_id' => $order->id_pesanan,
                'expected' => (int) $order->total_harga,
                'received' => $payload['gross_amount'] ?? null,
            ]);
            return response()->json(['status' => 'amount_mismatch'], 200);
        }

        if (!in_array($transactionStatus, ['capture', 'settlement', 'pending', 'deny', 'cancel', 'expire'], true)) {
            return response()->json(['status' => 'ignored'], 200);
        }

        if (in_array($order->payment_status, ['paid', 'failed'], true)) {
            return response()->json([
                'status'         => 'success',
                'order_id'       => $order->id_pesanan,
                'payment_status' => $order->payment_status,
            ], 200);
        }

        $newStatus = $this->mapMidtransStatus($transactionStatus);
        $newOrderStatus = $order->status_pesanan;
        if ($newStatus === 'failed') {
            $newOrderStatus = 'ditolak';
        }

        $changes = [
            'status_pembayaran' => $newStatus,
            'payment_status'    => $newStatus,
            'status_pesanan'    => $newOrderStatus,
        ];
        if ($newStatus === 'paid') {
            $changes['dibayar_pada'] = now();
        }
        $order->update($changes);

        \Log::info('Midtrans callback: order updated', [
            'order_id'       => $order->id_pesanan,
            'payment_status' => $newStatus,
            'order_status'   => $newOrderStatus,
        ]);

        return response()->json([
            'status'         => 'success',
            'order_id'       => $order->id_pesanan,
            'payment_status' => $newStatus,
        ], 200);
    }

    /**
     * Map Midtrans transaction_status ke status internal.
     */
    private function mapMidtransStatus(string $transactionStatus): string
    {
        return match (true) {
            in_array($transactionStatus, ['capture', 'settlement'], true) => 'paid',
            in_array($transactionStatus, ['deny', 'cancel', 'expire'], true) => 'failed',
            default => 'pending',
        };
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
            // Fallback ke verifyViaApi (mencoba Snap endpoint jika Core API 403)
            $payload = $midtransService->verifyViaApi($midtransOrderId);
            if (empty($payload)) {
                \Log::warning('Midtrans status sync failed', [
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

    private function syncGroupPaymentStatus(CheckoutGroup $group, MidtransService $midtransService): void
    {
        if ($group->metode_pembayaran !== 'midtrans'
            || in_array($group->payment_status, ['paid', 'failed'], true)) {
            return;
        }

        $midtransOrderId = $group->midtrans_order_id ?: $group->id_checkout;

        try {
            $payload = $midtransService->getTransactionStatus($midtransOrderId);
        } catch (\Throwable $exception) {
            // Fallback ke verifyViaApi
            $payload = $midtransService->verifyViaApi($midtransOrderId);
            if (empty($payload)) {
                \Log::warning('Midtrans checkout group status sync failed', [
                    'checkout_id' => $group->id_checkout,
                    'message' => $exception->getMessage(),
                ]);
                return;
            }
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

    public function hitungOngkir(Request $request): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }

        $idUmkm = (int) $request->input('id_umkm', 0);
        if ($idUmkm <= 0) {
            return $this->gagal('ID UMKM wajib diisi.', 422);
        }

        $umkm = Umkm::find($idUmkm);
        if (!$umkm) {
            return $this->gagal('UMKM tidak ditemukan.', 404);
        }

        $delivery = trim((string) $request->input('metode_pengiriman', 'kurir'));
        if (!in_array($delivery, ['pickup', 'kurir'], true)) {
            return $this->gagal('Metode pengiriman tidak valid.', 422);
        }

        $deliveryAddress = null;
        if ($request->filled('id_alamat')) {
            $deliveryAddress = PelangganAlamat::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->whereKey($request->integer('id_alamat'))
                ->first();
        } else {
            $deliveryAddress = PelangganAlamat::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderByDesc('utama')
                ->orderByDesc('updated_at')
                ->first();
        }

        $lat = $request->filled('latitude') ? (float) $request->input('latitude') : null;
        $lon = $request->filled('longitude') ? (float) $request->input('longitude') : null;

        try {
            $details = $this->calculateDeliveryDetails($umkm, $delivery, $deliveryAddress, $lat, $lon);
            return $this->sukses('Perhitungan ongkir berhasil.', [
                'id_umkm' => (int) $umkm->id_umkm,
                'nama_umkm' => $umkm->nama_usaha,
                'metode_pengiriman' => $delivery,
                'jarak_km' => $details['jarak_km'],
                'biaya_ongkir' => $details['biaya_ongkir'],
                'gratis' => $details['gratis'],
                'zona_ongkir_id' => $details['zona_ongkir_id'],
                'pesan' => $details['pesan'],
                'di_luar_jangkauan' => false,
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->gagal($e->getMessage(), 422, [
                'di_luar_jangkauan' => true,
                'metode_pengiriman' => $delivery,
            ]);
        }
    }

    public function calculateDeliveryDetails(
        Umkm $umkm,
        string $delivery,
        ?PelangganAlamat $address = null,
        ?float $latitude = null,
        ?float $longitude = null
    ): array {
        if ($delivery === 'pickup') {
            return [
                'jarak_km' => null,
                'biaya_ongkir' => 0,
                'gratis' => true,
                'zona_ongkir_id' => null,
                'pesan' => 'Ambil sendiri di toko (Gratis)',
            ];
        }

        $custLat = $latitude ?? ($address?->latitude !== null ? (float) $address->latitude : null);
        $custLon = $longitude ?? ($address?->longitude !== null ? (float) $address->longitude : null);

        if ($custLat === null || $custLon === null) {
            throw new InvalidArgumentException(
                'Alamat pengiriman harus memiliki koordinat GPS untuk layanan kurir.'
            );
        }

        // Koordinat default Desa Kreyongan Atas (-8.1585, 113.7025) jika UMKM belum set titik
        $umkmLat = $umkm->latitude !== null ? (float) $umkm->latitude : -8.1585000;
        $umkmLon = $umkm->longitude !== null ? (float) $umkm->longitude : 113.7025000;

        $distance = round($this->distanceInKm(
            $custLat,
            $custLon,
            $umkmLat,
            $umkmLon,
        ), 2);

        // Batas maksimal jangkauan kurir UMKM (default 10 KM untuk kurir warga desa)
        $maxDistance = (float) ($umkm->jarak_maksimal_km ?? 0);
        if ($maxDistance <= 0) {
            $maxDistance = 10.0;
        }
        if ($distance > $maxDistance) {
            throw new InvalidArgumentException(
                'Alamat pengiriman berada di luar jangkauan kurir UMKM (' . $maxDistance . ' km).'
            );
        }

        $freeRadius = max(0, (float) ($umkm->radius_gratis_km ?? 0));
        if ($distance <= $freeRadius) {
            return [
                'jarak_km' => $distance,
                'biaya_ongkir' => 0,
                'gratis' => true,
                'zona_ongkir_id' => null,
                'pesan' => 'Gratis ongkir (radius ' . $freeRadius . ' km)',
            ];
        }

        $zone = $umkm->zonaOngkir()
            ->orderBy('jarak_min')
            ->get()
            ->first(fn ($item) => $distance > (float) $item->jarak_min
                && $distance <= (float) $item->jarak_max);

        if ($zone) {
            $fee = max(0, (int) $zone->biaya);
            return [
                'jarak_km' => $distance,
                'biaya_ongkir' => $fee,
                'gratis' => $fee === 0,
                'zona_ongkir_id' => (int) $zone->id_zona,
                'pesan' => 'Tarif zona (' . $zone->jarak_min . ' - ' . $zone->jarak_max . ' km)',
            ];
        }

        // Fallback jika UMKM belum mengatur zona bertingkat tetapi memiliki tarif pengantaran
        if ($umkm->zonaOngkir()->doesntExist()) {
            $flatFee = max(0, (int) ($umkm->biaya_pengantaran ?? 0));
            if ($flatFee <= 0) {
                // Skema tarif kurir warga dinamis: Rp 3.000 (0-1 km) + Rp 1.500/km berikutnya
                $dynamicFee = 3000 + (int) (max(0, ceil($distance - 1)) * 1500);
                return [
                    'jarak_km' => $distance,
                    'biaya_ongkir' => $dynamicFee,
                    'gratis' => false,
                    'zona_ongkir_id' => null,
                    'pesan' => 'Tarif kurir warga desa (~' . $distance . ' km)',
                ];
            }
            return [
                'jarak_km' => $distance,
                'biaya_ongkir' => $flatFee,
                'gratis' => $flatFee === 0,
                'zona_ongkir_id' => null,
                'pesan' => 'Tarif flat kurir UMKM',
            ];
        }

        throw new InvalidArgumentException(
            'Alamat pengiriman berada di luar jangkauan kurir UMKM.'
        );
    }

    private function calculateDeliveryFee(Umkm $umkm, string $delivery, ?PelangganAlamat $address, ?float $latitude = null, ?float $longitude = null): int
    {
        return $this->calculateDeliveryDetails($umkm, $delivery, $address, $latitude, $longitude)['biaya_ongkir'];
    }

    private function distanceInKm(float $latitudeA, float $longitudeA, float $latitudeB, float $longitudeB): float
    {
        $earthRadius = 6371.0;
        $latDelta = deg2rad($latitudeB - $latitudeA);
        $lonDelta = deg2rad($longitudeB - $longitudeA);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($latitudeA))
            * cos(deg2rad($latitudeB))
            * sin($lonDelta / 2) ** 2;

        return $earthRadius * 2 * asin(min(1, sqrt($a)));
    }
}
