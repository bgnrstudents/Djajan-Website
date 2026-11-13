<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AdminOmzetDashboard
{
    public function data(
        string $mulai,
        string $akhir,
        string $granularity = 'day',
        string $search = '',
        int $page = 1,
        ?int $umkmId = null
    ): array {
        $orders = $this->eligibleOrders($mulai, $akhir, $umkmId);
        $totals = DB::query()
            ->fromSub(clone $orders, 'pesanan')
            ->join('detail_pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->selectRaw("COUNT(DISTINCT COALESCE(NULLIF(pesanan.id_checkout, ''), pesanan.id_pesanan)) as total_transaksi")
            ->selectRaw('COUNT(DISTINCT pesanan.id_umkm) as total_umkm_terjual')
            ->selectRaw('COALESCE(SUM(detail_pesanan.subtotal), 0) as total_omzet')
            ->selectRaw('COALESCE(SUM(detail_pesanan.jumlah), 0) as total_produk_terjual')
            ->first();

        $totalTransaksi = (int) $totals->total_transaksi;
        $totalOmzet = (int) $totals->total_omzet;

        $merchantCount = DB::table('umkm')
            ->where('status_verifikasi', 'terverifikasi')
            ->when($umkmId !== null, fn (Builder $query) => $query->where('id_umkm', $umkmId))
            ->when($search !== '', fn (Builder $query) => $query->where('nama_usaha', 'like', '%'.$search.'%'))
            ->count();
        $currentPage = min(max(1, $page), max(1, (int) ceil($merchantCount / 20)));
        $rankingRows = $this->rankingQuery($mulai, $akhir, $search, $umkmId)
            ->forPage($currentPage, 20)
            ->get();
        $periodLength = Carbon::parse($mulai)->diffInDays(Carbon::parse($akhir)) + 1;
        $previousAkhir = Carbon::parse($mulai)->subDay()->toDateString();
        $previousMulai = Carbon::parse($previousAkhir)->subDays($periodLength - 1)->toDateString();
        $previousOmzet = DB::query()
            ->fromSub($this->eligibleOrders($previousMulai, $previousAkhir, $umkmId), 'pesanan')
            ->join('detail_pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->select('pesanan.id_umkm as id_umkm')
            ->selectRaw('SUM(detail_pesanan.subtotal) as omzet')
            ->groupBy('pesanan.id_umkm')
            ->pluck('omzet', 'id_umkm');
        $comparison = $this->rankingQuery($mulai, $akhir, '', $umkmId)
            ->limit(10)
            ->get()
            ->map(fn (object $row): array => [
                'nama_usaha' => $row->nama_usaha,
                'omzet' => (int) $row->omzet,
                'kontribusi_persen' => $totalOmzet > 0
                    ? round(((int) $row->omzet / $totalOmzet) * 100, 1)
                    : 0,
            ])
            ->all();

        $trend = $this->trend($mulai, $akhir, $granularity, $umkmId);
        $exceptions = $this->orderExceptions($mulai, $akhir, $umkmId);

        return [
            'statistics' => [
                'total_omzet' => $totalOmzet,
                'total_transaksi' => $totalTransaksi,
                'total_umkm_terjual' => (int) $totals->total_umkm_terjual,
                'total_produk_terjual' => (int) $totals->total_produk_terjual,
                'rata_rata_transaksi' => $totalTransaksi > 0
                    ? (int) round($totalOmzet / $totalTransaksi)
                    : 0,
                'pesanan_belum_selesai' => $exceptions['pesanan_belum_selesai'],
                'pesanan_dibatalkan' => $exceptions['pesanan_dibatalkan'],
                'refund_menunggu' => $exceptions['refund_menunggu'],
                'refund_berhasil' => $exceptions['refund_berhasil'],
            ],
            'trend' => $trend,
            'comparison' => $comparison,
            'top_products' => $this->topProducts($mulai, $akhir, $totalOmzet, $umkmId),
            'recent_orders' => $this->recentOrders($mulai, $akhir, $umkmId),
            'ranking' => $rankingRows
                ->map(fn (object $row): array => [
                    'nama_usaha' => $row->nama_usaha,
                    'jumlah_transaksi' => (int) $row->jumlah_transaksi,
                    'omzet' => (int) $row->omzet,
                    'kontribusi_persen' => $totalOmzet > 0
                        ? round(((int) $row->omzet / $totalOmzet) * 100, 1)
                        : 0,
                    'perkembangan' => (int) ($previousOmzet[$row->id_umkm] ?? 0) > 0
                        ? round((((int) $row->omzet - (int) $previousOmzet[$row->id_umkm]) / (int) $previousOmzet[$row->id_umkm]) * 100, 1)
                        : null,
                ])
                ->all(),
            'pagination' => [
                'current_page' => $currentPage,
                'per_page' => 20,
                'total' => $merchantCount,
            ],
            'filters' => [
                'mulai' => $mulai,
                'akhir' => $akhir,
                'granularity' => $granularity,
                'search' => $search,
            ],
        ];
    }

    private function periodOrders(string $mulai, string $akhir, ?int $umkmId = null): Builder
    {
        return DB::table('pesanan')
            ->join('umkm', 'umkm.id_umkm', '=', 'pesanan.id_umkm')
            ->select(
                'pesanan.id_pesanan',
                'pesanan.id_umkm',
                'pesanan.id_checkout',
                'pesanan.nama_umkm_snapshot',
                'pesanan.dibayar_pada',
                'pesanan.tanggal_pesan',
                'pesanan.status_pesanan',
                'pesanan.status_pembayaran',
                'pesanan.payment_status',
                'pesanan.status_refund'
            )
            ->where('umkm.status_verifikasi', 'terverifikasi')
            ->whereNotNull('pesanan.id_umkm')
            ->when($umkmId !== null, fn (Builder $query) => $query->where('pesanan.id_umkm', $umkmId))
            ->whereRaw(
                'DATE(COALESCE(pesanan.dibayar_pada, pesanan.tanggal_pesan)) BETWEEN ? AND ?',
                [$mulai, $akhir]
            );
    }

    private function eligibleOrders(string $mulai, string $akhir, ?int $umkmId = null): Builder
    {
        return $this->periodOrders($mulai, $akhir, $umkmId)
            ->where(function (Builder $query): void {
                $query->whereIn('pesanan.payment_status', ['paid', 'settlement', 'capture'])
                    ->orWhereIn('pesanan.status_pembayaran', ['lunas', 'paid', 'settlement', 'capture']);
            })
            ->where('pesanan.status_pesanan', 'selesai')
            ->where(function (Builder $query): void {
                $query->whereNull('pesanan.status_refund')
                    ->orWhere('pesanan.status_refund', 'none');
            })
            ->whereExists(function (Builder $query): void {
                $query->selectRaw('1')
                    ->from('detail_pesanan')
                    ->whereColumn('detail_pesanan.id_pesanan', 'pesanan.id_pesanan');
            });
    }

    private function orderExceptions(string $mulai, string $akhir, ?int $umkmId = null): array
    {
        $counts = DB::query()
            ->fromSub($this->periodOrders($mulai, $akhir, $umkmId), 'pesanan')
            ->selectRaw("COUNT(DISTINCT CASE WHEN pesanan.status_pesanan NOT IN ('selesai', 'ditolak', 'dibatalkan', 'batal', 'cancelled') THEN pesanan.id_pesanan END) as pesanan_belum_selesai")
            ->selectRaw("COUNT(DISTINCT CASE WHEN pesanan.status_pesanan IN ('ditolak', 'dibatalkan', 'batal', 'cancelled') THEN pesanan.id_pesanan END) as pesanan_dibatalkan")
            ->selectRaw("COUNT(DISTINCT CASE WHEN pesanan.status_refund = 'perlu_refund' THEN pesanan.id_pesanan END) as refund_menunggu")
            ->selectRaw("COUNT(DISTINCT CASE WHEN pesanan.status_refund = 'refund_berhasil' THEN pesanan.id_pesanan END) as refund_berhasil")
            ->first();

        return [
            'pesanan_belum_selesai' => (int) $counts->pesanan_belum_selesai,
            'pesanan_dibatalkan' => (int) $counts->pesanan_dibatalkan,
            'refund_menunggu' => (int) $counts->refund_menunggu,
            'refund_berhasil' => (int) $counts->refund_berhasil,
        ];
    }

    private function topProducts(string $mulai, string $akhir, int $totalOmzet, ?int $umkmId): array
    {
        return DB::query()
            ->fromSub($this->eligibleOrders($mulai, $akhir, $umkmId), 'pesanan')
            ->join('detail_pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->join('umkm', 'umkm.id_umkm', '=', 'pesanan.id_umkm')
            ->select('pesanan.id_umkm', 'detail_pesanan.nama_produk')
            ->selectRaw('COALESCE(pesanan.nama_umkm_snapshot, umkm.nama_usaha) as nama_umkm')
            ->selectRaw('SUM(detail_pesanan.jumlah) as jumlah_terjual')
            ->selectRaw('SUM(detail_pesanan.subtotal) as omzet')
            ->groupByRaw('pesanan.id_umkm, detail_pesanan.nama_produk, COALESCE(pesanan.nama_umkm_snapshot, umkm.nama_usaha)')
            ->orderByDesc('jumlah_terjual')
            ->orderByDesc('omzet')
            ->limit(10)
            ->get()
            ->map(fn (object $row): array => [
                'nama_produk' => $row->nama_produk,
                'nama_umkm' => $row->nama_umkm,
                'jumlah_terjual' => (int) $row->jumlah_terjual,
                'omzet' => (int) $row->omzet,
                'kontribusi_persen' => $totalOmzet > 0
                    ? round(((int) $row->omzet / $totalOmzet) * 100, 1)
                    : 0,
            ])
            ->all();
    }

    private function recentOrders(string $mulai, string $akhir, ?int $umkmId): array
    {
        $orders = DB::query()
            ->fromSub($this->eligibleOrders($mulai, $akhir, $umkmId), 'pesanan')
            ->join('detail_pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->join('umkm', 'umkm.id_umkm', '=', 'pesanan.id_umkm')
            ->select(
                'pesanan.id_pesanan',
                'pesanan.id_checkout',
                'pesanan.id_umkm',
                'pesanan.nama_umkm_snapshot',
                'pesanan.status_pesanan',
                'pesanan.status_pembayaran',
                'pesanan.dibayar_pada',
                'pesanan.tanggal_pesan',
                'umkm.nama_usaha'
            )
            ->selectRaw('DATE(COALESCE(pesanan.dibayar_pada, pesanan.tanggal_pesan)) as tanggal')
            ->selectRaw('SUM(detail_pesanan.jumlah) as jumlah_produk')
            ->selectRaw('COUNT(detail_pesanan.id_detail) as jenis_produk')
            ->selectRaw('SUM(detail_pesanan.subtotal) as omzet_produk')
            ->groupBy(
                'pesanan.id_pesanan',
                'pesanan.id_checkout',
                'pesanan.id_umkm',
                'pesanan.nama_umkm_snapshot',
                'pesanan.status_pesanan',
                'pesanan.status_pembayaran',
                'pesanan.dibayar_pada',
                'pesanan.tanggal_pesan',
                'umkm.nama_usaha'
            )
            ->orderByDesc('pesanan.dibayar_pada')
            ->orderByDesc('pesanan.tanggal_pesan')
            ->limit(10)
            ->get();
        $itemsByOrder = DB::table('detail_pesanan')
            ->whereIn('id_pesanan', $orders->pluck('id_pesanan'))
            ->orderBy('id_detail')
            ->get(['id_pesanan', 'nama_produk', 'jumlah', 'harga_satuan', 'subtotal'])
            ->groupBy('id_pesanan');

        return $orders
            ->map(fn (object $row): array => [
                'id_pesanan' => $row->id_pesanan,
                'id_checkout' => $row->id_checkout,
                'tanggal' => $row->tanggal,
                'nama_umkm' => $row->nama_umkm_snapshot ?: $row->nama_usaha,
                'jumlah_produk' => (int) $row->jumlah_produk,
                'jenis_produk' => (int) $row->jenis_produk,
                'omzet_produk' => (int) $row->omzet_produk,
                'status_pesanan' => $row->status_pesanan,
                'status_pembayaran' => $row->status_pembayaran,
                'items' => $itemsByOrder->get($row->id_pesanan, collect())
                    ->map(fn (object $item): array => [
                        'nama_produk' => $item->nama_produk,
                        'jumlah' => (int) $item->jumlah,
                        'harga_satuan' => (int) $item->harga_satuan,
                        'subtotal' => (int) $item->subtotal,
                    ])
                    ->all(),
            ])
            ->all();
    }

    private function rankingQuery(string $mulai, string $akhir, string $search = '', ?int $umkmId = null): Builder
    {
        $orders = $this->eligibleOrders($mulai, $akhir, $umkmId);
        $query = DB::table('umkm')
            ->leftJoinSub($orders, 'pesanan', 'pesanan.id_umkm', '=', 'umkm.id_umkm')
            ->leftJoin('detail_pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->select('umkm.id_umkm', 'umkm.nama_usaha')
            ->selectRaw('COUNT(DISTINCT pesanan.id_pesanan) as jumlah_transaksi')
            ->selectRaw('COALESCE(SUM(detail_pesanan.subtotal), 0) as omzet')
            ->groupBy('umkm.id_umkm', 'umkm.nama_usaha')
            ->where('umkm.status_verifikasi', 'terverifikasi')
            ->when($umkmId !== null, fn (Builder $query) => $query->where('umkm.id_umkm', $umkmId))
            ->orderByDesc('omzet')
            ->orderBy('umkm.nama_usaha');

        if ($search !== '') {
            $query->where('umkm.nama_usaha', 'like', '%'.$search.'%');
        }

        return $query;
    }

    private function trend(string $mulai, string $akhir, string $granularity, ?int $umkmId = null): array
    {
        $daily = DB::query()
            ->fromSub($this->eligibleOrders($mulai, $akhir, $umkmId), 'pesanan')
            ->join('detail_pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->selectRaw('DATE(COALESCE(pesanan.dibayar_pada, pesanan.tanggal_pesan)) as tanggal')
            ->selectRaw('SUM(detail_pesanan.subtotal) as omzet')
            ->groupByRaw('DATE(COALESCE(pesanan.dibayar_pada, pesanan.tanggal_pesan))')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $cursor = Carbon::parse($mulai);
        $last = Carbon::parse($akhir);
        if ($granularity === 'week') {
            $cursor->startOfWeek();
            $last->startOfWeek();
        } elseif ($granularity === 'month') {
            $cursor->startOfMonth();
            $last->startOfMonth();
        } elseif ($granularity === 'year') {
            $cursor->startOfYear();
            $last->startOfYear();
        }

        $buckets = [];
        foreach ($daily as $date => $row) {
            $date = Carbon::parse($date);
            $bucketDate = match ($granularity) {
                'week' => $date->copy()->startOfWeek(),
                'month' => $date->copy()->startOfMonth(),
                'year' => $date->copy()->startOfYear(),
                default => $date,
            };
            $key = $bucketDate->format('Y-m-d');
            $buckets[$key] = ($buckets[$key] ?? 0) + (int) $row->omzet;
        }

        $points = [];
        while ($cursor->lte($last)) {
            $key = $cursor->format('Y-m-d');
            $points[] = [
                'label' => match ($granularity) {
                    'week' => 'Minggu '.$cursor->format('W/Y'),
                    'month' => $cursor->format('m/Y'),
                    'year' => $cursor->format('Y'),
                    default => $cursor->format('d/m'),
                },
                'omzet' => $buckets[$key] ?? 0,
            ];
            match ($granularity) {
                'week' => $cursor->addWeek(),
                'month' => $cursor->addMonth(),
                'year' => $cursor->addYear(),
                default => $cursor->addDay(),
            };
        }

        return $points;
    }
}
