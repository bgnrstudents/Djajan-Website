@extends('layouts.admin')

@section('title', 'Dashboard Omzet - Djajan')

@section('content')
@php($statistics = $analytics['statistics'])
<div class="mx-auto max-w-7xl space-y-6 pb-10">
    {{-- Page Header --}}
    <x-admin.page-hero
        badge="Pusat Monitoring Bisnis"
        title="Analitik Omzet UMKM"
        subtitle="Ringkasan kinerja penjualan produk UMKM yang sudah lunas dan selesai.">
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 3v18h18"></path>
                <path d="m19 9-5 5-4-4-3 3"></path>
                <path d="M15 9h4v4"></path>
            </svg>
        </x-slot:icon>
        <x-slot:right>
            <div class="admin-page-hero-pill inline-flex items-center gap-2.5 rounded-full px-3.5 py-2 text-xs font-medium" aria-label="Status pembaruan dashboard">
                <span id="live-indicator" class="h-2 w-2 rounded-full bg-amber-400"></span>
                <span id="live-status" aria-live="polite">Memuat pembaruan...</span>
            </div>
        </x-slot:right>
    </x-admin.page-hero>

    {{-- Top Bento KPI Row --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan omzet">
        <article class="stat-card stat-card-primary flex flex-col justify-between rounded-2xl bg-djajan-primary p-5 text-white">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/75">Omzet produk tercatat</p>
                    <p id="stat-omzet" class="mt-3 text-2xl font-extrabold tracking-tight tabular-nums sm:text-[26px]">Rp {{ number_format($statistics['total_omzet'], 0, ',', '.') }}</p>
                </div>
                <span class="stat-icon bg-white/15 text-white" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </span>
            </div>
            <p class="mt-4 border-t border-white/15 pt-3 text-xs text-white/80">Pesanan lunas &amp; selesai; tanpa ongkir</p>
        </article>

        <article class="stat-card flex flex-col justify-between rounded-2xl border border-gray-200/90 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Checkout berhasil</p>
                    <p id="stat-transaksi" class="mt-3 text-2xl font-extrabold tracking-tight text-djajan-neutral tabular-nums sm:text-[26px]">{{ number_format($statistics['total_transaksi'], 0, ',', '.') }}</p>
                </div>
                <span class="stat-icon bg-emerald-50 text-djajan-primary" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13"></path><path d="m3 6 1 1 2-2M3 12l1 1 2-2M3 18l1 1 2-2"></path></svg>
                </span>
            </div>
            <p class="mt-4 border-t border-gray-100 pt-3 text-xs text-gray-500">Checkout lintas-UMKM tidak dihitung ganda</p>
        </article>

        <article class="stat-card flex flex-col justify-between rounded-2xl border border-gray-200/90 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Produk terjual</p>
                    <p id="stat-produk-terjual" class="mt-3 text-2xl font-extrabold tracking-tight text-djajan-neutral tabular-nums sm:text-[26px]">{{ number_format($statistics['total_produk_terjual'], 0, ',', '.') }}</p>
                </div>
                <span class="stat-icon bg-emerald-50 text-djajan-primary" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 7h12l1 14H5L6 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path></svg>
                </span>
            </div>
            <p class="mt-4 border-t border-gray-100 pt-3 text-xs text-gray-500">Jumlah unit pada pesanan yang dihitung</p>
        </article>

        <article class="stat-card flex flex-col justify-between rounded-2xl border border-gray-200/90 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Rata-rata transaksi</p>
                    <p id="stat-rata-rata" class="mt-3 text-2xl font-extrabold tracking-tight text-djajan-neutral tabular-nums sm:text-[26px]">Rp {{ number_format($statistics['rata_rata_transaksi'], 0, ',', '.') }}</p>
                </div>
                <span class="stat-icon bg-emerald-50 text-djajan-primary" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="m19 9-5 5-4-4-3 3"></path><path d="M15 9h4v4"></path></svg>
                </span>
            </div>
            <p class="mt-4 border-t border-gray-100 pt-3 text-xs text-gray-500">Omzet produk dibagi checkout berhasil</p>
        </article>
    </section>

    {{-- Controls Toolbar: Filter Periode + Unduh Laporan PDF (matched equal height) --}}
    <section class="grid grid-cols-1 items-stretch gap-4 xl:grid-cols-12" aria-label="Kontrol periode dan laporan">
        <form method="get" action="{{ route('admin.dashboard') }}" class="flex h-full flex-col justify-between rounded-2xl border border-gray-200/90 bg-white p-5 xl:col-span-6" aria-label="Filter periode omzet">
            <div>
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-djajan-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 11h18"></path></svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-djajan-neutral">Filter periode analitik</h2>
                            <p class="text-xs text-gray-500">Atur rentang tanggal tampilan dashboard</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-1 items-end gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
                    <label class="block text-xs font-semibold text-gray-600">Tanggal mulai
                        <input type="date" name="mulai" value="{{ $mulai }}" required class="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/70 px-3 py-2 text-sm text-gray-800 focus:border-djajan-primary focus:bg-white focus:ring-djajan-primary">
                    </label>
                    <label class="block text-xs font-semibold text-gray-600">Tanggal akhir
                        <input type="date" name="akhir" value="{{ $akhir }}" required class="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/70 px-3 py-2 text-sm text-gray-800 focus:border-djajan-primary focus:bg-white focus:ring-djajan-primary">
                    </label>
                    <button class="inline-flex h-[42px] items-center justify-center gap-2 rounded-xl bg-djajan-primary px-4 text-sm font-semibold text-white shadow-xs transition hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-djajan-primary focus:ring-offset-2 sm:col-span-2 lg:col-span-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>
                        Terapkan
                    </button>
                </div>
            </div>
            <p class="mt-4 border-t border-gray-100 pt-3 text-xs text-gray-500">Acuan tanggal mengikuti waktu pembayaran; pesanan tanpa pembayaran memakai tanggal pesanan.</p>
        </form>

        <form id="omzet-report-form" method="get" action="{{ route('admin.omzet.report.pdf', [], false) }}" target="_blank" class="flex h-full flex-col justify-between rounded-2xl border border-gray-200/90 bg-white p-5 xl:col-span-6" aria-label="Cetak laporan omzet PDF">
            <input type="hidden" name="preview" value="1">
            <input type="hidden" name="report_key" value="{{ $reportKey }}">
            <div>
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-djajan-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6M8 13h8M8 17h8"></path></svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-djajan-neutral">Ekspor laporan PDF</h2>
                            <p class="text-xs text-gray-500">Unduh rekap transaksi resmi periode terpilih</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-1 items-end gap-3 sm:grid-cols-3">
                    <label class="block text-xs font-semibold text-gray-600">Periode laporan
                        <select id="report-period" name="periode" class="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/70 px-3 py-2 text-sm text-gray-800 focus:border-djajan-primary focus:bg-white focus:ring-djajan-primary">
                            <option value="harian">Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="tahunan">Tahunan</option>
                            <option value="rentang" selected>Rentang tanggal</option>
                        </select>
                    </label>
                    <label id="report-date-field" class="hidden text-xs font-semibold text-gray-600 sm:col-span-2">Tanggal acuan
                        <input type="date" name="tanggal" value="{{ $akhir }}" disabled class="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/70 px-3 py-2 text-sm text-gray-800 focus:border-djajan-primary focus:bg-white focus:ring-djajan-primary">
                    </label>
                    <div id="report-range-fields" class="grid grid-cols-1 gap-3 sm:col-span-2 sm:grid-cols-2">
                        <label class="block text-xs font-semibold text-gray-600">Mulai
                            <input type="date" name="mulai" value="{{ $mulai }}" required class="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/70 px-3 py-2 text-sm text-gray-800 focus:border-djajan-primary focus:bg-white focus:ring-djajan-primary">
                        </label>
                        <label class="block text-xs font-semibold text-gray-600">Sampai
                            <input type="date" name="akhir" value="{{ $akhir }}" required class="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50/70 px-3 py-2 text-sm text-gray-800 focus:border-djajan-primary focus:bg-white focus:ring-djajan-primary">
                        </label>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-3">
                <p class="text-xs text-gray-500">PDF dibuka di tab baru untuk pratinjau &amp; unduh.</p>
                <button type="submit" class="inline-flex h-9 items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50/80 px-4 text-xs font-bold text-djajan-primary transition hover:bg-emerald-100/80 focus:outline-none focus:ring-2 focus:ring-djajan-primary focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5M12 15V3"></path></svg>
                    Cetak Laporan PDF
                </button>
            </div>
        </form>
    </section>

    {{-- Revenue Breakdown: UMKM vs Platform Fee --}}
    <section class="rounded-2xl border border-gray-200/90 bg-white p-5" aria-label="Pendapatan platform dan bagian UMKM">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-djajan-neutral">Pendapatan platform &amp; bagian UMKM</h2>
                <p class="mt-0.5 text-xs text-gray-500">Biaya layanan platform (Flat Rp 1.500/checkout) dibayar pembeli dan tidak mengurangi omzet produk UMKM.</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">Periode terpilih</span>
        </div>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="revenue-subcard flex items-center justify-between gap-4 rounded-xl border border-gray-200/80 p-4">
                <div>
                    <div class="flex items-center gap-2">
                        <p class="text-xs font-semibold text-gray-500">Omzet produk UMKM</p>
                        <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-djajan-primary">100% Hak Mitra</span>
                    </div>
                    <p id="stat-bagian-umkm" class="mt-1.5 text-xl font-extrabold tracking-tight text-djajan-neutral tabular-nums">Rp {{ number_format($statistics['total_bagian_umkm'], 0, ',', '.') }}</p>
                </div>
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-djajan-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </span>
            </div>
            <div class="revenue-subcard flex items-center justify-between gap-4 rounded-xl border border-gray-200/80 p-4">
                <div>
                    <div class="flex items-center gap-2">
                        <p class="text-xs font-semibold text-gray-500">Pendapatan platform · biaya layanan flat Rp 1.500</p>
                    </div>
                    <p id="stat-komisi-platform" class="mt-1.5 text-xl font-extrabold tracking-tight text-djajan-neutral tabular-nums">Rp {{ number_format($statistics['total_komisi_platform'], 0, ',', '.') }}</p>
                </div>
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-djajan-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                </span>
            </div>
        </div>
    </section>

    {{-- Charts Section --}}
    <section class="grid grid-cols-1 items-stretch gap-4 xl:grid-cols-5">
        <article class="flex flex-col justify-between rounded-2xl border border-gray-200/90 bg-white p-5 xl:col-span-3">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-djajan-neutral">Tren omzet produk</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Subtotal produk dari pesanan lunas dan selesai</p>
                </div>
                <div class="flex rounded-xl bg-gray-100 p-1" role="group" aria-label="Kelompok waktu grafik">
                    @foreach(['day' => 'Hari', 'week' => 'Minggu', 'month' => 'Bulan', 'year' => 'Tahun'] as $value => $label)
                        <button type="button" data-granularity="{{ $value }}" class="granularity-button rounded-lg px-3 py-1.5 text-xs font-semibold text-gray-500 transition hover:text-djajan-primary">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            <div class="mt-5 min-h-64 flex-1" id="trend-chart-container">
                <svg id="trend-chart" class="h-64 w-full overflow-visible" role="img" aria-label="Grafik tren omzet"></svg>
            </div>
        </article>

        <article class="flex flex-col rounded-2xl border border-gray-200/90 bg-white p-5 xl:col-span-2">
            <div>
                <h2 class="text-base font-bold text-djajan-neutral">Perbandingan omzet UMKM</h2>
                <p class="mt-0.5 text-xs text-gray-500">10 usaha dengan omzet tertinggi</p>
            </div>
            <div id="comparison-chart" class="mt-5 flex-1 space-y-4"></div>
        </article>
    </section>

    {{-- Ranking Table --}}
    <section class="overflow-hidden rounded-2xl border border-gray-200/90 bg-white">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 px-5 py-4">
            <div>
                <h2 class="text-base font-bold text-djajan-neutral">Peringkat omzet UMKM</h2>
                <p class="mt-0.5 text-xs text-gray-500">Diurutkan dari omzet tertinggi; hanya UMKM terverifikasi yang ditampilkan.</p>
            </div>
            <label class="flex items-center gap-2.5 w-full sm:w-72 rounded-xl border border-gray-200 bg-gray-50/70 px-3 py-2 focus-within:border-djajan-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-djajan-primary/20 transition-colors">
                <span class="sr-only">Cari nama UMKM</span>
                <svg class="shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                <input id="umkm-search" type="search" value="{{ $analytics['filters']['search'] }}" placeholder="Cari nama UMKM..." class="admin-search-input w-full min-w-0 border-0 bg-transparent p-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-0">
            </label>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] text-sm">
                <thead class="bg-gray-50 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3.5">Peringkat</th>
                        <th class="px-5 py-3.5">Nama UMKM</th>
                        <th class="px-5 py-3.5 text-right">Pesanan UMKM</th>
                        <th class="px-5 py-3.5 text-right">Omzet produk</th>
                        <th class="px-5 py-3.5 text-right">Kontribusi</th>
                        <th class="px-5 py-3.5 text-right" title="Dibandingkan dengan rentang sebelumnya yang durasinya sama. Tanda — berarti belum ada omzet pembanding.">Perubahan omzet</th>
                    </tr>
                </thead>
                <tbody id="ranking-body" class="divide-y divide-gray-100">
                    @forelse($analytics['ranking'] as $index => $row)
                        @php($rank = ($analytics['pagination']['current_page'] - 1) * $analytics['pagination']['per_page'] + $index + 1)
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-5 py-3.5 font-semibold text-gray-500">
                                <span class="ranking-medal {{ $rank <= 3 ? 'is-top' : '' }}">{{ $rank }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-djajan-neutral">{{ $row['nama_usaha'] }}</td>
                            <td class="px-5 py-3.5 text-right text-gray-600 tabular-nums">{{ number_format($row['jumlah_transaksi'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-djajan-neutral tabular-nums">Rp {{ number_format($row['omzet'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-right text-gray-600 tabular-nums">{{ number_format($row['kontribusi_persen'], 1, ',', '.') }}%</td>
                            <td class="px-5 py-3.5 text-right tabular-nums">
                                @if($row['perkembangan'] === null)
                                    <span class="text-gray-400">—</span>
                                @elseif($row['perkembangan'] >= 0)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">+{{ number_format($row['perkembangan'], 1, ',', '.') }}%</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-600">{{ number_format($row['perkembangan'], 1, ',', '.') }}%</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">UMKM tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 px-5 py-3.5">
            <p id="ranking-count" class="text-xs font-medium text-gray-500">{{ number_format($analytics['pagination']['total'], 0, ',', '.') }} UMKM</p>
            <div class="flex items-center gap-2">
                <button id="ranking-prev" type="button" class="rounded-xl border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40">Sebelumnya</button>
                <span id="ranking-page" class="px-1 text-xs font-medium text-gray-500">Halaman {{ $analytics['pagination']['current_page'] }} dari {{ max(1, (int) ceil($analytics['pagination']['total'] / $analytics['pagination']['per_page'])) }}</span>
                <button id="ranking-next" type="button" class="rounded-xl border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40">Berikutnya</button>
            </div>
        </div>
    </section>

    {{-- Top Products & Recent Orders --}}
    <section class="grid grid-cols-1 items-stretch gap-4 xl:grid-cols-2" aria-label="Rincian penjualan">
        <article class="flex flex-col overflow-hidden rounded-2xl border border-gray-200/90 bg-white">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-base font-bold text-djajan-neutral">Produk terlaris</h2>
                <p class="mt-0.5 text-xs text-gray-500">Diurutkan menurut jumlah unit pada pesanan lunas dan selesai.</p>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full min-w-[520px] text-sm">
                    <thead class="bg-gray-50 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3.5">Produk / UMKM</th>
                            <th class="px-5 py-3.5 text-right">Unit</th>
                            <th class="px-5 py-3.5 text-right">Nilai produk</th>
                            <th class="px-5 py-3.5 text-right">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody id="top-products-body" class="divide-y divide-gray-100">
                        @forelse($analytics['top_products'] as $product)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-djajan-neutral">{{ $product['nama_produk'] }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $product['nama_umkm'] }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-right text-gray-600 tabular-nums">{{ number_format($product['jumlah_terjual'], 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right font-semibold text-djajan-neutral tabular-nums">Rp {{ number_format($product['omzet'], 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right text-gray-600 tabular-nums">{{ number_format($product['kontribusi_persen'], 1, ',', '.') }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Belum ada produk terjual pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="flex flex-col overflow-hidden rounded-2xl border border-gray-200/90 bg-white">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-base font-bold text-djajan-neutral">Pesanan UMKM terbaru</h2>
                <p class="mt-0.5 text-xs text-gray-500">10 pesanan per UMKM terbaru yang masuk dalam omzet periode ini.</p>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full min-w-[650px] text-sm">
                    <thead class="bg-gray-50 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3.5">Tanggal / ID / produk</th>
                            <th class="px-5 py-3.5">UMKM</th>
                            <th class="px-5 py-3.5 text-right">Unit</th>
                            <th class="px-5 py-3.5 text-right">Omzet produk</th>
                            <th class="px-5 py-3.5 text-right">Biaya layanan</th>
                        </tr>
                    </thead>
                    <tbody id="recent-orders-body" class="divide-y divide-gray-100">
                        @forelse($analytics['recent_orders'] as $order)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-djajan-neutral">{{ $order['tanggal'] }}</p>
                                    <span class="mt-1 inline-block rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[11px] font-medium text-gray-600">{{ $order['id_pesanan'] }}</span>
                                    <p class="mt-1 max-w-xs text-xs text-gray-500">{{ collect($order['items'])->map(fn ($item) => $item['jumlah'].'x '.$item['nama_produk'])->implode(' • ') }}</p>
                                </td>
                                <td class="px-5 py-3.5 font-medium text-gray-700">{{ $order['nama_umkm'] }}</td>
                                <td class="px-5 py-3.5 text-right text-gray-600 tabular-nums">{{ number_format($order['jumlah_produk'], 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right font-semibold text-djajan-neutral tabular-nums">Rp {{ number_format($order['omzet_produk'], 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right text-gray-600 tabular-nums">Rp {{ number_format($order['komisi_platform'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">Belum ada transaksi pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    {{-- Operational Summary & Pending Submissions --}}
    <section class="grid grid-cols-1 items-stretch gap-4 lg:grid-cols-2">
        <article class="flex flex-col justify-between rounded-2xl border border-gray-200/90 bg-white p-5">
            <div>
                <h2 class="text-base font-bold text-djajan-neutral">Ringkasan operasional</h2>
                <p class="mt-0.5 text-xs text-gray-500">Kondisi ekosistem UMKM &amp; katalog saat ini</p>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="revenue-subcard rounded-xl border border-gray-200/80 p-4">
                    <p class="text-xs font-semibold text-gray-500">UMKM aktif</p>
                    <p class="mt-1.5 text-2xl font-extrabold text-djajan-neutral tabular-nums">{{ number_format($totalUmkmAktif, 0, ',', '.') }}</p>
                </div>
                <div class="revenue-subcard rounded-xl border border-gray-200/80 p-4">
                    <p class="text-xs font-semibold text-gray-500">Produk terdaftar</p>
                    <p class="mt-1.5 text-2xl font-extrabold text-djajan-neutral tabular-nums">{{ number_format($totalProduk, 0, ',', '.') }}</p>
                </div>
                <div class="revenue-subcard rounded-xl border border-gray-200/80 p-4">
                    <p class="text-xs font-semibold text-gray-500">Total UMKM</p>
                    <p class="mt-1.5 text-2xl font-extrabold text-djajan-neutral tabular-nums">{{ number_format($totalPengguna, 0, ',', '.') }}</p>
                </div>
                <div class="revenue-subcard rounded-xl border border-gray-200/80 p-4">
                    <p class="text-xs font-semibold text-gray-500">Menunggu verifikasi</p>
                    <p class="mt-1.5 text-2xl font-extrabold text-djajan-primary tabular-nums">{{ number_format($totalPengajuanBaru, 0, ',', '.') }}</p>
                </div>
            </div>
        </article>

        <article class="flex flex-col overflow-hidden rounded-2xl border border-gray-200/90 bg-white">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-djajan-neutral">Pengajuan terbaru</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Pendaftaran UMKM yang perlu ditinjau</p>
                </div>
                <a href="{{ route('admin.umkm.index', ['status' => 'pending']) }}" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-djajan-primary transition hover:bg-emerald-100">Lihat semua</a>
            </div>
            <div class="flex-1 divide-y divide-gray-100">
                @forelse($pengajuanTerbaru as $umkm)
                    <a href="{{ route('admin.umkm.show', $umkm->id_umkm) }}" class="flex items-center justify-between gap-3 px-5 py-3.5 transition hover:bg-gray-50/80">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-xs font-bold text-djajan-primary">{{ mb_strtoupper(mb_substr($umkm->nama_usaha, 0, 1)) }}</span>
                            <span class="truncate text-sm font-semibold text-gray-700">{{ $umkm->nama_usaha }}</span>
                        </div>
                        <span class="shrink-0 rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-semibold text-amber-700">{{ optional($umkm->created_at)->format('d/m/Y') }}</span>
                    </a>
                @empty
                    <div class="flex h-full items-center justify-center px-5 py-10 text-center text-sm text-gray-500">Tidak ada pengajuan baru.</div>
                @endforelse
            </div>
        </article>
    </section>
</div>

<script>
    (() => {
        const form = document.getElementById('omzet-report-form');
        const period = document.getElementById('report-period');
        const dateField = document.getElementById('report-date-field');
        const rangeFields = document.getElementById('report-range-fields');
        const reportKey = form.querySelector('input[name="report_key"]');
        const dateInput = dateField.querySelector('input');
        const rangeInputs = rangeFields.querySelectorAll('input');

        function updateReportPeriodFields() {
            const isRange = period.value === 'rentang';
            dateField.classList.toggle('hidden', isRange);
            dateInput.disabled = isRange;
            dateInput.required = !isRange;
            rangeFields.classList.toggle('hidden', !isRange);
            rangeInputs.forEach(input => {
                input.disabled = !isRange;
                input.required = isRange;
            });
        }

        period.addEventListener('change', updateReportPeriodFields);
        form.addEventListener('submit', () => {
            if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                reportKey.value = window.crypto.randomUUID();
            }
        });
        updateReportPeriodFields();
    })();

    (() => {
        const dataUrl = @json(route('admin.omzet.data'));
        const initialData = @json($analytics);
        const state = {
            mulai: @json($mulai),
            akhir: @json($akhir),
            granularity: @json($analytics['filters']['granularity']),
            search: @json($analytics['filters']['search']),
            page: @json($analytics['pagination']['current_page']),
            data: initialData,
            timer: null,
            requestController: null
        };
        const currency = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
        const number = new Intl.NumberFormat('id-ID');
        const svgNs = 'http://www.w3.org/2000/svg';

        function svgElement(tag, attributes = {}, text = null) {
            const element = document.createElementNS(svgNs, tag);
            Object.entries(attributes).forEach(([key, value]) => element.setAttribute(key, value));
            if (text !== null) element.textContent = text;
            return element;
        }

        function drawTrend(points) {
            const svg = document.getElementById('trend-chart');
            svg.replaceChildren();
            const width = Math.max(svg.clientWidth, 320);
            const height = 256;
            const padding = { top: 12, right: 12, bottom: 34, left: 62 };
            const chartWidth = width - padding.left - padding.right;
            const chartHeight = height - padding.top - padding.bottom;
            const values = points.map(point => point.omzet);
            const max = Math.max(...values, 0);
            const scaleMax = max > 0 ? max : 1;
            const y = value => padding.top + chartHeight - (value / scaleMax) * chartHeight;
            const baseline = padding.top + chartHeight;

            for (let index = 0; index <= 4; index++) {
                const value = scaleMax * index / 4;
                const lineY = y(value);
                svg.appendChild(svgElement('line', { x1: padding.left, y1: lineY, x2: width - padding.right, y2: lineY, stroke: 'var(--chart-grid)', 'stroke-dasharray': '3 4' }));
                svg.appendChild(svgElement('text', { x: padding.left - 8, y: lineY + 4, 'text-anchor': 'end', fill: 'var(--chart-label)', 'font-size': '10' }, compactMoney(value)));
            }

            if (points.length === 0) {
                svg.appendChild(svgElement('text', { x: width / 2, y: height / 2, 'text-anchor': 'middle', fill: 'var(--chart-label)', 'font-size': '13' }, 'Belum ada transaksi pada periode ini'));
                return;
            }

            const coordinates = points.map((point, index) => ({
                x: points.length === 1 ? padding.left + chartWidth / 2 : padding.left + index * chartWidth / (points.length - 1),
                y: y(point.omzet),
                point
            }));
            const defs = svgElement('defs');
            const gradient = svgElement('linearGradient', { id: 'trend-area-gradient', x1: '0', x2: '0', y1: '0', y2: '1' });
            gradient.append(
                svgElement('stop', { offset: '0%', 'stop-color': 'var(--chart-green)', 'stop-opacity': '.3' }),
                svgElement('stop', { offset: '72%', 'stop-color': 'var(--chart-green)', 'stop-opacity': '.08' }),
                svgElement('stop', { offset: '100%', 'stop-color': 'var(--chart-green)', 'stop-opacity': '0' })
            );
            defs.appendChild(gradient);
            svg.appendChild(defs);
            const path = coordinates.map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`).join(' ');
            const areaPath = `${path} L ${coordinates[coordinates.length - 1].x} ${baseline} L ${coordinates[0].x} ${baseline} Z`;
            svg.appendChild(svgElement('path', { d: areaPath, class: 'trend-depth', transform: 'translate(0 5)' }));
            svg.appendChild(svgElement('path', { d: areaPath, class: 'trend-area' }));
            svg.appendChild(svgElement('path', { d: path, class: 'trend-line' }));
            coordinates.forEach(({ x, y: pointY, point }) => {
                const marker = svgElement('circle', {
                    cx: x,
                    cy: pointY,
                    r: '4',
                    fill: 'var(--admin-surface)',
                    stroke: 'var(--chart-green)',
                    'stroke-width': '2',
                    class: 'trend-point',
                    tabindex: '0',
                    'aria-label': `${point.label}: ${currency.format(point.omzet)}`
                });
                marker.appendChild(svgElement('title', {}, `${point.label}: ${currency.format(point.omzet)}`));
                svg.appendChild(marker);
            });
            const labelStep = Math.max(1, Math.ceil(points.length / 6));
            coordinates.forEach(({ x, point }, index) => {
                if (index % labelStep === 0 || index === coordinates.length - 1) {
                    svg.appendChild(svgElement('text', { x, y: height - 8, 'text-anchor': 'middle', fill: 'var(--chart-label)', 'font-size': '10' }, point.label));
                }
            });
        }

        function compactMoney(value) {
            if (value >= 1000000000) return `Rp ${(value / 1000000000).toFixed(1)} M`;
            if (value >= 1000000) return `Rp ${(value / 1000000).toFixed(1)} jt`;
            if (value >= 1000) return `Rp ${(value / 1000).toFixed(0)} rb`;
            return `Rp ${number.format(value)}`;
        }

        function renderComparison(rows) {
            const container = document.getElementById('comparison-chart');
            container.replaceChildren();
            const max = Math.max(...rows.map(row => row.omzet), 0);
            const totalOmzet = state.data.statistics.total_omzet;
            rows.forEach((row, index) => {
                const item = document.createElement('div');
                item.style.animationDelay = `${Math.min(index * 45, 270)}ms`;
                const heading = document.createElement('div');
                heading.className = 'mb-1 flex items-center justify-between gap-3 text-xs';
                const identity = document.createElement('span');
                identity.className = 'flex min-w-0 items-center gap-2';
                const rank = document.createElement('span');
                rank.className = `ranking-medal shrink-0 text-[11px] font-bold ${index < 3 ? 'is-top' : ''}`;
                rank.textContent = String(index + 1).padStart(2, '0');
                const name = document.createElement('span');
                name.className = 'truncate font-semibold text-gray-700';
                name.textContent = row.nama_usaha;
                const amount = document.createElement('span');
                amount.className = 'shrink-0 font-bold text-djajan-neutral tabular-nums';
                amount.textContent = currency.format(row.omzet);
                identity.append(rank, name);
                heading.append(identity, amount);
                const track = document.createElement('div');
                track.className = 'comparison-track mt-2 overflow-hidden rounded-full';
                const bar = document.createElement('div');
                bar.className = 'comparison-bar h-full rounded-full';
                bar.style.width = `${max > 0 ? Math.max(2, row.omzet / max * 100) : 0}%`;
                track.appendChild(bar);
                const contribution = document.createElement('p');
                contribution.className = 'mt-1 text-right text-[11px] text-gray-500 tabular-nums';
                contribution.textContent = `${totalOmzet > 0 ? (row.omzet / totalOmzet * 100).toLocaleString('id-ID', { maximumFractionDigits: 1 }) : '0'}% dari omzet periode`;
                item.append(heading, track, contribution);
                container.appendChild(item);
            });
            if (!rows.length) container.textContent = 'Belum ada transaksi pada periode ini.';
        }

        function renderRanking(rows, pagination) {
            const body = document.getElementById('ranking-body');
            body.replaceChildren();
            if (!rows.length) {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 6;
                cell.className = 'px-5 py-10 text-center text-gray-500';
                cell.textContent = 'UMKM tidak ditemukan.';
                row.appendChild(cell);
                body.appendChild(row);
            } else {
                rows.forEach((entry, index) => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50/80';
                    const rankValue = (pagination.current_page - 1) * pagination.per_page + index + 1;
                    const values = [
                        number.format(rankValue),
                        entry.nama_usaha,
                        number.format(entry.jumlah_transaksi),
                        currency.format(entry.omzet),
                        `${number.format(entry.kontribusi_persen)}%`,
                        entry.perkembangan === null ? '—' : `${entry.perkembangan > 0 ? '+' : ''}${entry.perkembangan.toFixed(1)}%`
                    ];
                    values.forEach((value, cellIndex) => {
                        const cell = document.createElement('td');
                        cell.className = `px-5 py-3.5 ${cellIndex === 0 ? 'font-semibold text-gray-500' : ''} ${cellIndex === 1 ? 'font-semibold text-djajan-neutral' : ''} ${cellIndex > 1 ? 'text-right text-gray-600 tabular-nums' : ''}`;
                        if (cellIndex === 3) cell.classList.add('font-bold', 'text-djajan-neutral');
                        if (cellIndex === 0) {
                            const badge = document.createElement('span');
                            badge.className = `ranking-medal ${rankValue <= 3 ? 'is-top' : ''}`;
                            badge.textContent = value;
                            cell.appendChild(badge);
                        } else if (cellIndex === 5) {
                            const pill = document.createElement('span');
                            if (entry.perkembangan === null) {
                                pill.className = 'text-gray-400';
                            } else if (entry.perkembangan >= 0) {
                                pill.className = 'inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700';
                            } else {
                                pill.className = 'inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-600';
                            }
                            pill.textContent = value;
                            cell.appendChild(pill);
                        } else {
                            cell.textContent = value;
                        }
                        row.appendChild(cell);
                    });
                    body.appendChild(row);
                });
            }

            const lastPage = Math.max(1, Math.ceil(pagination.total / pagination.per_page));
            document.getElementById('ranking-count').textContent = `${number.format(pagination.total)} UMKM`;
            document.getElementById('ranking-page').textContent = `Halaman ${pagination.current_page} dari ${lastPage}`;
            document.getElementById('ranking-prev').disabled = pagination.current_page <= 1;
            document.getElementById('ranking-next').disabled = pagination.current_page >= lastPage;
        }

        function renderTopProducts(products) {
            const body = document.getElementById('top-products-body');
            body.replaceChildren();
            if (!products.length) {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 4;
                cell.className = 'px-5 py-10 text-center text-gray-500';
                cell.textContent = 'Belum ada produk terjual pada periode ini.';
                row.appendChild(cell);
                body.appendChild(row);
                return;
            }

            products.forEach(product => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50/80';
                const identityCell = document.createElement('td');
                identityCell.className = 'px-5 py-3.5';
                const name = document.createElement('p');
                name.className = 'font-semibold text-djajan-neutral';
                name.textContent = product.nama_produk;
                const merchant = document.createElement('p');
                merchant.className = 'mt-0.5 text-xs text-gray-500';
                merchant.textContent = product.nama_umkm;
                identityCell.append(name, merchant);
                row.append(
                    identityCell,
                    createTextCell(number.format(product.jumlah_terjual), 'text-right tabular-nums'),
                    createTextCell(currency.format(product.omzet), 'text-right font-semibold text-djajan-neutral tabular-nums'),
                    createTextCell(`${number.format(product.kontribusi_persen)}%`, 'text-right tabular-nums')
                );
                body.appendChild(row);
            });
        }

        function renderRecentOrders(orders) {
            const body = document.getElementById('recent-orders-body');
            body.replaceChildren();
            if (!orders.length) {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 5;
                cell.className = 'px-5 py-10 text-center text-gray-500';
                cell.textContent = 'Belum ada transaksi pada periode ini.';
                row.appendChild(cell);
                body.appendChild(row);
                return;
            }

            orders.forEach(order => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50/80';
                const idCell = document.createElement('td');
                idCell.className = 'px-5 py-3.5';
                const date = document.createElement('p');
                date.className = 'font-semibold text-djajan-neutral';
                date.textContent = order.tanggal;
                const id = document.createElement('span');
                id.className = 'mt-1 inline-block rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[11px] font-medium text-gray-600';
                id.textContent = order.id_pesanan;
                const items = document.createElement('p');
                items.className = 'mt-1 max-w-xs text-xs text-gray-500';
                items.textContent = order.items.map(item => `${number.format(item.jumlah)}x ${item.nama_produk}`).join(' • ');
                idCell.append(date, id, items);

                row.append(
                    idCell,
                    createTextCell(order.nama_umkm, 'font-medium text-gray-700'),
                    createTextCell(number.format(order.jumlah_produk), 'text-right tabular-nums'),
                    createTextCell(currency.format(order.omzet_produk), 'text-right font-semibold text-djajan-neutral tabular-nums'),
                    createTextCell(currency.format(order.komisi_platform), 'text-right tabular-nums')
                );
                body.appendChild(row);
            });
        }

        function createTextCell(value, alignment = '') {
            const cell = document.createElement('td');
            cell.className = `px-5 py-3.5 text-gray-600 ${alignment}`;
            cell.textContent = value;
            return cell;
        }

        function render(data) {
            state.data = data;
            document.getElementById('stat-omzet').textContent = currency.format(data.statistics.total_omzet);
            document.getElementById('stat-bagian-umkm').textContent = currency.format(data.statistics.total_bagian_umkm);
            document.getElementById('stat-komisi-platform').textContent = currency.format(data.statistics.total_komisi_platform);
            document.getElementById('stat-transaksi').textContent = number.format(data.statistics.total_transaksi);
            document.getElementById('stat-produk-terjual').textContent = number.format(data.statistics.total_produk_terjual);
            document.getElementById('stat-rata-rata').textContent = currency.format(data.statistics.rata_rata_transaksi);
            drawTrend(data.trend);
            renderComparison(data.comparison);
            renderRanking(data.ranking, data.pagination);
            renderTopProducts(data.top_products);
            renderRecentOrders(data.recent_orders);
            document.querySelectorAll('.granularity-button').forEach(button => {
                const active = button.dataset.granularity === state.granularity;
                button.setAttribute('aria-pressed', String(active));
                button.className = `granularity-button rounded-lg px-3 py-1.5 text-xs font-semibold text-gray-500 transition hover:text-djajan-primary ${active ? 'bg-white text-djajan-primary shadow-xs' : ''}`;
            });
        }

        async function refresh() {
            if (document.hidden) return;
            state.requestController?.abort();
            state.requestController = new AbortController();
            const params = new URLSearchParams({
                mulai: state.mulai,
                akhir: state.akhir,
                granularity: state.granularity,
                search: state.search,
                page: String(state.page)
            });
            try {
                const response = await fetch(`${dataUrl}?${params}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    signal: state.requestController.signal
                });
                if (response.status === 401 || response.status === 419 || response.redirected) {
                    window.location.assign('{{ route('login') }}');
                    return;
                }
                if (!response.ok) throw new Error(`Permintaan data gagal (${response.status})`);
                render(await response.json());
                document.getElementById('live-indicator').className = 'h-2 w-2 rounded-full bg-green-500';
                document.getElementById('live-status').textContent = `Diperbarui ${new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).format(new Date())}`;
            } catch (error) {
                if (error.name === 'AbortError') return;
                document.getElementById('live-indicator').className = 'h-2 w-2 rounded-full bg-rose-500';
                document.getElementById('live-status').textContent = 'Pembaruan tertunda';
                console.error('Gagal memperbarui dashboard omzet:', error);
            }
        }

        document.querySelectorAll('.granularity-button').forEach(button => {
            button.addEventListener('click', () => {
                state.granularity = button.dataset.granularity;
                refresh();
            });
        });
        document.getElementById('umkm-search').addEventListener('input', event => {
            state.search = event.target.value.trim();
            state.page = 1;
            clearTimeout(state.timer);
            state.timer = setTimeout(refresh, 300);
        });
        document.getElementById('ranking-prev').addEventListener('click', () => {
            if (state.page > 1) {
                state.page--;
                refresh();
            }
        });
        document.getElementById('ranking-next').addEventListener('click', () => {
            const lastPage = Math.max(1, Math.ceil(state.data.pagination.total / state.data.pagination.per_page));
            if (state.page < lastPage) {
                state.page++;
                refresh();
            }
        });
        window.addEventListener('resize', () => drawTrend(state.data.trend));
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) refresh();
        });

        render(initialData);
        refresh();
        window.setInterval(refresh, 30000);
    })();
</script>
@endsection
