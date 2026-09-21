@extends('layouts.public')

@section('title', 'Home - Djajan')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #0F623F;
        --primary-ink: #0A4A2F;
        --secondary: #4CAF50;
        --accent: #F59E0B;
        --ink: #111827;
        --ink-soft: #374151;
        --muted: #6B7280;
        --line: #E5E7EB;
        --surface: #FAFAF9;
        --surface-alt: #F5F5F4;
    }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        -webkit-font-smoothing: antialiased;
        color: var(--ink);
        background: #FFFFFF;
    }

    .font-display {
        font-family: 'Plus Jakarta Sans', sans-serif;
        letter-spacing: -0.02em;
    }

    html {
        scroll-behavior: smooth;
    }

    /* Subtle transitions */
    .link-underline {
        position: relative;
    }

    .link-underline::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 0;
        height: 1px;
        background: var(--primary);
        transition: width 0.3s ease;
    }

    .link-underline:hover::after {
        width: 100%;
    }

    .btn-primary {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .card-subtle {
        transition: all 0.3s ease;
        border: 1px solid var(--line);
    }

    .card-subtle:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    .img-zoom {
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .img-zoom:hover {
        transform: scale(1.03);
    }

    .num-display {
        font-variant-numeric: tabular-nums;
    }

    .hairline {
        height: 1px;
        background: var(--line);
    }

    /* Subtle dot pattern */
    .dot-pattern {
        background-image: radial-gradient(circle, #0F623F 0.5px, transparent 0.5px);
        background-size: 24px 24px;
        opacity: 0.03;
    }
</style>


<!-- ==================== HERO SECTION ==================== -->
<section id="home" class="relative min-h-screen overflow-hidden">

    <!-- Background Image -->
    <div class="absolute inset-0">
        <img
            src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=1920&q=80"
            alt="Pemandangan Desa Kreyongan Atas"
            class="w-full h-full object-cover">
        <!-- Gradient overlay untuk readability -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#111827]/90 via-[#111827]/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#111827]/70 via-transparent to-[#111827]/30"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 min-h-screen flex flex-col justify-between px-6 lg:px-8 py-24 lg:py-20">

        <!-- Main Content (middle-left) -->
        <div class="flex-1 flex items-center">
            <div class="mx-auto max-w-7xl w-full">
                <div class="max-w-3xl">
                    <!-- Heading -->
                    <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-[1.05] text-white mb-6">
                        Kenali dan Dukung
                        <br>
                        <span class="text-[#4CAF50]">UMKM Lokal</span>
                        <br>
                        Bersama Djajan.
                    </h1>

                    <!-- Description -->
                    <p class="text-lg lg:text-xl text-white/70 leading-relaxed max-w-2xl mb-10">
                        Temukan berbagai usaha dan produk lokal dari Desa Kreyongan Atas dalam satu platform. Kenali produknya, dukung usahanya, dan ikut menggerakkan ekonomi lokal.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap items-center gap-4">
                        <a
                            href="#umkm"
                            class="group inline-flex items-center gap-2 rounded-full bg-[#F59E0B] px-7 py-3.5 text-sm font-semibold text-[#111827] hover:bg-[#FBBF24] transition-all duration-200 hover:shadow-lg hover:shadow-[#F59E0B]/30">
                            Jelajahi UMKM
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        <a
                            href="#tentang"
                            class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/5 backdrop-blur-sm px-7 py-3.5 text-sm font-medium text-white hover:bg-white/10 hover:border-white/50 transition-all duration-200">
                            Tentang Djajan
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <!-- Stats Row (bottom) -->
        <div class="mx-auto max-w-7xl w-full pt-12  border-white/10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12">

                <div>
                    <p class="font-display text-3xl lg:text-4xl font-bold text-white num-display">25+</p>
                    <p class="mt-2 text-sm text-white/60">UMKM Lokal</p>
                    <div class="mt-3 h-px w-12 bg-white/20"></div>
                </div>

                <div>
                    <p class="font-display text-3xl lg:text-4xl font-bold text-white num-display">100+</p>
                    <p class="mt-2 text-sm text-white/60">Produk Lokal</p>
                    <div class="mt-3 h-px w-12 bg-white/20"></div>
                </div>

                <div>
                    <p class="font-display text-3xl lg:text-4xl font-bold text-white num-display">5+</p>
                    <p class="mt-2 text-sm text-white/60">Kategori Usaha</p>
                    <div class="mt-3 h-px w-12 bg-white/20"></div>
                </div>

                <div>
                    <p class="font-display text-3xl lg:text-4xl font-bold text-[#F59E0B] num-display">1</p>
                    <p class="mt-2 text-sm text-white/60">Desa Kreyongan Atas</p>
                    <div class="mt-3 h-px w-12 bg-[#F59E0B]/40"></div>
                </div>

            </div>
        </div>

    </div>

</section>

<!-- ==================== ABOUT DJAJAN ==================== -->
<section id="tentang" class="py-20 lg:py-28 px-6 lg:px-8 bg-[#FAFAF9] relative overflow-hidden">

    <!-- Subtle background accent -->
    <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-[#0F623F]/[0.02] to-transparent pointer-events-none"></div>

    <div class="relative mx-auto max-w-7xl">

        <!-- Section Label -->
        <div class="flex items-center gap-3 mb-12 lg:mb-16">
            <div class="h-px w-8 bg-[#0F623F]"></div>
            <span class="text-xs font-medium uppercase tracking-widest text-[#0F623F]">Tentang Djajan</span>
            <div class="hidden sm:block h-px flex-1 bg-gray-200"></div>
            <span class="hidden sm:block text-xs text-[#6B7280] font-display">02 / Cerita Kami</span>
        </div>

        <div class="grid lg:grid-cols-12 gap-10 lg:gap-16 items-start">

            <!-- Left: Image Composition (asymmetric) -->
            <div class="lg:col-span-6 relative">

                <!-- Main Image -->
                <div class="relative rounded-3xl overflow-hidden bg-gray-100 aspect-[4/5] lg:aspect-[5/6]">
                    <img
                        src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=900&q=80"
                        alt="Pemandangan Desa Kreyongan Atas"
                        class="img-zoom w-full h-full object-cover">

                    <!-- Bottom annotation (handwritten feel) -->
                    <div class="absolute bottom-6 left-6 right-6">
                        <div class="bg-[#111827]/90 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
                            <p class="text-white/60 text-xs uppercase tracking-wider mb-1">Lokasi</p>
                            <p class="font-display text-lg font-semibold text-white">Kreyongan Atas, Jawa Barat</p>
                            <p class="text-white/50 text-sm mt-1">Rumah bagi puluhan UMKM lokal</p>
                        </div>
                    </div>
                </div>

                <!-- Floating accent image -->
                <div class="hidden lg:block absolute -right-8 top-16 w-40 h-48 rounded-2xl overflow-hidden shadow-xl border-4 border-[#FAFAF9]">
                    <img
                        src="https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=300&q=80"
                        alt="Produk lokal"
                        class="w-full h-full object-cover">
                </div>

                <!-- Decorative number -->
                <div class="hidden lg:block absolute -bottom-6 -left-4 font-display text-[180px] font-bold text-[#0F623F]/[0.04] leading-none pointer-events-none select-none">
                    02
                </div>

            </div>

            <!-- Right: Content -->
            <div class="lg:col-span-6 lg:pt-4">

                <h2 class="font-display text-4xl lg:text-5xl font-bold leading-[1.08] text-[#111827]">
                    Dari desa,
                    <br>
                    untuk semua.
                </h2>

                <p class="mt-6 text-lg leading-relaxed text-[#374151]">
                    Djajan lahir dari kebutuhan nyata — mengenalkan UMKM dan produk masyarakat Desa Kreyongan Atas agar lebih mudah ditemukan, bukan hanya oleh tetangga, tapi oleh siapa saja.
                </p>

                <p class="mt-4 text-lg leading-relaxed text-[#374151]">
                    Melalui website dan aplikasi mobile, kami membangun satu sistem terpadu di mana informasi usaha dan produk lokal bisa dikelola dan diakses dengan mudah.
                </p>

                <!-- Feature list with icons (not bullets) -->
                <div class="mt-10 space-y-5">

                    <div class="flex items-start gap-4 group">
                        <div class="shrink-0 w-11 h-11 rounded-xl bg-[#0F623F]/10 flex items-center justify-center group-hover:bg-[#0F623F] transition-colors duration-300">
                            <svg class="w-5 h-5 text-[#0F623F] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-display font-semibold text-[#111827] mb-1">Mengenalkan Usaha Desa</h4>
                            <p class="text-sm text-[#6B7280] leading-relaxed">Memberi panggung bagi pelaku UMKM yang selama ini sulit dijangkau.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 group">
                        <div class="shrink-0 w-11 h-11 rounded-xl bg-[#F59E0B]/10 flex items-center justify-center group-hover:bg-[#F59E0B] transition-colors duration-300">
                            <svg class="w-5 h-5 text-[#F59E0B] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-display font-semibold text-[#111827] mb-1">Memperluas Jangkauan Produk</h4>
                            <p class="text-sm text-[#6B7280] leading-relaxed">Produk lokal tidak lagi hanya dikenal di lingkungan sekitar.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 group">
                        <div class="shrink-0 w-11 h-11 rounded-xl bg-[#4CAF50]/10 flex items-center justify-center group-hover:bg-[#4CAF50] transition-colors duration-300">
                            <svg class="w-5 h-5 text-[#4CAF50] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-display font-semibold text-[#111827] mb-1">Mendorong Ekonomi Lokal</h4>
                            <p class="text-sm text-[#6B7280] leading-relaxed">Setiap kunjungan dan transaksi adalah dukungan nyata bagi desa.</p>
                        </div>
                    </div>

                </div>

                <!-- Editorial Pull Quote -->
                <div class="mt-12 relative pl-6">
                    <svg class="absolute -top-2 -left-2 w-10 h-10 text-[#F59E0B]/30" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                    </svg>
                    <p class="font-display text-lg lg:text-xl font-medium text-[#111827] leading-snug italic">
                        Mengenalkan usaha lokal hari ini untuk membuka lebih banyak peluang di masa depan.
                    </p>
                    <div class="mt-4 flex items-center gap-3">
                        <div class="h-px w-8 bg-[#F59E0B]"></div>
                        <span class="text-xs font-medium text-[#6B7280] uppercase tracking-wider">Visi Djajan</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

</section>

<!-- ==================== PRODUCTS ==================== -->
<section id="produk" class="py-20 lg:py-28 px-6 lg:px-8 bg-white relative overflow-hidden">

    <!-- Subtle background accent -->
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-[#0F623F]/20 to-transparent"></div>

    <div class="relative mx-auto max-w-7xl">

        <!-- Section Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12 lg:mb-16">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-px w-8 bg-[#0F623F]"></div>
                    <span class="text-xs font-medium uppercase tracking-widest text-[#0F623F]">Produk Lokal</span>
                    <div class="hidden sm:block h-px flex-1 bg-gray-200"></div>
                    <span class="hidden sm:block text-xs text-[#6B7280] font-display">03 / Katalog</span>
                </div>
                <h2 class="font-display text-4xl lg:text-5xl font-bold leading-[1.08] text-[#111827]">
                    Dari dapur desa,
                    <br class="hidden lg:block">
                    untuk meja Anda.
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

        <!-- Product Grid (Asymmetric) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

            @if($products->isNotEmpty())

            @php
            $featuredProduct = $products->first();
            $otherProducts = $products->skip(1);
            @endphp

            <!-- Featured Product -->
            <article class="group md:col-span-2 lg:col-span-2 relative overflow-hidden rounded-3xl bg-[#FAFAF9] border border-gray-100 hover:border-[#0F623F]/30 transition-all duration-500 hover:shadow-xl">

                <div class="grid md:grid-cols-2">

                    <!-- Image -->
                    <div class="relative aspect-square md:aspect-auto overflow-hidden bg-gray-100">

                        @if($featuredProduct->gambar)
                        <img
                            src="{{ asset('storage/' . $featuredProduct->gambar) }}"
                            alt="{{ $featuredProduct->nama_produk }}"
                            class="img-zoom w-full h-full object-cover">
                        @else
                        <div class="w-full h-full min-h-[300px] flex items-center justify-center bg-[#F3F4F6]">
                            <span class="text-sm text-gray-400">
                                Belum ada gambar
                            </span>
                        </div>
                        @endif

                        <!-- Badge -->
                        <div class="absolute top-5 left-5 bg-[#0F623F] text-white text-xs font-medium px-3 py-1.5 rounded-full">
                            Produk Terbaru
                        </div>

                    </div>

                    <!-- Content -->
                    <div class="p-8 lg:p-10 flex flex-col justify-between">

                        <div>

                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-block h-1.5 w-1.5 rounded-full bg-[#0F623F]"></span>

                                <span class="text-xs font-medium text-[#0F623F] uppercase tracking-wide">
                                    {{ $featuredProduct->kategori ?: 'Produk' }}
                                </span>
                            </div>

                            <h3 class="font-display text-2xl lg:text-3xl font-bold text-[#111827] mb-3">
                                {{ $featuredProduct->nama_produk }}
                            </h3>

                            <p class="text-[#374151] leading-relaxed mb-6">
                                {{ $featuredProduct->deskripsi ?: 'Produk lokal dari UMKM Desa Kreyongan Atas.' }}
                            </p>

                            <div class="flex items-center gap-4 text-sm">

                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#4CAF50]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                    </svg>

                                    <span class="text-[#6B7280]">
                                        Tersedia
                                    </span>
                                </div>

                                @if($featuredProduct->umkm)
                                <div class="flex items-center gap-2">

                                    <svg class="w-4 h-4 text-[#F59E0B]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                    </svg>

                                    <span class="text-[#6B7280]">
                                        {{ $featuredProduct->umkm->nama_umkm }}
                                    </span>

                                </div>
                                @endif

                            </div>

                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">

                            <div class="flex items-center justify-between">

                                <div>
                                    <p class="text-xs text-[#6B7280] mb-1">
                                        Harga
                                    </p>

                                    <p class="font-display text-xl font-bold text-[#111827]">
                                        Rp {{ number_format($featuredProduct->harga, 0, ',', '.') }}
                                    </p>
                                </div>

                                <a
                                    href="#"
                                    class="group/btn inline-flex items-center gap-2 rounded-full bg-[#111827] px-6 py-3 text-sm font-semibold text-white hover:bg-[#0F623F] transition-colors duration-300">
                                    Lihat Detail

                                    <svg
                                        class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.5"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </article>


            <!-- Other Products -->
            @foreach($otherProducts as $product)

            <article class="group relative overflow-hidden rounded-3xl bg-white border border-gray-100 hover:border-[#4CAF50]/30 transition-all duration-300 hover:shadow-lg">

                <!-- Image -->
                <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">

                    @if($product->gambar)

                    <img
                        src="{{ asset('storage/' . $product->gambar) }}"
                        alt="{{ $product->nama_produk }}"
                        class="img-zoom w-full h-full object-cover">

                    @else

                    <div class="w-full h-full flex items-center justify-center bg-[#F3F4F6]">
                        <span class="text-sm text-gray-400">
                            Belum ada gambar
                        </span>
                    </div>

                    @endif

                    <div class="absolute top-4 left-4 bg-[#4CAF50] text-white text-xs font-medium px-3 py-1.5 rounded-full">
                        {{ $product->kategori ?: 'Produk' }}
                    </div>

                </div>

                <!-- Content -->
                <div class="p-6">

                    <div class="flex items-center gap-2 mb-2">

                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-[#4CAF50]"></span>

                        <span class="text-xs font-medium text-[#4CAF50] uppercase tracking-wide">
                            {{ $product->kategori ?: 'Produk' }}
                        </span>

                    </div>

                    <h3 class="font-display text-xl font-semibold text-[#111827] mb-2">
                        {{ $product->nama_produk }}
                    </h3>

                    <p class="text-sm text-[#6B7280] leading-relaxed mb-4">
                        {{ $product->deskripsi ?: 'Produk lokal dari UMKM Desa Kreyongan Atas.' }}
                    </p>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">

                        <p class="font-display text-lg font-bold text-[#111827]">
                            Rp {{ number_format($product->harga, 0, ',', '.') }}
                        </p>

                        <a
                            href="#"
                            class="text-sm font-medium text-[#0F623F] hover:text-[#0A4A2F] transition-colors inline-flex items-center gap-1">
                            Detail

                            <svg
                                class="w-3.5 h-3.5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>

                        </a>

                    </div>

                </div>

            </article>

            @endforeach

            @else

            <!-- Empty State -->
            <div class="md:col-span-2 lg:col-span-3 rounded-3xl border border-dashed border-gray-200 bg-[#FAFAF9] py-16 px-6 text-center">

                <h3 class="font-display text-xl font-semibold text-[#111827] mb-2">
                    Belum Ada Produk
                </h3>

                <p class="text-sm text-[#6B7280]">
                    Produk UMKM Desa Kreyongan Atas akan ditampilkan di sini.
                </p>

            </div>

            @endif

        </div>
    </div>

</section>

<!-- ==================== UMKM LOKAL ==================== -->
<section id="umkm" class="py-20 lg:py-28 px-6 lg:px-8 bg-[#FAFAF9] relative overflow-hidden">

    <!-- Subtle background elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-[#0F623F]/[0.02] rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-[#F59E0B]/[0.02] rounded-full blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl">

        <!-- Section Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12 lg:mb-16">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-px w-8 bg-[#0F623F]"></div>
                    <span class="text-xs font-medium uppercase tracking-widest text-[#0F623F]">UMKM Lokal</span>
                    <div class="hidden sm:block h-px flex-1 bg-gray-200"></div>
                    <span class="hidden sm:block text-xs text-[#6B7280] font-display">04 / Usaha Desa</span>
                </div>
                <h2 class="font-display text-4xl lg:text-5xl font-bold leading-[1.08] text-[#111827]">
                    Wajah-wajah di balik
                    <br class="hidden lg:block">
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
        <!-- Featured UMKM (Full Width) -->
        <article class="group relative overflow-hidden rounded-3xl bg-white border border-gray-100 hover:border-[#0F623F]/30 transition-all duration-500 hover:shadow-xl mb-8">

            <div class="grid lg:grid-cols-5 gap-0">

                <!-- Image -->
                <div class="lg:col-span-3 relative aspect-[16/10] lg:aspect-auto overflow-hidden bg-gray-100">

                    @if($featuredUmkm->gambar)

                    <img
                        src="{{ asset('storage/' . $featuredUmkm->gambar) }}"
                        alt="{{ $featuredUmkm->nama_umkm }}"
                        class="img-zoom w-full h-full object-cover">

                    @else

                    <div class="w-full h-full min-h-[350px] flex items-center justify-center bg-[#F3F4F6]">
                        <span class="text-sm text-gray-400">
                            Belum ada gambar
                        </span>
                    </div>

                    @endif

                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent lg:bg-gradient-to-r"></div>

                    <!-- Badge -->
                    <div class="absolute top-6 left-6 bg-[#0F623F] text-white text-xs font-medium px-4 py-2 rounded-full shadow-lg">
                        UMKM Unggulan
                    </div>

                    <!-- Location -->
                    <div class="absolute bottom-6 left-6 flex items-center gap-2 bg-white/95 backdrop-blur-sm rounded-full px-4 py-2 shadow-sm">

                        <svg class="w-4 h-4 text-[#0F623F]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5 0-1.38 1.12-2.5 2.5-2.5 2.5 0 2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                        </svg>

                        <span class="text-xs font-medium text-[#111827]">
                            Desa Kreyongan Atas
                        </span>

                    </div>

                </div>


                <!-- Content -->
                <div class="lg:col-span-2 p-8 lg:p-10 flex flex-col justify-between">

                    <div>

                        <!-- Category -->
                        <div class="flex items-center gap-2 mb-4">

                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-[#0F623F]"></span>

                            <span class="text-xs font-medium text-[#0F623F] uppercase tracking-wide">
                                {{ $featuredUmkm->kategori }}
                            </span>

                        </div>


                        <!-- Name -->
                        <h3 class="font-display text-3xl lg:text-4xl font-bold text-[#111827] mb-4">
                            {{ $featuredUmkm->nama_umkm }}
                        </h3>


                        <!-- Description -->
                        <p class="text-[#374151] leading-relaxed mb-6">
                            {{ $featuredUmkm->deskripsi ?: 'UMKM lokal Desa Kreyongan Atas.' }}
                        </p>


                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6">

                            <div>

                                <p class="font-display text-2xl font-bold text-[#111827]">
                                    {{ $featuredUmkm->products_count }}
                                </p>

                                <p class="text-xs text-[#6B7280] mt-1">
                                    Produk
                                </p>

                            </div>


                            @if($featuredUmkm->tahun_berdiri)

                            <div class="border-l border-gray-200 pl-4">

                                <p class="font-display text-2xl font-bold text-[#111827]">
                                    {{ date('Y') - $featuredUmkm->tahun_berdiri }}
                                </p>

                                <p class="text-xs text-[#6B7280] mt-1">
                                    Tahun Berjalan
                                </p>

                            </div>

                            @endif

                        </div>

                    </div>


                    <!-- Button -->
                    <div class="pt-6 border-t border-gray-200">

                        <a
                            href="#"
                            class="group/btn inline-flex items-center justify-between w-full rounded-full bg-[#111827] px-6 py-4 text-sm font-semibold text-white hover:bg-[#0F623F] transition-colors duration-300">

                            <span>Kunjungi UMKM</span>

                            <svg
                                class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>

                        </a>

                    </div>

                </div>

            </div>

        </article>
        @endif

        <!-- UMKM Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

            @php
            $otherUmkms = $umkms->filter(function ($umkm) use ($featuredUmkm) {
            return !$featuredUmkm || $umkm->id !== $featuredUmkm->id;
            });
            @endphp

            @forelse($otherUmkms as $umkm)

            <article class="group relative overflow-hidden rounded-3xl bg-white border border-gray-100 hover:border-[#0F623F]/30 transition-all duration-300 hover:shadow-lg">

                <!-- Image -->
                <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">

                    @if($umkm->gambar)

                    <img
                        src="{{ asset('storage/' . $umkm->gambar) }}"
                        alt="{{ $umkm->nama_umkm }}"
                        class="img-zoom w-full h-full object-cover">

                    @else

                    <div class="w-full h-full flex items-center justify-center bg-[#F3F4F6]">
                        <span class="text-sm text-gray-400">
                            Belum ada gambar
                        </span>
                    </div>

                    @endif


                    @if($umkm->unggulan)

                    <div class="absolute top-4 left-4 bg-[#F59E0B] text-white text-xs font-medium px-3 py-1.5 rounded-full shadow-sm">
                        Unggulan
                    </div>

                    @else

                    <div class="absolute top-4 left-4 bg-[#0F623F] text-white text-xs font-medium px-3 py-1.5 rounded-full shadow-sm">
                        {{ $umkm->kategori }}
                    </div>

                    @endif

                </div>


                <!-- Content -->
                <div class="p-6">

                    <h3 class="font-display text-xl font-semibold text-[#111827] mb-2">
                        {{ $umkm->nama_umkm }}
                    </h3>


                    <p class="text-sm text-[#6B7280] leading-relaxed mb-4 line-clamp-2">
                        {{ $umkm->deskripsi ?: 'UMKM lokal Desa Kreyongan Atas.' }}
                    </p>


                    <!-- Meta info -->
                    <div class="flex items-center gap-4 text-xs text-[#6B7280] mb-5 pb-5 border-b border-gray-100">

                        <div class="flex items-center gap-1.5">

                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 9 12 9s2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                            </svg>

                            <span>
                                Kreyongan Atas
                            </span>

                        </div>


                        <div class="flex items-center gap-1.5">

                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                            </svg>

                            <span>
                                {{ $umkm->products_count }} Produk
                            </span>

                        </div>

                    </div>


                    <!-- Detail -->
                    <a
                        href="#"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-[#0F623F] hover:text-[#0A4A2F] transition-colors">

                        Lihat UMKM

                        <svg
                            class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7" />
                        </svg>

                    </a>

                </div>

            </article>

            @empty

            <div class="md:col-span-2 lg:col-span-3 rounded-3xl border border-dashed border-gray-200 bg-[#FAFAF9] py-12 px-6 text-center">

                <h3 class="font-display text-xl font-semibold text-[#111827] mb-2">
                    Belum Ada UMKM Lain
                </h3>

                <p class="text-sm text-[#6B7280]">
                    UMKM lainnya akan ditampilkan di sini.
                </p>

            </div>

            @endforelse

        </div>
    </div>

</section>

<!-- ==================== CTA SECTION ==================== -->
<section class="relative py-20 lg:py-28 px-6 lg:px-8 overflow-hidden">

    <!-- Background Image -->
    <div class="absolute inset-0">
        <img
            src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=1920&q=80"
            alt="Pemandangan Desa"
            class="w-full h-full object-cover">
        <!-- Gradient overlays -->
        <div class="absolute inset-0 bg-[#111827]/85"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-[#0F623F]/40 via-transparent to-[#111827]/90"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#111827] via-[#111827]/60 to-transparent"></div>
    </div>

    <!-- Decorative elements -->
    <div class="absolute top-10 left-10 w-64 h-64 bg-[#0F623F]/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 right-10 w-80 h-80 bg-[#F59E0B]/10 rounded-full blur-3xl"></div>

    <!-- Dot pattern overlay -->
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px;"></div>

    <div class="relative mx-auto max-w-7xl">

        <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">

            <!-- Left: Content -->
            <div class="lg:col-span-7">

                <!-- Section label -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-px w-8 bg-[#F59E0B]"></div>
                    <span class="text-xs font-medium uppercase tracking-widest text-[#F59E0B]">Bergabung Bersama Kami</span>
                </div>

                <h2 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-[1.05] text-white mb-6">
                    Dukung UMKM lokal,
                    <br>
                    mulai dari
                    <span class="text-[#F59E0B]"> langkah kecil.</span>
                </h2>

                <p class="text-lg text-white/70 leading-relaxed max-w-xl mb-10">
                    Setiap kunjungan, setiap produk yang kamu lihat, dan setiap usaha yang kamu kenali — itu semua adalah dukungan nyata bagi perekonomian Desa Kreyongan Atas.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap items-center gap-4">

                    <a href="#" class="group inline-flex items-center gap-2.5 rounded-full bg-[#F59E0B] px-8 py-4 text-sm font-semibold text-[#111827] hover:bg-[#FBBF24] transition-all duration-300 hover:shadow-xl hover:shadow-[#F59E0B]/30 hover:-translate-y-0.5">
                        Jelajahi UMKM Sekarang
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>

                    <a href="#tentang" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 backdrop-blur-sm px-8 py-4 text-sm font-medium text-white hover:bg-white/10 hover:border-white/40 transition-all duration-300">
                        Pelajari Lebih Lanjut
                    </a>

                </div>

                <!-- Trust indicators -->
                <div class="mt-12 pt-8 border-t border-white/10">
                    <div class="flex flex-wrap items-center gap-x-8 gap-y-4">

                        <div class="flex items-center gap-3">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-[#0F623F] border-2 border-[#111827] flex items-center justify-center">
                                    <span class="text-[10px] font-bold text-white">A</span>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-[#4CAF50] border-2 border-[#111827] flex items-center justify-center">
                                    <span class="text-[10px] font-bold text-white">B</span>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-[#F59E0B] border-2 border-[#111827] flex items-center justify-center">
                                    <span class="text-[10px] font-bold text-white">C</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">25+ UMKM</p>
                                <p class="text-xs text-white/50">Sudah bergabung</p>
                            </div>
                        </div>

                        <div class="hidden sm:block w-px h-10 bg-white/10"></div>

                        <div>
                            <p class="text-sm font-semibold text-white">100+ Produk Lokal</p>
                            <p class="text-xs text-white/50">Dari berbagai kategori</p>
                        </div>

                        <div class="hidden sm:block w-px h-10 bg-white/10"></div>

                        <div>
                            <p class="text-sm font-semibold text-white">1 Desa</p>
                            <p class="text-xs text-white/50">Kreyongan Atas</p>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right: Visual Card -->
            <div class="lg:col-span-5 hidden lg:block">

                <div class="relative">

                    <!-- Main card -->
                    <div class="relative bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20">

                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#0F623F] flex items-center justify-center">
                                    <span class="font-display font-bold text-white text-sm">D</span>
                                </div>
                                <div>
                                    <p class="font-display font-semibold text-white">Djajan</p>
                                    <p class="text-xs text-white/50">Platform UMKM Lokal</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#4CAF50]/20 border border-[#4CAF50]/30">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#4CAF50] animate-pulse"></div>
                                <span class="text-xs font-medium text-[#4CAF50]">Live</span>
                            </div>
                        </div>

                        <!-- Stats grid -->
                        <div class="grid grid-cols-2 gap-4 mb-6">

                            <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-[#0F623F]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <p class="font-display text-2xl font-bold text-white">{{$totalUmkm}}</p>
                                <p class="text-xs text-white/50 mt-1">UMKM Aktif</p>
                            </div>

                            <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-[#F59E0B]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <p class="font-display text-2xl font-bold text-white">{{$totalProducts}}</p>
                                <p class="text-xs text-white/50 mt-1">Produk Lokal</p>
                            </div>

                            <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-[#4CAF50]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="font-display text-2xl font-bold text-white">{{$totalCategories}}</p>
                                <p class="text-xs text-white/50 mt-1">Kategori</p>
                            </div>

                            <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-white/60" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                </div>
                                <p class="font-display text-2xl font-bold text-white">1</p>
                                <p class="text-xs text-white/50 mt-1">Desa</p>
                            </div>

                        </div>

                        <!-- Bottom info -->
                        <div class="pt-6 border-t border-white/10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-white/50 mb-1">Lokasi</p>
                                    <p class="text-sm font-medium text-white">Desa Kreyongan Atas</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-white/50 mb-1">Status</p>
                                    <p class="text-sm font-medium text-[#4CAF50]">Aktif</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Floating accent -->
                    <div class="absolute -top-4 -right-4 w-20 h-20 bg-[#F59E0B]/20 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-[#0F623F]/20 rounded-full blur-2xl"></div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection