<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Omzet UMKM</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 35mm 14mm 22mm;
        }

        * { box-sizing: border-box; }

        body {
            color: #111;
            font-family: "DejaVu Serif", serif;
            font-size: 9pt;
            line-height: 1.35;
        }

        .running-header {
            border-bottom: 1px solid #777;
            height: 23mm;
            left: 0;
            position: fixed;
            right: 0;
            top: -27mm;
        }

        .brand-table,
        .footer-table,
        .report-meta {
            border-collapse: collapse;
            width: 100%;
        }

        .brand-table td { vertical-align: middle; }
        .brand-mark { padding-right: 3mm; width: 20mm; }
        .brand-logo { height: 17mm; width: 17mm; }
        .brand-name {
            color: #111;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 14pt;
            font-weight: bold;
        }
        .brand-copy { color: #333; font-size: 7.5pt; }
        .header-document { color: #111; font-size: 8pt; text-align: right; }
        .header-document strong { font-size: 8.5pt; }

        .report-footer {
            border-top: 1px solid #777;
            bottom: -16mm;
            color: #111;
            font-size: 7.5pt;
            height: 11mm;
            left: 0;
            position: fixed;
            right: 0;
        }

        .footer-table { margin-top: 2mm; }
        .footer-table td { vertical-align: top; }
        .footer-brand { font-style: italic; }

        .title-block {
            border-bottom: 1px solid #777;
            margin-bottom: 4mm;
            padding: 0 0 2.5mm;
            text-align: center;
        }
        .document-type {
            font-size: 8pt;
            margin: 0 0 1.5mm;
        }
        h1 {
            color: #111;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 14pt;
            font-weight: bold;
            line-height: 1.2;
            margin: 0 0 1mm;
        }
        .period-title { font-size: 10pt; font-weight: bold; margin: 0; }
        .report-meta { margin-top: 2mm; }
        .report-meta td { font-size: 8pt; }
        .report-meta .right { text-align: right; }

        .section { margin-top: 3.5mm; }
        .section-heading {
            color: #111;
            font-size: 11pt;
            font-weight: bold;
            margin: 0 0 1.5mm;
            page-break-after: avoid;
        }
        .roman { display: inline-block; width: 10mm; }
        .section-note { font-size: 8pt; margin: 0 0 1.5mm; }

        table { border-collapse: collapse; width: 100%; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }

        .report-table { table-layout: fixed; }
        .report-table th,
        .report-table td {
            border: .5pt solid #aaa;
            padding: 1.4mm 1.5mm;
            vertical-align: middle;
        }
        .report-table th {
            background: #e8e8e8;
            color: #111;
            font-size: 7.6pt;
            font-weight: bold;
            text-align: center;
        }
        .report-table td {
            background: #fff;
            color: #111;
            font-size: 7.7pt;
        }
        .report-table .right { text-align: right; }
        .report-table .center { text-align: center; }
        .report-table .total-row td {
            background: #ededed;
            font-weight: bold;
        }
        .empty-row td {
            padding: 3mm 1.5mm;
            text-align: center;
        }
        .order-number {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 7pt;
        }
    </style>
</head>
<body>
    @php
        $statistics = $report['statistics'];
        $totalMerchantOrders = collect($report['merchants'])->sum('jumlah_transaksi');
        $currency = fn ($amount) => 'Rp '.number_format((int) $amount, 0, ',', '.');
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $longDate = fn ($date) => \Carbon\Carbon::parse($date)->format('d').' '.$months[(int) \Carbon\Carbon::parse($date)->format('n')].' '.\Carbon\Carbon::parse($date)->format('Y');
        $periodLabel = $longDate($mulai).' – '.$longDate($akhir);
        $printedDate = $longDate($printedAt).', '.$printedAt->format('H:i').' WIB';
        $paymentLabel = fn ($status) => match (strtolower((string) $status)) {
            'lunas', 'paid', 'settlement' => 'Lunas',
            'capture' => 'Berhasil',
            'belum_bayar' => 'Belum dibayar',
            'pending' => 'Menunggu pembayaran',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            default => $status ? ucfirst(str_replace('_', ' ', (string) $status)) : '—',
        };
        $orderLabel = fn ($status) => match (strtolower((string) $status)) {
            'selesai' => 'Selesai',
            'diproses' => 'Diproses',
            'menunggu' => 'Menunggu',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
            default => $status ? ucfirst(str_replace('_', ' ', (string) $status)) : '—',
        };
    @endphp

    <header class="running-header">
        <table class="brand-table">
            <tr>
                <td class="brand-mark">
                    <img class="brand-logo" src="{{ $logo }}" alt="Logo Djajan">
                </td>
                <td>
                    <div class="brand-name">Djajan</div>
                    <div class="brand-copy">Platform Digital UMKM Lokal</div>
                    <div class="brand-copy">Desa Kreongan Atas</div>
                </td>
                <td class="header-document">
                    <strong>Dokumen Internal</strong><br>
                    Dicetak: {{ $printedDate }}
                </td>
            </tr>
        </table>
    </header>

    <footer class="report-footer">
        <table class="footer-table">
            <tr>
                <td class="footer-brand">Dokumen Internal Djajan</td>
            </tr>
        </table>
    </footer>

    <main>
        <div class="title-block">
            <p class="document-type">Laporan Administrasi</p>
            <h1>LAPORAN REKAPITULASI OMZET UMKM</h1>
            <p class="period-title">Periode {{ $periodLabel }}</p>
        </div>

        <section class="section">
            <h2 class="section-heading"><span class="roman">I.</span>Ringkasan Omzet</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 58%">Uraian</th>
                        <th style="width: 42%">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Omzet Produk</td>
                        <td class="right"><strong>{{ $currency($statistics['total_omzet']) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Checkout Berhasil</td>
                        <td class="right">{{ number_format($statistics['total_transaksi'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>UMKM Menghasilkan Omzet</td>
                        <td class="right">{{ number_format($statistics['total_umkm_terjual'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Produk Terjual</td>
                        <td class="right">{{ number_format($statistics['total_produk_terjual'], 0, ',', '.') }} unit</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2 class="section-heading"><span class="roman">II.</span>Rekapitulasi Omzet UMKM</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 8%">No.</th>
                        <th style="width: 31%">Nama UMKM</th>
                        <th style="width: 19%">Jumlah Pesanan</th>
                        <th style="width: 24%">Omzet Produk</th>
                        <th style="width: 18%">Kontribusi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['merchants'] as $index => $merchant)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ $merchant['nama_usaha'] }}</td>
                            <td class="right">{{ number_format($merchant['jumlah_transaksi'], 0, ',', '.') }}</td>
                            <td class="right">{{ $currency($merchant['omzet']) }}</td>
                            <td class="right">{{ number_format($merchant['kontribusi_persen'], 1, ',', '.') }}%</td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="5">Tidak ada UMKM terverifikasi untuk ditampilkan.</td></tr>
                    @endforelse
                    <tr class="total-row">
                        <td class="center" colspan="2">Total</td>
                        <td class="right">{{ number_format($totalMerchantOrders, 0, ',', '.') }}</td>
                        <td class="right">{{ $currency($statistics['total_omzet']) }}</td>
                        <td class="right">{{ $statistics['total_omzet'] > 0 ? '100%' : '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2 class="section-heading"><span class="roman">III.</span>Rincian Transaksi</h2>
            <p class="section-note">
                Berikut seluruh transaksi yang memenuhi kriteria omzet pada periode laporan.
            </p>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 14%">Tanggal</th>
                        <th style="width: 23%">Nama UMKM</th>
                        <th style="width: 23%">Nomor Pesanan</th>
                        <th style="width: 22%">Status Pembayaran / Pesanan</th>
                        <th style="width: 18%">Nominal Produk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['transactions'] as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction['tanggal'])->format('d/m/Y') }}</td>
                            <td>{{ $transaction['nama_umkm'] }}</td>
                            <td class="order-number">{{ $transaction['id_pesanan'] }}</td>
                            <td>{{ $paymentLabel($transaction['status_pembayaran']) }} / {{ $orderLabel($transaction['status_pesanan']) }}</td>
                            <td class="right">{{ $currency($transaction['omzet']) }}</td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="5">Tidak ada transaksi yang memenuhi kriteria omzet pada periode ini.</td></tr>
                    @endforelse
                    <tr class="total-row">
                        <td colspan="4">Total Omzet Produk</td>
                        <td class="right">{{ $currency($statistics['total_omzet']) }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

    </main>
</body>
</html>
