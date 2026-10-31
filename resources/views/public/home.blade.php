@extends('layouts.public')

@section('title', 'Home - Djajan')

@section('content')

<style>
    /* ===== Carousel ===== */
    .slide-bg {
        transition: opacity 1.2s ease-in-out, transform 9s ease-out;
        transform: scale(1.06);
    }

    .slide-bg.active {
        opacity: 1 !important;
        transform: scale(1);
    }

    /* ===== Utilities halaman ===== */
    .img-zoom {
        transition: transform .6s cubic-bezier(.25, .46, .45, .94);
    }

    .group:hover .img-zoom {
        transform: scale(1.04);
    }

    /* ===== Mobile Optimizations ===== */
    @media (max-width: 1023px) {
        /* Hero: Keep original hero sizing */
        #home {
            min-height: 640px;
            height: auto;
        }

        /* Section: Consistent spacing */
        section {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        /* Cards: Ensure consistent layout in "Mengapa Djajan" */
        .grid.grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        /* Featured product stack */
        .md\\:col-span-2,
        .lg\\:col-span-2 {
            grid-column: auto;
        }

        /* Card overlap: reset absolute positioning for mobile */
        .lg\\:absolute,
        .lg\\:-bottom-10,
        .lg\\:right-10,
        .lg\\:w-\[380px\] {
            position: static;
            width: 100%;
            transform: none;
            margin-top: 1.5rem;
        }

        /* CTA: keep original scaling */
        #cta .font-display {
            font-size: 1.875rem;
            line-height: 1.25;
        }
    }

    /* ===== Modal mobile ===== */
    #detail-modal .dm-card {
        margin: 1rem;
        max-height: calc(100vh - 2rem);
    }

    #detail-modal .dm-body {
        padding: 1rem;
        font-size: 0.875rem;
    }

    #detail-modal .dm-title {
        font-size: 1.5rem;
    }

    @media (min-width: 768px) {
        #detail-modal .dm-body {
            padding: 2rem;
        }

        #detail-modal .dm-title {
            font-size: 2rem;
        }
    }
</style>


{{-- ==================== 01 · HERO CAROUSEL ==================== --}}
<section id="home" class="relative h-screen min-h-[640px] w-full flex items-center overflow-hidden bg-gray-900">

    {{-- Slide 1: Sawah Indonesia --}}
    <div id="slide-bg-0" class="slide-bg active absolute inset-0 bg-cover bg-center opacity-100"
        style="background-image:url('https://images.unsplash.com/photo-1587595431973-160d0d94add1?auto=format&fit=crop&w=1920&q=80')"></div>

    {{-- Slide 2: Pemandangan desa Jawa --}}
    <div id="slide-bg-1" class="slide-bg absolute inset-0 bg-cover bg-center opacity-0"
        style="background-image:url('https://images.unsplash.com/photo-1518495973542-4542c06a5843?auto=format&fit=crop&w=1920&q=80')"></div>

    {{-- Slide 3: Pertanian lokal --}}
    <div id="slide-bg-2" class="slide-bg absolute inset-0 bg-cover bg-center opacity-0"
        style="background-image:url('https://images.unsplash.com/photo-1592982537447-6f2a6a0c7c18?auto=format&fit=crop&w=1920&q=80')"></div>

    {{-- Slide 4: Komunitas desa --}}
    <div id="slide-bg-3" class="slide-bg absolute inset-0 bg-cover bg-center opacity-0"
        style="background-image:url('https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=1920&q=80')"></div>

    {{-- Slide 5: Produk pangan lokal --}}
    <div id="slide-bg-4" class="slide-bg absolute inset-0 bg-cover bg-center opacity-0"
        style="background-image:url('https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?auto=format&fit=crop&w=1920&q=80')"></div>

    <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/60 to-black/10"></div>
    <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/50 to-transparent"></div>

    <div class="relative z-10 mx-auto flex h-full w-full max-w-7xl flex-col justify-center px-6 pb-20 pt-24 lg:px-8">
        <div class="max-w-2xl">

            <h1 class="font-hero text-4xl font-semibold leading-[1.15] tracking-[-0.045em] text-white md:text-6xl lg:text-[4.5rem]">
                Kenali dan dukung<br>
                <span class="text-[#4CAF50]">UMKM lokal</span><br>
                bersama Djajan.
            </h1>

            <p class="mt-7 max-w-lg text-base leading-relaxed text-white/75 md:text-lg">
                Temukan berbagai usaha dan produk lokal dari Desa Kreyongan Atas dalam satu platform. Kenali produknya, dukung usahanya, dan ikut menggerakkan ekonomi lokal.
            </p>
        </div>
    </div>
</section>


{{-- ==================== 02 · MENGAPA DJAJAN ==================== --}}
<section id="tentang" class="px-6 lg:px-8 py-20 lg:py-28 bg-white">
    <div class="mx-auto max-w-7xl">

        <div class="flex flex-col lg:flex-row gap-10 lg:gap-14 items-stretch">

            {{-- KOLOM KIRI: Teks + CTA --}}
            <div class="lg:w-[42%] flex-shrink-0 flex flex-col justify-center">

                {{-- Label --}}
                <div class="inline-flex items-center gap-2.5 mb-5 w-fit">
                    <div class="w-7 h-7 rounded-full bg-[#0F623F] flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#0F623F]">Mengapa Djajan?</span>
                </div>

                {{-- Headline --}}
                <h2 class="font-display text-3xl md:text-4xl font-bold leading-[1.1] text-[#111827] tracking-tight mb-5">
                    Platform yang Menghubungkan
                    <span class="text-[#0F623F]">Desa</span>
                    dengan
                    <span class="text-[#0F623F]">Dunia.</span>
                </h2>

                {{-- Subtitle --}}
                <p class="text-sm md:text-base text-[#6B7280] leading-relaxed mb-8 max-w-sm">
                    Tiga alasan mengapa Djajan menjadi pilihan tepat untuk mendukung UMKM lokal Desa Kreyongan Atas.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-wrap items-center gap-4">
                    <a href="#umkm" class="group inline-flex items-center gap-2.5 rounded-full bg-[#0F623F] px-6 py-3 text-sm font-semibold text-white hover:bg-[#0A4A2F] transition-all duration-300 shadow-lg shadow-[#0F623F]/20">
                        Jelajahi UMKM
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="#tentang" class="inline-flex items-center gap-2 text-sm font-semibold text-[#111827] hover:text-[#0F623F] transition-colors">
                        Pelajari Lebih Lanjut
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

            </div>

            {{-- KOLOM KANAN: 3 Kartu Foto --}}
            <div class="lg:flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- Card 1 --}}
                <div class="group relative min-h-[280px] sm:min-h-[380px] overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=600&q=80"
                        alt="Kualitas Terjamin"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                    <div class="absolute inset-0 p-5 flex flex-col justify-end">
                        <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center mb-3 shadow-lg">
                            <svg class="w-4 h-4 text-[#0F623F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>

                        <h3 class="font-display text-base font-bold text-white mb-1.5">
                            Kualitas Terjamin
                        </h3>
                        <p class="text-white/75 text-xs leading-relaxed">
                            Produk lokal dengan standar kualitas yang diawasi langsung oleh komunitas desa.
                        </p>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="group relative min-h-[280px] sm:min-h-[380px] overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&q=80"
                        alt="Harga Transparan"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                    <div class="absolute inset-0 p-5 flex flex-col justify-end">
                        <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center mb-3 shadow-lg">
                            <svg class="w-4 h-4 text-[#0F623F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <h3 class="font-display text-base font-bold text-white mb-1.5">
                            Harga Transparan
                        </h3>
                        <p class="text-white/75 text-xs leading-relaxed">
                            Tanpa biaya tersembunyi. Keuntungan kembali langsung kepada masyarakat desa.
                        </p>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="group relative min-h-[280px] sm:min-h-[380px] overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1593113598332-cd288d649433?w=600&q=80"
                        alt="Ekonomi Lokal"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                    <div class="absolute inset-0 p-5 flex flex-col justify-end">
                        <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center mb-3 shadow-lg">
                            <svg class="w-4 h-4 text-[#0F623F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>

                        <h3 class="font-display text-base font-bold text-white mb-1.5">
                            Ekonomi Lokal
                        </h3>
                        <p class="text-white/75 text-xs leading-relaxed">
                            Setiap pembelian memberdayakan pengrajin dan petani lokal Desa Kreyongan Atas.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>


