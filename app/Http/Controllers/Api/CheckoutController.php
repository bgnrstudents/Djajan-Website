<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\QrisPaymentException;
use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\CheckoutGroup;
use App\Models\Pelanggan;
use App\Models\PelangganAlamat;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\UlasanProduk;
use App\Models\Umkm;
use App\Services\MidtransService;
use App\Services\OrderCancellationService;
use App\Services\StockReservationService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
            ->whereIn('metode_pembayaran', ['qris', 'midtrans'])
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

        $orders = Pesanan::with('items.produk', 'items.ulasan', 'umkm')
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

        $order = Pesanan::with(['items.produk', 'items.ulasan', 'umkm'])
            ->where('id_pesanan', $idPesanan)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();

        if (!$order) {
            return $this->gagal('Pesanan tidak ditemukan.', 404);
        }

        $this->syncPaymentStatus($order, $midtransService);

        // Auto-cancel lazy check jika batas konfirmasi UMKM telah lewat
        if ($order->status_pesanan === 'po_baru' && $order->batas_konfirmasi && $order->batas_konfirmasi <= now()) {
            app(OrderCancellationService::class)->cancelOrder(
                $order,
                'sistem',
                'Batas waktu konfirmasi UMKM telah berakhir.'
            );
            $order->refresh();
        }

        return $this->sukses('Detail pesanan dimuat.', $order->keFormatJson());
    }

    public function cancel(
        Request $request,
        ?string $idPesanan = null,
        OrderCancellationService $cancellationService = null
    ): JsonResponse
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
            $service = $cancellationService ?? app(OrderCancellationService::class);
            $order = $service->cancelOrder($order, 'pelanggan', $alasan);
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

    public function reviewProduct(
        Request $request,
        string $idPesanan,
        string $idProduk
    ): JsonResponse {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }

        $validator = Validator::make($request->all(), [
            'id_detail' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'komentar' => ['required', 'string', 'max:1000'],
        ]);
        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 422);
        }

        $order = Pesanan::query()
            ->where('id_pesanan', $idPesanan)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();
        if (!$order) {
            return $this->gagal('Pesanan tidak ditemukan.', 404);
        }
        if ($order->status_pesanan !== 'selesai') {
            return $this->gagal('Ulasan hanya dapat diberikan pada pesanan yang sudah selesai.', 409);
        }

        $detail = $order->items()
            ->where('id_detail', $request->input('id_detail'))
            ->where('id_produk', $idProduk)
            ->first();
        if (!$detail) {
            return $this->gagal('Produk tersebut tidak ditemukan pada pesanan ini.', 404);
        }

        try {
            DB::transaction(function () use ($detail, $pelanggan, $idProduk, $request) {
                UlasanProduk::updateOrCreate(
                    ['id_detail' => $detail->id_detail],
                    [
                        'id_pelanggan' => $pelanggan->id_pelanggan,
                        'id_produk' => $idProduk,
                        'rating' => (int) $request->input('rating'),
                        'komentar' => trim((string) $request->input('komentar')),
                    ],
                );
            });
        } catch (\Throwable $e) {
            report($e);
            return $this->gagal('Ulasan gagal disimpan. Silakan coba kembali.', 500);
        }

        $order->load(['items.produk', 'items.ulasan', 'umkm', 'pelanggan']);

        return $this->sukses('Ulasan berhasil disimpan.', $order->keFormatJson());
    }

    public function store(Request $request, MidtransService $midtransService): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan instanceof Pelanggan) {
            return $this->gagal('Sesi pelanggan tidak valid.', 401);
        }

        $items = $request->input('items', []);
        $metodePembayaran = strtolower((string) $request->input('metode_pembayaran', 'qris'));
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

        if (!in_array($metodePembayaran, ['qris', 'midtrans', 'cod'], true)) {
            return $this->gagal('Metode pembayaran tidak valid.', 422);
        }
        if ($metodePembayaran === 'midtrans') {
            $metodePembayaran = 'qris';
        }

        if (!in_array($pengiriman, ['pickup', 'kurir'], true)) {
            return $this->gagal('Metode pengiriman tidak valid.', 422);
        }

        $produkMap = [];
        $totalQty = 0;
        $subtotal = 0;
        $umkmIds = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                return $this->gagal('Format item pesanan tidak valid.', 422);
            }
            $idProduk = (int) ($item['id_produk'] ?? 0);
            $jumlah = filter_var($item['jumlah'] ?? null, FILTER_VALIDATE_INT);

            if ($idProduk <= 0 || $jumlah === false || $jumlah < 1) {
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
            $requestedType = strtolower((string) ($item['jenis_pesanan'] ?? ''));
            $isPreorder = $modePenjualan === 'preorder' ||
                ($modePenjualan === 'keduanya' &&
                    (in_array($requestedType, ['preorder', 'po'], true) ||
                        ($requestedType === '' && !empty($waktuDibutuhkan))));

            if ($isPreorder && (int) $produk->min_order_po > 1 && $jumlah < (int) $produk->min_order_po) {
                return $this->gagal(
                    "Minimal pembelian Pre-Order untuk \"{$produk->nama_produk}\" adalah {$produk->min_order_po} " . ($produk->satuan ?: 'porsi') . '.',
                    422
                );
            }

            if ($isPreorder && $produk->stok < 0) {
                return $this->gagal('Produk pre-order tidak dapat dipesan pada stok yang tidak valid.', 422);
            }

            if (!$isPreorder && $produk->stok < $jumlah) {
                return $this->gagal('Stok produk "' . $produk->nama_produk . '" tidak mencukupi.', 409);
            }

            // Harga dan biaya tidak boleh dipercaya dari client karena menjadi
            // dasar total pembayaran QRIS maupun COD.
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
                'is_preorder' => $isPreorder,
            ];
        }

        $stockRequests = [];
        foreach ($produkMap as $entry) {
            if ($entry['is_preorder']) {
                continue;
            }
            $productId = (int) $entry['produk']->id_produk;
            $stockRequests[$productId] = ($stockRequests[$productId] ?? 0) + (int) $entry['jumlah'];
        }
        foreach ($stockRequests as $productId => $requested) {
            $product = collect($produkMap)->first(
                fn (array $entry) => (int) $entry['produk']->id_produk === (int) $productId
            )['produk'];
            if ((int) $product->stok < $requested) {
                return $this->gagal('Stok produk "' . $product->nama_produk . '" tidak mencukupi.', 409);
            }
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

        $isPreOrder = collect($produkMap)->contains(
            fn (array $entry) => $entry['is_preorder']
        );

        $skemaData = $this->parseSkemaPo($umkm->skema_po);
        $skemaKode = $isPreOrder ? $skemaData['kode'] : 'ready';

        if ($isPreOrder) {
            if ($skemaKode === 'tanpa_dp') {
                // COD diizinkan untuk skema Tanpa DP
            } elseif ($skemaKode === 'dp_30' || $skemaKode === 'dp_50') {
                if ($metodePembayaran === 'cod') {
                    return $this->gagal(
                        "Toko {$umkm->nama_usaha} mewajibkan Uang Muka ({$skemaData['label']}) untuk pesanan Pre-Order. Silakan gunakan pembayaran QRIS.",
                        422
                    );
                }
            } else {
                if ($metodePembayaran === 'cod') {
                    return $this->gagal(
                        "Pembayaran COD tidak tersedia untuk pesanan Pre-Order/Katering dari {$umkm->nama_usaha}. Silakan gunakan QRIS untuk jaminan kepastian pesanan & modal bahan dapur UMKM.",
                        422
                    );
                }
            }

            if (empty($waktuDibutuhkan)) {
                return $this->gagal(
                    'Tanggal dan jam kebutuhan acara wajib dipilih untuk pesanan Pre-Order.',
                    422
                );
            }
        }

        $biayaLayanan = $this->calculateServiceFee($subtotal);
        $totalHarga = $subtotal + $biayaPengantaran + $biayaLayanan;

        if ($isPreOrder && ($skemaKode === 'dp_30' || $skemaKode === 'dp_50')) {
            $nominalDp = (int) round($totalHarga * $skemaData['ratio']);
            $sisaBayar = $totalHarga - $nominalDp;
        } elseif ($isPreOrder && $skemaKode === 'tanpa_dp') {
            $nominalDp = 0;
            $sisaBayar = $totalHarga;
        } elseif ($metodePembayaran === 'cod') {
            $nominalDp = 0;
            $sisaBayar = $totalHarga;
        } else {
            $nominalDp = $totalHarga;
            $sisaBayar = 0;
        }

        $orderId = $this->buildOrderId();

        $statusPesanan = 'po_baru';
        $statusPembayaran = $metodePembayaran === 'qris' ? 'pending' : 'bayar_di_tempat';
        $destLat = $request->filled('latitude') ? (float) $request->input('latitude') : null;
        $destLon = $request->filled('longitude') ? (float) $request->input('longitude') : null;

        try {
            return DB::transaction(function () use (
            $midtransService,
            $pelanggan,
            $orderId,
            $umkm,
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
            $nominalDp,
            $sisaBayar,
            $skemaKode,
            $umkmIds,
            $produkMap,
            $destLat,
            $destLon,
            $isPreOrder,
            $request,
            $stockRequests,
        ) {
            if ($stockRequests !== []) {
                $lockedProducts = Produk::query()
                    ->whereIn('id_produk', array_keys($stockRequests))
                    ->orderBy('id_produk')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id_produk');
                $reservedQuantities = app(StockReservationService::class)
                    ->reservedReadyStock(array_map('intval', array_keys($stockRequests)));
                foreach ($stockRequests as $productId => $requested) {
                    $product = $lockedProducts->get($productId);
                    if (!$product || !(bool) $product->status_ketersediaan ||
                        (int) $product->stok < $requested + ($reservedQuantities[$productId] ?? 0)) {
                        return $this->gagal('Stok produk tidak mencukupi atau sudah dipesan.', 409);
                    }
                }
            }

            $waktuKebutuhanFinal = $isPreOrder
                ? ($waktuDibutuhkan ?? now()->addDay()->format('Y-m-d H:i:s'))
                : now()->addMinutes(30)->format('Y-m-d H:i:s');
            $batasKonfirmasi = $isPreOrder
                ? now()->addHours(24)->format('Y-m-d H:i:s')
                : now()->addMinutes(30)->format('Y-m-d H:i:s');
            $keperluanAcaraFinal = $isPreOrder ? $keperluanAcara : '';

            $order = Pesanan::create([
                'id_pesanan' => $orderId,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_umkm' => $umkmIds[0],
                'nama_umkm_snapshot' => $umkm->nama_usaha ?? 'UMKM',
                'tanggal_pesan' => now()->format('Y-m-d H:i:s'),
                'waktu_dibutuhkan' => $waktuKebutuhanFinal,
                'batas_konfirmasi' => $batasKonfirmasi,
                'keperluan_acara' => $keperluanAcaraFinal,
                'metode_pengiriman' => $pengiriman,
                'alamat_tujuan' => $alamat,
                'latitude_tujuan' => $destLat,
                'longitude_tujuan' => $destLon,
                'catatan' => $catatan,
                'metode_pembayaran' => $metodePembayaran,
                'skema_po' => $skemaKode,
                'status_pembayaran' => $statusPembayaran,
                'payment_status' => $metodePembayaran === 'cod' ? 'cod_pending' : 'pending',
                'biaya_pengantaran' => $biayaPengantaran,
                'biaya_layanan' => $biayaLayanan,
                'total_harga' => $totalHarga,
                'nominal_dp' => $nominalDp,
                'sisa_bayar' => $sisaBayar,
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
                    'jenis_pesanan' => $entry['is_preorder'] ? 'preorder' : 'ready',
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

            if ($metodePembayaran === 'qris') {
                try {
                    $amountToCharge = ($nominalDp > 0 && $nominalDp < $totalHarga) ? $nominalDp : $totalHarga;
                    if ($amountToCharge < $totalHarga) {
                        $midtransItems = [
                            [
                                'id' => 'DP-' . substr($order->id_pesanan, -8),
                                'price' => $amountToCharge,
                                'quantity' => 1,
                                'name' => 'Uang Muka (DP) Pesanan ' . $order->id_pesanan,
                            ]
                        ];
                    } else {
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
                                'name' => 'Biaya platform 3%',
                            ];
                        }
                    }

                    $transaksi = $midtransService->createTransaction(
                        $order->id_pesanan,
                        $amountToCharge,
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
        } catch (QrisPaymentException $error) {
            \Log::error('Checkout QRIS could not create a payment session.', [
                'reason' => $error->getMessage(),
                'status' => $error->getCode(),
            ]);

            return $this->gagal(
                'Pembayaran QRIS belum dapat dibuat. Silakan hubungi admin untuk memeriksa konfigurasi pembayaran.',
                503
            );
        }
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

        $payment = strtolower((string) $request->input('metode_pembayaran', 'qris'));
        if (!in_array($payment, ['qris', 'midtrans', 'cod'], true)) {
            return $this->gagal('Metode pembayaran tidak valid.', 422);
        }
        if ($payment === 'midtrans') {
            $payment = 'qris';
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
                    if (!is_array($item)) {
                        return $this->gagal('Format item pesanan tidak valid.', 422);
                    }
                    $produk = Produk::find((int) ($item['id_produk'] ?? 0));
                    $jumlah = filter_var($item['jumlah'] ?? null, FILTER_VALIDATE_INT);
                    if ($jumlah === false || $jumlah < 1) {
                        return $this->gagal('Jumlah produk harus berupa bilangan bulat positif.', 422);
                    }
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
                $requestedType = strtolower((string) ($item['jenis_pesanan'] ?? ''));
                $isPreorderItem = $mode === 'preorder' ||
                    ($mode === 'keduanya' &&
                        (in_array($requestedType, ['preorder', 'po'], true) ||
                            ($requestedType === '' && !empty($group['waktu_dibutuhkan']))));
                if ($isPreorderItem && (int) $produk->min_order_po > 1 && $jumlah < (int) $produk->min_order_po) {
                    return $this->gagal(
                        "Minimal pembelian Pre-Order untuk \"{$produk->nama_produk}\" adalah {$produk->min_order_po} " . ($produk->satuan ?: 'porsi') . '.',
                        422
                    );
                }
                if (!$isPreorderItem && $produk->stok < $jumlah) {
                    return $this->gagal('Stok produk "' . $produk->nama_produk . '" tidak mencukupi.', 409);
                }
                $harga = (int) $produk->harga;
                $lineTotal = $harga * $jumlah;
                $subtotal += $lineTotal;
                $produkMap[] = compact('produk', 'jumlah', 'harga', 'lineTotal', 'isPreorderItem');
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

            $isPreOrder = collect($produkMap)->contains(
                fn (array $entry) => $entry['isPreorderItem']
            );

            $skemaData = $this->parseSkemaPo($umkm->skema_po);
            $skemaKode = $isPreOrder ? $skemaData['kode'] : 'ready';

            if ($isPreOrder) {
                if ($skemaKode === 'tanpa_dp') {
                    // COD diizinkan untuk skema Tanpa DP
                } elseif ($skemaKode === 'dp_30' || $skemaKode === 'dp_50') {
                    if ($payment === 'cod') {
                        return $this->gagal(
                            "Toko {$umkm->nama_usaha} mewajibkan Uang Muka ({$skemaData['label']}) untuk pesanan Pre-Order. Silakan gunakan pembayaran QRIS.",
                            422
                        );
                    }
                } else {
                    if ($payment === 'cod') {
                        return $this->gagal(
                            "Pembayaran COD tidak tersedia untuk pesanan Pre-Order/Katering dari {$umkm->nama_usaha}. Silakan gunakan QRIS untuk jaminan kepastian pesanan & modal bahan dapur UMKM.",
                            422
                        );
                    }
                }

                $waktuDibutuhkan = $group['waktu_dibutuhkan'] ?? null;
                if (empty($waktuDibutuhkan)) {
                    return $this->gagal(
                        'Tanggal dan jam kebutuhan acara wajib dipilih untuk pesanan Pre-Order.',
                        422
                    );
                }
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
            $destLat = $request->filled('latitude') ? (float) $request->input('latitude') : ($deliveryAddress?->latitude !== null ? (float) $deliveryAddress->latitude : null);
            $destLon = $request->filled('longitude') ? (float) $request->input('longitude') : ($deliveryAddress?->longitude !== null ? (float) $deliveryAddress->longitude : null);
            $biayaLayanan = $this->calculateServiceFee($subtotal);
            $total = $subtotal + $ongkir + $biayaLayanan;

            if ($isPreOrder && ($skemaKode === 'dp_30' || $skemaKode === 'dp_50')) {
                $nominalDp = (int) round($total * $skemaData['ratio']);
                $sisaBayar = $total - $nominalDp;
            } elseif ($isPreOrder && $skemaKode === 'tanpa_dp') {
                $nominalDp = 0;
                $sisaBayar = $total;
            } elseif ($payment === 'cod') {
                $nominalDp = 0;
                $sisaBayar = $total;
            } else {
                $nominalDp = $total;
                $sisaBayar = 0;
            }

            $grandTotal += $total;
            $prepared[] = compact('umkmId', 'produkMap', 'delivery', 'ongkir', 'jarakKm', 'zonaOngkirId', 'biayaLayanan', 'total', 'group', 'destLat', 'destLon', 'isPreOrder', 'skemaKode', 'nominalDp', 'sisaBayar');
        }

        $stockRequests = [];
        foreach ($prepared as $entry) {
            foreach ($entry['produkMap'] as $line) {
                if ($line['isPreorderItem']) {
                    continue;
                }
                $productId = (int) $line['produk']->id_produk;
                $stockRequests[$productId] = ($stockRequests[$productId] ?? 0) + (int) $line['jumlah'];
            }
        }
        foreach ($stockRequests as $productId => $requested) {
            foreach ($prepared as $entry) {
                foreach ($entry['produkMap'] as $line) {
                    if ((int) $line['produk']->id_produk === (int) $productId &&
                        (int) $line['produk']->stok < $requested) {
                        return $this->gagal(
                            'Stok produk "' . $line['produk']->nama_produk . '" tidak mencukupi.',
                            409
                        );
                    }
                }
            }
        }

        $grandNominalDp = collect($prepared)->sum('nominalDp');
        $grandSisaBayar = collect($prepared)->sum('sisaBayar');
        $primarySkemaPo = $prepared[0]['skemaKode'] ?? 'ready';

        $checkoutId = 'CHK-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));
        try {
            $checkoutResult = DB::transaction(function () use ($prepared, $checkoutId, $pelanggan, $payment, $grandTotal, $grandNominalDp, $grandSisaBayar, $primarySkemaPo, $alamat, $stockRequests, $midtransService) {
            if ($stockRequests !== []) {
                $lockedProducts = Produk::query()
                    ->whereIn('id_produk', array_keys($stockRequests))
                    ->orderBy('id_produk')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id_produk');
                $reservedQuantities = app(StockReservationService::class)
                    ->reservedReadyStock(array_map('intval', array_keys($stockRequests)));
                foreach ($stockRequests as $productId => $requested) {
                    $product = $lockedProducts->get($productId);
                    if (!$product || !(bool) $product->status_ketersediaan ||
                        (int) $product->stok < $requested + ($reservedQuantities[$productId] ?? 0)) {
                        return $this->gagal('Stok produk tidak mencukupi atau sudah dipesan.', 409);
                    }
                }
            }

            CheckoutGroup::create([
                'id_checkout' => $checkoutId,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'metode_pembayaran' => $payment,
                'skema_po' => $primarySkemaPo,
                'total_harga' => $grandTotal,
                'nominal_dp' => $grandNominalDp,
                'sisa_bayar' => $grandSisaBayar,
                'payment_status' => $payment === 'cod' ? 'cod_pending' : 'pending',
            ]);

            $orders = [];
            foreach ($prepared as $entry) {
                $isPreOrderEntry = $entry['isPreOrder'] ?? false;
                $waktuKebutuhan = $isPreOrderEntry
                    ? ($entry['group']['waktu_dibutuhkan'] ?? now()->addDay()->format('Y-m-d H:i:s'))
                    : now()->addMinutes(30)->format('Y-m-d H:i:s');
                $batasKonfirmasi = $isPreOrderEntry
                    ? now()->addHours(24)->format('Y-m-d H:i:s')
                    : now()->addMinutes(30)->format('Y-m-d H:i:s');
                $keperluanAcara = $isPreOrderEntry
                    ? trim((string) ($entry['group']['keperluan_acara'] ?? ''))
                    : '';

                $order = Pesanan::create([
                    'id_pesanan' => $this->buildOrderId(),
                    'id_checkout' => $checkoutId,
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'id_umkm' => $entry['umkmId'],
                    'nama_umkm_snapshot' => Umkm::find($entry['umkmId'])?->nama_usaha ?? 'UMKM',
                    'tanggal_pesan' => now()->format('Y-m-d H:i:s'),
                    'waktu_dibutuhkan' => $waktuKebutuhan,
                    'batas_konfirmasi' => $batasKonfirmasi,
                    'keperluan_acara' => $keperluanAcara,
                    'metode_pengiriman' => $entry['delivery'],
                    'alamat_tujuan' => $alamat,
                    'latitude_tujuan' => $entry['destLat'] ?? null,
                    'longitude_tujuan' => $entry['destLon'] ?? null,
                    'catatan' => trim((string) ($entry['group']['catatan'] ?? '')),
                    'metode_pembayaran' => $payment,
                    'skema_po' => $entry['skemaKode'],
                    'status_pembayaran' => $payment === 'cod' ? 'bayar_di_tempat' : 'pending',
                    'payment_status' => $payment === 'cod' ? 'cod_pending' : 'pending',
                    'biaya_pengantaran' => $entry['ongkir'],
                    'jarak_km' => $entry['jarakKm'],
                    'zona_ongkir_id' => $entry['zonaOngkirId'],
                    'biaya_layanan' => $entry['biayaLayanan'] ?? 0,
                    'total_harga' => $entry['total'],
                    'nominal_dp' => $entry['nominalDp'],
                    'sisa_bayar' => $entry['sisaBayar'],
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
                        'jenis_pesanan' => $line['isPreorderItem'] ? 'preorder' : 'ready',
                    ]);
                }
                $orders[] = $order;
            }
            $paymentData = ['token' => '', 'redirect_url' => '', 'transaction_id' => ''];
            if ($payment === 'qris') {
                $amountToCharge = ($grandNominalDp > 0 && $grandNominalDp < $grandTotal)
                    ? $grandNominalDp
                    : $grandTotal;
                if ($amountToCharge < $grandTotal) {
                    $items = [[
                        'id' => 'DP-' . substr($checkoutId, -8),
                        'price' => $amountToCharge,
                        'quantity' => 1,
                        'name' => 'Uang Muka (DP) Pesanan ' . $checkoutId,
                    ]];
                } else {
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
                            $items[] = [
                                'id' => 'delivery-' . $entry['umkmId'],
                                'price' => $entry['ongkir'],
                                'quantity' => 1,
                                'name' => 'Biaya pengantaran',
                            ];
                        }
                        if (($entry['biayaLayanan'] ?? 0) > 0) {
                            $items[] = [
                                'id' => 'service-' . $entry['umkmId'],
                                'price' => $entry['biayaLayanan'],
                                'quantity' => 1,
                                'name' => 'Biaya platform 3%',
                            ];
                        }
                    }
                }

                $paymentData = $midtransService->createTransaction(
                    $checkoutId,
                    $amountToCharge,
                    $items,
                    [
                        'first_name' => $pelanggan->nama,
                        'email' => $pelanggan->email ?? 'customer@djajan.id',
                        'phone' => $pelanggan->no_telp ?? '',
                    ],
                );
                $paymentUpdate = [
                    'midtrans_order_id' => $paymentData['transaction_id'] ?: $checkoutId,
                    'snap_token' => $paymentData['token'] ?: null,
                    'payment_url' => $paymentData['redirect_url'] ?: null,
                ];
                CheckoutGroup::whereKey($checkoutId)->update($paymentUpdate);
                Pesanan::where('id_checkout', $checkoutId)->update($paymentUpdate);
            }

                return ['orders' => $orders, 'paymentData' => $paymentData];
            });
        } catch (QrisPaymentException $error) {
            \Log::error('Checkout QRIS could not create a payment session.', [
                'reason' => $error->getMessage(),
                'status' => $error->getCode(),
            ]);

            return $this->gagal(
                'Pembayaran QRIS belum dapat dibuat. Silakan hubungi admin untuk memeriksa konfigurasi pembayaran.',
                503
            );
        }

        if ($checkoutResult instanceof JsonResponse) {
            return $checkoutResult;
        }

        $orders = $checkoutResult['orders'];
        $paymentData = $checkoutResult['paymentData'];

        return $this->sukses('Pesanan berhasil dibuat.', [
            'id_checkout' => $checkoutId,
            'metode_pembayaran' => $payment,
            'total_harga' => $grandTotal,
            'nominal_dp' => $grandNominalDp,
            'sisa_bayar' => $grandSisaBayar,
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
            $expectedAmount = ($checkoutGroup->nominal_dp > 0 && $checkoutGroup->nominal_dp < $checkoutGroup->total_harga)
                ? (float) $checkoutGroup->nominal_dp
                : (float) $checkoutGroup->total_harga;

            $grossAmount = (float) ($payload['gross_amount'] ?? -1);
            if ($grossAmount < 0 || abs($grossAmount - $expectedAmount) > 0.001) {
                \Log::warning('Midtrans callback: amount mismatch (group)', [
                    'order_id' => $orderId,
                    'expected' => $expectedAmount,
                    'received' => $payload['gross_amount'] ?? null,
                ]);
                return response()->json(['status' => 'amount_mismatch'], 200);
            }
            if (in_array($checkoutGroup->payment_status, ['paid', 'dp_terbayar', 'failed'], true)) {
                return response()->json(['status' => 'success', 'payment_status' => $checkoutGroup->payment_status], 200);
            }
            $newStatus = $this->mapMidtransStatus($transactionStatus, $checkoutGroup->skema_po);
            $changes = ['payment_status' => $newStatus];
            if ($newStatus === 'paid' || $newStatus === 'dp_terbayar') {
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
            if ($newStatus === 'paid' || $newStatus === 'dp_terbayar') {
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

        $expectedAmount = ($order->nominal_dp > 0 && $order->nominal_dp < $order->total_harga)
            ? (float) $order->nominal_dp
            : (float) $order->total_harga;

        $grossAmount = (float) ($payload['gross_amount'] ?? -1);
        if ($grossAmount < 0 || abs($grossAmount - $expectedAmount) > 0.001) {
            \Log::warning('Midtrans callback: amount mismatch (order)', [
                'order_id' => $order->id_pesanan,
                'expected' => $expectedAmount,
                'received' => $payload['gross_amount'] ?? null,
            ]);
            return response()->json(['status' => 'amount_mismatch'], 200);
        }

        if (!in_array($transactionStatus, ['capture', 'settlement', 'pending', 'deny', 'cancel', 'expire'], true)) {
            return response()->json(['status' => 'ignored'], 200);
        }

        if (in_array($order->payment_status, ['paid', 'dp_terbayar', 'failed'], true)) {
            return response()->json([
                'status'         => 'success',
                'order_id'       => $order->id_pesanan,
                'payment_status' => $order->payment_status,
            ], 200);
        }

        $newStatus = $this->mapMidtransStatus($transactionStatus, $order->skema_po);
        $newOrderStatus = $order->status_pesanan;
        if ($newStatus === 'failed') {
            $newOrderStatus = 'ditolak';
        }

        $changes = [
            'status_pembayaran' => $newStatus,
            'payment_status'    => $newStatus,
            'status_pesanan'    => $newOrderStatus,
        ];
        if ($newStatus === 'paid' || $newStatus === 'dp_terbayar') {
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
     * Parsing skema PO dari setting UMKM menjadi kode dan persentase DP.
     */
    private function parseSkemaPo(?string $skemaRaw): array
    {
        $raw = (string) ($skemaRaw ?? 'Lunas di Awal (Full Payment)');
        if (str_contains($raw, '30%')) {
            return ['kode' => 'dp_30', 'ratio' => 0.30, 'label' => 'DP 30%'];
        }
        if (str_contains($raw, '50%')) {
            return ['kode' => 'dp_50', 'ratio' => 0.50, 'label' => 'DP 50%'];
        }
        if (str_contains(strtolower($raw), 'tanpa dp') || str_contains(strtolower($raw), 'bayar saat')) {
            return ['kode' => 'tanpa_dp', 'ratio' => 0.0, 'label' => 'Tanpa DP'];
        }
        return ['kode' => 'full', 'ratio' => 1.0, 'label' => 'Lunas di Awal'];
    }

    /**
     * Map Midtrans transaction_status ke status internal.
     */
    private function mapMidtransStatus(string $transactionStatus, ?string $skemaPo = null): string
    {
        $base = match (true) {
            in_array($transactionStatus, ['capture', 'settlement'], true) => 'paid',
            in_array($transactionStatus, ['deny', 'cancel', 'expire'], true) => 'failed',
            default => 'pending',
        };
        if ($base === 'paid' && in_array($skemaPo, ['dp_30', 'dp_50'], true)) {
            return 'dp_terbayar';
        }
        return $base;
    }

    private function syncPaymentStatus(Pesanan $order, MidtransService $midtransService): void
    {
        if (!in_array($order->metode_pembayaran, ['qris', 'midtrans'], true)
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
        $newStatus = $this->mapMidtransStatus($transactionStatus, $order->skema_po);
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
            if ($newStatus === 'paid' || $newStatus === 'dp_terbayar') {
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
        if (!in_array($group->metode_pembayaran, ['qris', 'midtrans'], true)
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
        $newStatus = $this->mapMidtransStatus($status, $group->skema_po);
        if ($newStatus === $group->payment_status) {
            return;
        }
        $group->update([
            'payment_status' => $newStatus,
            'dibayar_pada' => ($newStatus === 'paid' || $newStatus === 'dp_terbayar') ? now() : $group->dibayar_pada,
        ]);
        $changes = [
            'payment_status' => $newStatus,
            'status_pembayaran' => $newStatus,
        ];
        if ($newStatus === 'failed') {
            $changes['status_pesanan'] = 'ditolak';
        }
        if ($newStatus === 'paid' || $newStatus === 'dp_terbayar') {
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

        // Koordinat default Desa Kreyongan Atas (-8.1556187, 113.7027475) jika UMKM belum set titik
        $umkmLat = $umkm->latitude !== null ? (float) $umkm->latitude : -8.1556187;
        $umkmLon = $umkm->longitude !== null ? (float) $umkm->longitude : 113.7027475;

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

    private function calculateServiceFee(int $subtotal): int
    {
        return (int) round($subtotal * 0.03);
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

    public function strukHtml(string $id_pesanan)
    {
        $pesanan = Pesanan::with(['items.produk', 'umkm', 'pelanggan'])
            ->where('id_pesanan', $id_pesanan)
            ->firstOrFail();

        return view('pesanan.struk', compact('pesanan'));
    }
}
