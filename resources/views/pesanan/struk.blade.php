<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $pesanan->id_pesanan }} - DJajan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f3f4f6; color: #1f2937; padding: 24px 12px; }
        .receipt-container { max-width: 520px; margin: 0 auto; background: #fff; padding: 28px 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .header { text-align: center; border-bottom: 2px dashed #e5e7eb; padding-bottom: 16px; margin-bottom: 16px; }
        .brand { font-size: 24px; font-weight: 800; color: #00880C; letter-spacing: 1px; }
        .subtitle { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .meta-box { display: flex; justify-content: space-between; font-size: 11.5px; margin-bottom: 6px; }
        .meta-box span:first-child { color: #6b7280; }
        .meta-box span:last-child { font-weight: 600; color: #111827; }
        .divider { border-top: 1px solid #e5e7eb; margin: 12px 0; }
        .table-items { width: 100%; border-collapse: collapse; font-size: 12px; margin: 14px 0; }
        .table-items th { text-align: left; padding: 8px 4px; border-bottom: 1.5px solid #e5e7eb; color: #4b5563; font-size: 11px; }
        .table-items td { padding: 8px 4px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .table-items td.right, .table-items th.right { text-align: right; }
        .table-items td.center, .table-items th.center { text-align: center; }
        .item-name { font-weight: 600; color: #111827; }
        .item-sub { font-size: 10px; color: #6b7280; }
        .summary-row { display: flex; justify-content: space-between; font-size: 12px; padding: 3px 0; }
        .summary-row.total { font-size: 15px; font-weight: 800; color: #00880C; border-top: 1.5px solid #e5e7eb; margin-top: 8px; padding-top: 8px; }
        .footer { text-align: center; font-size: 10.5px; color: #6b7280; margin-top: 20px; padding-top: 14px; border-top: 2px dashed #e5e7eb; line-height: 1.4; }
        .btn-print { display: block; width: 100%; padding: 12px; background: #00880C; color: #fff; font-size: 13px; font-weight: 700; border: none; border-radius: 8px; cursor: pointer; margin-top: 18px; text-align: center; text-decoration: none; }
        .btn-print:hover { background: #00700a; }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt-container { box-shadow: none; padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <div class="brand">DJAJAN</div>
            <div class="subtitle">Pasar Kuliner Hyperlocal UMKM Desa Kreyongan Atas</div>
            <div style="margin-top: 6px; font-size: 12px; font-weight: 700; color: #374151;">STRUK TRANSAKSI RESMI</div>
        </div>

        <div class="meta-box">
            <span>No. Pesanan:</span>
            <span>#{{ $pesanan->id_pesanan }}</span>
        </div>
        <div class="meta-box">
            <span>Tanggal & Waktu:</span>
            <span>{{ $pesanan->tanggal_pesan }}</span>
        </div>
        <div class="meta-box">
            <span>Penjual / Toko:</span>
            <span>{{ $pesanan->nama_umkm_snapshot ?? $pesanan->umkm?->nama_usaha ?? 'UMKM Kreyongan' }}</span>
        </div>
        <div class="meta-box">
            <span>Pelanggan:</span>
            <span>{{ $pesanan->pelanggan?->nama ?? 'Pelanggan' }} ({{ $pesanan->pelanggan?->no_telp ?? '-' }})</span>
        </div>
        <div class="meta-box">
            <span>Alamat Tujuan:</span>
            <span>{{ $pesanan->alamat_tujuan ?? 'Ambil Sendiri di Toko' }}</span>
        </div>
        <div class="meta-box">
            <span>Metode Pengiriman:</span>
            <span>{{ $pesanan->metode_pengiriman === 'kurir' ? 'Diantar Kurir Warga' : 'Ambil Sendiri (Pickup)' }}</span>
        </div>
        <div class="meta-box">
            <span>Status Transaksi:</span>
            <span style="text-transform: uppercase;">{{ $pesanan->status_pesanan }} ({{ $pesanan->status_pembayaran }})</span>
        </div>

        <table class="table-items">
            <thead>
                <tr>
                    <th>Menu / Produk</th>
                    <th class="center">Qty</th>
                    <th class="right">Harga</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pesanan->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->produk?->nama_produk ?? 'Produk' }}</div>
                        @if($item->opsi)
                            <div class="item-sub">Varian: {{ $item->opsi }}</div>
                        @endif
                    </td>
                    <td class="center">{{ $item->jumlah }}x</td>
                    <td class="right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="right" style="font-weight: 600;">Rp {{ number_format($item->subtotal ?? ($item->harga_satuan * $item->jumlah), 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-box">
            <div class="summary-row">
                <span>Subtotal Menu:</span>
                <span>Rp {{ number_format($pesanan->items->sum(fn($i) => ($i->harga_satuan * $i->jumlah)), 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span>Biaya Pengantaran (Ongkir):</span>
                <span>{{ $pesanan->biaya_pengantaran > 0 ? 'Rp ' . number_format($pesanan->biaya_pengantaran, 0, ',', '.') : 'Gratis' }}</span>
            </div>
            <div class="summary-row">
                <span>Biaya Layanan:</span>
                <span>{{ $pesanan->biaya_layanan > 0 ? 'Rp ' . number_format($pesanan->biaya_layanan, 0, ',', '.') : 'Gratis' }}</span>
            </div>
            <div class="summary-row total">
                <span>TOTAL PEMBAYARAN:</span>
                <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            Terima kasih telah berbelanja dan memajukan produk UMKM Desa Kreyongan Atas.<br>
            <strong>DJajan - Dari Desa untuk Warga</strong>
        </div>

        <button onclick="window.print()" class="btn-print no-print">🖨️ Cetak / Simpan Struk PDF</button>
    </div>
</body>
</html>