{{-- ==================== 03 · TENTANG DJAJAN (OVERLAP CARD) ==================== --}}
<section class="relative px-6 lg:px-8 py-20 lg:py-28 pb-20 lg:pb-40 bg-[#FAFAF9] overflow-hidden">

    <div class="mx-auto max-w-7xl">

        <div class="relative">

            {{-- FOTO --}}
            <div class="relative w-full rounded-2xl overflow-hidden shadow-xl" style="aspect-ratio: 21/9;">
                <img src="https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=1600&q=85"
                    alt="Pemandangan Desa Kreyongan Atas"
                    class="w-full h-full object-cover">

                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>

                <div class="absolute top-5 left-5 flex items-center gap-2 bg-white/90 backdrop-blur-sm rounded-full px-3.5 py-1.5 shadow-md">
                    <svg class="w-3.5 h-3.5 text-[#0F623F]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                    </svg>
                    <span class="text-[11px] font-semibold text-[#111827]">Kreyongan Atas, Jawa Barat</span>
                </div>
            </div>

            {{-- KARTU OVERLAP --}}
            <div class="relative lg:absolute lg:-bottom-10 lg:right-10 lg:w-[380px] bg-white rounded-2xl shadow-xl p-7 lg:p-8 border border-gray-100">

                <div class="inline-flex items-center gap-2 mb-5">
                    <div class="w-5 h-5 rounded-full bg-[#0F623F] flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#0F623F]">Tentang Djajan</span>
                </div>

                <h2 class="font-display text-2xl lg:text-3xl font-bold leading-[1.1] text-[#111827] tracking-tight mb-4">
                    Dari Desa,<br>
                    <span class="text-[#0F623F]">Untuk Semua.</span>
                </h2>

                <p class="text-sm text-[#6B7280] leading-relaxed mb-5">
                    Djajan lahir dari kebutuhan nyata — mengenalkan UMKM dan produk masyarakat Desa Kreyongan Atas agar lebih mudah ditemukan.
                </p>

                <div class="space-y-3 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-[#0F623F] flex items-center justify-center mt-0.5">
                            <span class="text-white text-[10px] font-bold">1</span>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-sm text-[#111827] mb-0.5">Mengenalkan Usaha Desa</h4>
                            <p class="text-xs text-[#6B7280] leading-relaxed">Memberi panggung bagi pelaku UMKM yang sulit dijangkau.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-[#0F623F] flex items-center justify-center mt-0.5">
                            <span class="text-white text-[10px] font-bold">2</span>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-sm text-[#111827] mb-0.5">Memperluas Jangkauan</h4>
                            <p class="text-xs text-[#6B7280] leading-relaxed">Produk lokal tidak lagi hanya dikenal di lingkungan sekitar.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-[#0F623F] flex items-center justify-center mt-0.5">
                            <span class="text-white text-[10px] font-bold">3</span>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-sm text-[#111827] mb-0.5">Mendorong Ekonomi Lokal</h4>
                            <p class="text-xs text-[#6B7280] leading-relaxed">Setiap transaksi adalah dukungan nyata bagi desa.</p>
                        </div>
                    </div>
                </div>

                <div class="border-l-2 border-[#0F623F] pl-4">
                    <p class="font-display text-sm font-medium text-[#111827] leading-snug italic">
                        Mengenalkan usaha lokal hari ini untuk membuka peluang di masa depan.
                    </p>
                    <p class="text-[10px] text-[#6B7280] mt-1.5 font-bold uppercase tracking-wider">— Visi Djajan</p>
                </div>

            </div>

        </div>

    </div>
</section>


{{-- ==================== 04 · PRODUK ==================== --}}
<section id="produk" class="relative overflow-hidden px-6 lg:px-8 py-20 lg:py-28 bg-white">

    <div class="relative mx-auto max-w-7xl">

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12 lg:mb-16">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-px w-8 bg-[#0F623F]"></div>
                    <span class="text-xs font-medium uppercase tracking-widest text-[#0F623F]">Produk Lokal</span>
                    <span class="text-xs text-[#6B7280] font-display">03 / Katalog</span>
                </div>
                <h2 class="font-display text-4xl lg:text-5xl font-bold leading-[1.08] text-[#111827]">
                    Dari dapur desa,<br class="hidden lg:block"> untuk meja Anda.
                </h2>
                <p class="mt-4 text-lg text-[#374151] leading-relaxed max-w-xl">
                    Produk pilihan dari UMKM Desa Kreyongan Atas — dibuat dengan tangan, ditanam dengan hati.
                </p>
            </div>
            <a href="#" class="group inline-flex items-center gap-2 text-sm font-medium text-[#111827] hover:text-[#0F623F] transition-colors self-start lg:self-auto">
                Lihat semua produk
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

            @if($products->isNotEmpty())
            @php
            $featuredProduct = $products->first();
            $otherProducts = $products->skip(1);
            @endphp

            {{-- Featured --}}
            <article class="group md:col-span-2 lg:col-span-2 relative overflow-hidden rounded-3xl bg-[#FAFAF9] border border-gray-100 hover:border-[#0F623F]/30 transition-all duration-500 hover:shadow-xl">
                <div class="grid md:grid-cols-2">
                    <div class="relative aspect-square md:aspect-auto overflow-hidden bg-gray-100">
                        @if($featuredProduct->gambar_produk)
                        <img src="{{ $featuredProduct->urlGambar() }}" alt="{{ $featuredProduct->nama_produk }}" class="img-zoom w-full h-full object-cover">
                        @else
                        <div class="w-full h-full min-h-[300px] flex items-center justify-center bg-[#F3F4F6]">
                            <span class="text-sm text-gray-400">Belum ada gambar</span>
                        </div>
                        @endif
                        <div class="absolute top-5 left-5 bg-[#0F623F] text-white text-xs font-medium px-3 py-1.5 rounded-full">Produk Terbaru</div>
                    </div>

                    <div class="p-8 lg:p-10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-block h-1.5 w-1.5 rounded-full bg-[#0F623F]"></span>
                                <span class="text-xs font-medium text-[#0F623F] uppercase tracking-wide">{{ $featuredProduct->kategori ?: 'Produk' }}</span>
                            </div>
                            <h3 class="font-display text-2xl lg:text-3xl font-bold text-[#111827] mb-3">{{ $featuredProduct->nama_produk }}</h3>
                            <p class="text-[#374151] leading-relaxed mb-6">{{ $featuredProduct->deskripsi ?: 'Produk lokal dari UMKM Desa Kreyongan Atas.' }}</p>

                            <div class="flex flex-wrap items-center gap-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#4CAF50]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                    </svg>
                                    <span class="text-[#6B7280]">Tersedia</span>
                                </div>
                                @if($featuredProduct->umkm)
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#F59E0B]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                    </svg>
                                    <span class="text-[#6B7280]">{{ $featuredProduct->umkm->nama_usaha }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs text-[#6B7280] mb-1">Harga</p>
                                    <p class="font-display text-xl font-bold text-[#111827]">Rp {{ number_format($featuredProduct->harga, 0, ',', '.') }}</p>
                                </div>
                                <button type="button" data-detail-modal
                                    data-modal-title="{{ $featuredProduct->nama_produk }}"
                                    data-modal-category="{{ $featuredProduct->kategori ?: 'Produk' }}"
                                    data-modal-description="{{ $featuredProduct->deskripsi ?: 'Produk lokal dari UMKM Desa Kreyongan Atas.' }}"
                                    data-modal-price="Rp {{ number_format($featuredProduct->harga, 0, ',', '.') }}"
                                    data-modal-label="Harga"
                                    data-modal-image="{{ $featuredProduct->gambar_produk ? $featuredProduct->urlGambar() : '' }}"
                                    class="group/btn inline-flex items-center gap-2 rounded-full bg-[#111827] px-6 py-3 text-sm font-semibold text-white hover:bg-[#0F623F] transition-colors duration-300">
                                    Lihat Detail
                                    <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Lainnya --}}
            @foreach($otherProducts as $product)
            <article class="group relative overflow-hidden rounded-3xl bg-white border border-gray-100 hover:border-[#4CAF50]/30 transition-all duration-300 hover:shadow-lg">
                <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                    @if($product->gambar_produk)
                    <img src="{{ $product->urlGambar() }}" alt="{{ $product->nama_produk }}" class="img-zoom w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-[#F3F4F6]">
                        <span class="text-sm text-gray-400">Belum ada gambar</span>
                    </div>
                    @endif
                    <div class="absolute top-4 left-4 bg-[#4CAF50] text-white text-xs font-medium px-3 py-1.5 rounded-full">{{ $product->kategori ?: 'Produk' }}</div>
                </div>

                <div class="p-6">
                    <h3 class="font-display text-xl font-semibold text-[#111827] mb-2">{{ $product->nama_produk }}</h3>
                    <p class="text-sm text-[#6B7280] leading-relaxed mb-4 line-clamp-2">{{ $product->deskripsi ?: 'Produk lokal dari UMKM Desa Kreyongan Atas.' }}</p>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <p class="font-display text-lg font-bold text-[#111827]">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>
                        <button type="button" data-detail-modal
                            data-modal-title="{{ $product->nama_produk }}"
                            data-modal-category="{{ $product->kategori ?: 'Produk' }}"
                            data-modal-description="{{ $product->deskripsi ?: 'Produk lokal dari UMKM Desa Kreyongan Atas.' }}"
                            data-modal-price="Rp {{ number_format($product->harga, 0, ',', '.') }}"
                            data-modal-label="Harga"
                            data-modal-image="{{ $product->gambar_produk ? $product->urlGambar() : '' }}"
                            class="text-sm font-medium text-[#0F623F] hover:text-[#0A4A2F] transition-colors inline-flex items-center gap-1">
                            Detail
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </article>
            @endforeach
            @else
            <div class="md:col-span-2 lg:col-span-3 rounded-3xl border border-dashed border-gray-200 bg-[#FAFAF9] py-16 px-6 text-center">
                <h3 class="font-display text-xl font-semibold text-[#111827] mb-2">Belum Ada Produk</h3>
                <p class="text-sm text-[#6B7280]">Produk UMKM Desa Kreyongan Atas akan ditampilkan di sini.</p>
            </div>
            @endif

        </div>
    </div>
</section>


{{-- ==================== 05 · UMKM ==================== --}}
<section id="umkm" class="relative overflow-hidden px-6 lg:px-8 py-20 lg:py-28 bg-[#FAFAF9]">

    <div class="relative mx-auto max-w-7xl">

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12 lg:mb-16">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-px w-8 bg-[#0F623F]"></div>
                    <span class="text-xs font-medium uppercase tracking-widest text-[#0F623F]">UMKM Lokal</span>
                    <span class="text-xs text-[#6B7280] font-display">04 / Usaha Desa</span>
                </div>
                <h2 class="font-display text-4xl lg:text-5xl font-bold leading-[1.08] text-[#111827]">
                    Wajah-wajah di balik<br class="hidden lg:block">
                    <span class="text-[#0F623F]">UMKM Kreyongan.</span>
                </h2>
                <p class="mt-4 text-lg text-[#374151] leading-relaxed max-w-xl">
                    Setiap usaha punya cerita. Kenali pelaku UMKM Desa Kreyongan Atas dan dukung perjalanan mereka.
                </p>
            </div>
            <a href="#" class="group inline-flex items-center gap-2 text-sm font-medium text-[#111827] hover:text-[#0F623F] transition-colors self-start lg:self-auto">
                Lihat semua UMKM
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        @if($featuredUmkm)
        <article class="group relative overflow-hidden rounded-3xl bg-white border border-gray-100 hover:border-[#0F623F]/30 transition-all duration-500 hover:shadow-xl mb-8">
            <div class="grid lg:grid-cols-5">
                <div class="lg:col-span-3 relative aspect-[16/10] lg:aspect-auto overflow-hidden bg-gray-100">
                    @if($featuredUmkm->foto_profil)
                    <img src="{{ $featuredUmkm->urlFotoProfil() }}" alt="{{ $featuredUmkm->nama_usaha }}" class="img-zoom w-full h-full object-cover">
                    @else
                    <div class="w-full h-full min-h-[350px] flex items-center justify-center bg-[#F3F4F6]">
                        <span class="text-sm text-gray-400">Belum ada gambar</span>
                    </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    <div class="absolute top-6 left-6 bg-[#0F623F] text-white text-xs font-medium px-4 py-2 rounded-full shadow-lg">UMKM Unggulan</div>
                    <div class="absolute bottom-6 left-6 flex items-center gap-2 bg-white/95 backdrop-blur-sm rounded-full px-4 py-2 shadow-sm">
                        <svg class="w-4 h-4 text-[#0F623F]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                        </svg>
                        <span class="text-xs font-medium text-[#111827]">Desa Kreyongan Atas</span>
                    </div>
                </div>

                <div class="lg:col-span-2 p-8 lg:p-10 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-[#0F623F]"></span>
                            <span class="text-xs font-medium text-[#0F623F] uppercase tracking-wide">{{ $featuredUmkm->spesialis ?: 'UMKM Lokal' }}</span>
                        </div>
                        <h3 class="font-display text-3xl lg:text-4xl font-bold text-[#111827] mb-4">{{ $featuredUmkm->nama_usaha }}</h3>
                        <p class="text-[#374151] leading-relaxed mb-6">{{ $featuredUmkm->deskripsi_usaha ?: 'UMKM lokal Desa Kreyongan Atas.' }}</p>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <p class="font-display text-2xl font-bold text-[#111827]">{{ $featuredUmkm->produk_count }}</p>
                                <p class="text-xs text-[#6B7280] mt-1">Produk</p>
                            </div>
                            @if($featuredUmkm->spesialis)
                            <div class="border-l border-gray-200 pl-4">
                                <p class="font-display text-sm font-bold text-[#111827] line-clamp-2">{{ $featuredUmkm->spesialis }}</p>
                                <p class="text-xs text-[#6B7280] mt-1">Spesialisasi</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200">
                        <button type="button" data-detail-modal
                            data-modal-title="{{ $featuredUmkm->nama_usaha }}"
                            data-modal-category="{{ $featuredUmkm->spesialis ?: 'UMKM Lokal' }}"
                            data-modal-description="{{ $featuredUmkm->deskripsi_usaha ?: 'UMKM lokal Desa Kreyongan Atas.' }}"
                            data-modal-price="{{ $featuredUmkm->produk_count }} Produk"
                            data-modal-label="Etalase"
                            data-modal-image="{{ $featuredUmkm->foto_profil ? $featuredUmkm->urlFotoProfil() : '' }}"
                            class="group/btn inline-flex items-center justify-between w-full rounded-full bg-[#111827] px-6 py-4 text-sm font-semibold text-white hover:bg-[#0F623F] transition-colors duration-300">
                            <span>Kunjungi UMKM</span>
                            <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </article>
        @endif

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @php
            $otherUmkms = $umkms->filter(function ($umkm) use ($featuredUmkm) {
            return !$featuredUmkm || $umkm->id_umkm !== $featuredUmkm->id_umkm;
            });
            @endphp

            @forelse($otherUmkms as $umkm)
            <article class="group relative overflow-hidden rounded-3xl bg-white border border-gray-100 hover:border-[#0F623F]/30 transition-all duration-300 hover:shadow-lg">
                <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
                    @if($umkm->foto_profil)
                    <img src="{{ $umkm->urlFotoProfil() }}" alt="{{ $umkm->nama_usaha }}" class="img-zoom w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-[#F3F4F6]">
                        <span class="text-sm text-gray-400">Belum ada gambar</span>
                    </div>
                    @endif

                    @if($umkm->status_verifikasi === 'terverifikasi')
                    <div class="absolute top-4 left-4 bg-[#0F623F] text-white text-xs font-medium px-3 py-1.5 rounded-full shadow-sm">Terverifikasi</div>
                    @elseif($umkm->spesialis)
                    <div class="absolute top-4 left-4 bg-[#6B7280] text-white text-xs font-medium px-3 py-1.5 rounded-full shadow-sm">{{ $umkm->spesialis }}</div>
                    @endif
                </div>

                <div class="p-6">
                    <h3 class="font-display text-xl font-semibold text-[#111827] mb-2">{{ $umkm->nama_usaha }}</h3>
                    <p class="text-sm text-[#6B7280] leading-relaxed mb-4 line-clamp-2">{{ $umkm->deskripsi_usaha ?: 'UMKM lokal Desa Kreyongan Atas.' }}</p>

                    <div class="flex items-center gap-4 text-xs text-[#6B7280] mb-5 pb-5 border-b border-gray-100">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                            </svg>
                            <span>Kreyongan Atas</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                            </svg>
                            <span>{{ $umkm->produk_count }} Produk</span>
                        </div>
                    </div>

                    <button type="button" data-detail-modal
                        data-modal-title="{{ $umkm->nama_usaha }}"
                        data-modal-category="{{ $umkm->spesialis ?: 'UMKM Lokal' }}"
                        data-modal-description="{{ $umkm->deskripsi_usaha ?: 'UMKM lokal Desa Kreyongan Atas.' }}"
                        data-modal-price="{{ $umkm->produk_count }} Produk"
                        data-modal-label="Etalase"
                        data-modal-image="{{ $umkm->foto_profil ? $umkm->urlFotoProfil() : '' }}"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-[#0F623F] hover:text-[#0A4A2F] transition-colors">
                        Lihat UMKM
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </article>
            @empty
            <div class="md:col-span-2 lg:col-span-3 rounded-3xl border border-dashed border-gray-200 bg-white py-12 px-6 text-center">
                <h3 class="font-display text-xl font-semibold text-[#111827] mb-2">Belum Ada UMKM Lain</h3>
                <p class="text-sm text-[#6B7280]">UMKM lainnya akan ditampilkan di sini.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>


{{-- ==================== 06 · CTA (DOWNLOAD APP) ==================== --}}
<section class="relative py-24 lg:py-32 px-6 lg:px-8 overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1593113598332-cd288d649433?w=1920&q=80" alt="" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-[#0B1512]/85"></div>
    </div>

    <div class="relative mx-auto max-w-3xl">
        <div class="text-center">

            {{-- Label --}}
            <div class="inline-flex items-center gap-2.5 mb-6">
                <div class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#F59E0B]">Aplikasi Mobile</span>
            </div>

            {{-- Headline --}}
            <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold leading-[1.05] text-white tracking-tight mb-5">
                Belanja Lebih Mudah<br class="hidden md:block">
                dengan <span class="text-[#F59E0B]">Aplikasi Djajan.</span>
            </h2>

            {{-- Paragraf --}}
            <p class="text-base md:text-lg text-white/70 leading-relaxed max-w-xl mx-auto mb-8">
                Download aplikasi kami untuk pengalaman belanja yang lebih cepat. Temukan produk lokal Desa Kreyongan Atas langsung dari genggaman tangan Anda.
            </p>

            {{-- Download Button --}}
            <div class="mb-4">
                <a href="{{ asset('apk/djajan.apk') }}"
                   download
                   class="group inline-flex items-center gap-3 rounded-full bg-white px-8 py-4 text-sm font-semibold text-[#111827] hover:bg-gray-100 transition-all duration-300 shadow-xl">
                    <svg class="w-5 h-5 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Aplikasi (Android)
                </a>
            </div>

            {{-- Version Info --}}
            <p class="text-sm text-white/50">
                Versi 1.0.0 &middot; 15 MB &middot; Android 5.0+
            </p>

        </div>
    </div>
</section>


{{-- ==================== MODAL DETAIL ==================== --}}
<div id="detail-modal" role="dialog" aria-modal="true" aria-labelledby="detail-modal-title">
    <div class="dm-card">
        <button type="button" class="dm-close" data-modal-close aria-label="Tutup">&times;</button>

        <div class="dm-media">
            <img id="detail-modal-image" alt="" style="display:none">
            <span id="detail-modal-placeholder" class="dm-empty">Belum ada gambar</span>
        </div>

        <div class="dm-body">
            <span id="detail-modal-category" class="dm-chip"></span>
            <h2 id="detail-modal-title" class="dm-title"></h2>
            <p id="detail-modal-description" class="dm-desc"></p>

            <div class="dm-footer">
                <div class="dm-info">
                    <div>
                        <p id="detail-modal-label" class="dm-label">Harga</p>
                        <p id="detail-modal-price" class="dm-value"></p>
                    </div>
                </div>
                <button type="button" class="dm-btn" data-modal-close>Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<style>
    /* ===== Modal Detail ===== */
    #detail-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(15, 23, 42, .55);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        opacity: 0;
        transition: opacity .2s ease
    }

    #detail-modal.is-open {
        display: flex
    }

    #detail-modal.is-visible {
        opacity: 1
    }

    #detail-modal .dm-card {
        position: relative;
        display: grid;
        grid-template-columns: 1fr;
        width: 100%;
        max-width: 880px;
        max-height: calc(100vh - 32px);
        overflow: hidden;
        background: #fff;
        border-radius: 28px;
        box-shadow: 0 30px 80px -20px rgba(0, 0, 0, .45);
        transform: translateY(16px) scale(.98);
        transition: transform .25s ease
    }

    #detail-modal.is-visible .dm-card {
        transform: none
    }

    #detail-modal .dm-media {
        position: relative;
        background: #ECF3EF;
        min-height: 240px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden
    }

    #detail-modal .dm-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        max-height: 320px
    }

    #detail-modal .dm-empty {
        color: #8AA093;
        font-size: 14px
    }

    #detail-modal .dm-body {
        padding: 28px 26px 26px;
        overflow-y: auto;
        display: flex;
        flex-direction: column
    }

    #detail-modal .dm-chip {
        align-self: flex-start;
        padding: 6px 12px;
        border-radius: 999px;
        background: #E7F4EC;
        color: #0F623F;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .02em;
        margin-bottom: 14px
    }

    #detail-modal .dm-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        letter-spacing: -.02em;
        font-size: 28px;
        line-height: 1.2;
        font-weight: 800;
        color: #111827;
        margin: 0 0 12px
    }

    #detail-modal .dm-desc {
        color: #4B5563;
        line-height: 1.7;
        font-size: 15px;
        margin: 0;
        white-space: pre-line
    }

    #detail-modal .dm-footer {
        margin-top: auto;
        padding-top: 22px
    }

    #detail-modal .dm-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        border-radius: 18px;
        background: #F4F8F5;
        border: 1px solid #E1EDE5
    }

    #detail-modal .dm-label {
        font-size: 12px;
        color: #6B7280;
        margin: 0 0 2px
    }

    #detail-modal .dm-value {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 22px;
        font-weight: 800;
        color: #0F623F;
        margin: 0
    }

    #detail-modal .dm-close {
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 3;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 0;
        cursor: pointer;
        background: #fff;
        color: #111827;
        font-size: 22px;
        line-height: 1;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .18);
        transition: transform .15s ease, background .15s ease
    }

    #detail-modal .dm-close:hover {
        background: #F3F4F6;
        transform: scale(1.06)
    }

    #detail-modal .dm-btn {
        margin-top: 12px;
        width: 100%;
        padding: 13px 16px;
        border: 0;
        border-radius: 14px;
        cursor: pointer;
        background: #0F623F;
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        transition: background .15s ease
    }

    #detail-modal .dm-btn:hover {
        background: #0A4A2F
    }

    @media (min-width:768px) {
        #detail-modal .dm-card {
            grid-template-columns: 1fr 1fr
        }

        #detail-modal .dm-media img {
            max-height: none;
            min-height: 460px
        }

        #detail-modal .dm-body {
            padding: 44px 40px 36px
        }

        #detail-modal .dm-title {
            font-size: 32px
        }
    }
</style>

<script>

    /* ---------- Modal Detail ---------- */
    (function() {
        const modal = document.getElementById('detail-modal');
        if (!modal) return;

        const image = document.getElementById('detail-modal-image');
        const placeholder = document.getElementById('detail-modal-placeholder');
        const $ = (id) => document.getElementById(id);

        const showImage = (src) => {
            if (!src) {
                image.style.display = 'none';
                image.removeAttribute('src');
                placeholder.style.display = '';
                return;
            }
            image.onerror = () => {
                image.style.display = 'none';
                placeholder.style.display = '';
            };
            image.onload = () => {
                image.style.display = 'block';
                placeholder.style.display = 'none';
            };
            image.src = src;
        };

        const open = (d) => {
            $('detail-modal-title').textContent = d.modalTitle || '';
            $('detail-modal-category').textContent = d.modalCategory || '';
            $('detail-modal-description').textContent = d.modalDescription || '';
            $('detail-modal-label').textContent = d.modalLabel || 'Informasi';
            $('detail-modal-price').textContent = d.modalPrice || '';
            image.alt = d.modalTitle || '';
            showImage(d.modalImage);
            modal.classList.add('is-open');
            requestAnimationFrame(() => modal.classList.add('is-visible'));
            document.body.style.overflow = 'hidden';
        };

        const close = () => {
            modal.classList.remove('is-visible');
            setTimeout(() => modal.classList.remove('is-open'), 200);
            document.body.style.overflow = '';
        };

        document.querySelectorAll('[data-detail-modal]').forEach(btn => {
            btn.addEventListener('click', () => open(btn.dataset));
        });
        modal.querySelectorAll('[data-modal-close]').forEach(b => b.addEventListener('click', close));
        modal.addEventListener('click', e => {
            if (e.target === modal) close();
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) close();
        });
    })();
</script>
@endpush