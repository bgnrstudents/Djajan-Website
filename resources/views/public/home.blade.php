@extends('layouts.public')

@section('title', 'Home - Djajan')

@section('content')

<style>
    /* ===== Carousel (animasi dihandle GSAP) ===== */
    .slide-bg {
        will-change: transform, opacity;
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
        .md\:col-span-2,
        .lg\:col-span-2 {
            grid-column: auto;
        }

        /* Card overlap: reset absolute positioning for mobile */
        .lg\:absolute,
        .lg\:-bottom-10,
        .lg\:right-10,
        .lg\:w-\[380px\] {
            position: static;
            width: 100%;
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

    /* ===== Motion system (Produk & UMKM) ===== */
    :root {
        --ease: cubic-bezier(.22, 1, .36, 1);
    }

    /* Sembunyiin dulu biar nggak kedip sebelum GSAP jalan.
       Class .gsap-on ditambah lewat <script> di <head> layout. */
    .gsap-on .rv {
        opacity: 0;
    }

    /* Dipakai filter kategori: row yang baru muncul tetap kelihatan
       walau belum pernah kena ScrollTrigger */
    .gsap-on .rv.is-in {
        opacity: 1;
    }

    .gsap-on .mask>span {
        visibility: hidden;
    }

    /* Judul reveal per baris */
    .mask {
        display: block;
        overflow: hidden;
        padding-bottom: .1em;
    }

    .mask>span {
        display: block;
    }

    /* Kata raksasa outline di background */
    .bgword {
        position: absolute;
        top: 1rem;
        left: -2rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        line-height: .8;
        letter-spacing: -.05em;
        font-size: clamp(6rem, 19vw, 17rem);
        color: transparent;
        -webkit-text-stroke: 1.5px rgba(15, 98, 63, .09);
        white-space: nowrap;
        pointer-events: none;
        user-select: none;
        will-change: transform;
    }

    /* Parallax layer: lebih tinggi dari wadahnya supaya tidak ada celah */
    .plx {
        position: absolute;
        left: 0;
        right: 0;
        top: -8%;
        height: 116%;
        will-change: transform;
    }

    .plx img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1.1s var(--ease);
    }

    /* Link teks dengan garis bawah animasi */
    .link-line {
        position: relative;
        padding-bottom: 4px;
    }

    .link-line::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        height: 1.5px;
        width: 100%;
        background: currentColor;
        transform-origin: right;
        transform: scaleX(.35);
        transition: transform .6s var(--ease);
    }

    .link-line:hover::after {
        transform-origin: left;
        transform: scaleX(1);
    }

    /* Filter chip */
    #product-filters {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .fchip {
        flex: none;
        white-space: nowrap;
        border: 1px solid #DDE3DF;
        border-radius: 999px;
        background: #fff;
        padding: .5rem 1rem;
        font-size: .8125rem;
        font-weight: 600;
        color: #374151;
        transition: all .4s var(--ease);
    }

    .fchip:hover {
        border-color: #0F623F;
        color: #0F623F;
    }

    .fchip.is-active {
        background: #0F623F;
        border-color: #0F623F;
        color: #fff;
    }

    /* ===== Katalog produk (list + preview) ===== */
    .plist {
        list-style: none;
        margin: 0;
        padding: 0;
        counter-reset: prow;
        border-top: 1px solid #DDE3DF;
    }

    .prow {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        counter-increment: prow;
        display: grid;
        grid-template-columns: 76px 1fr;
        column-gap: 1rem;
        align-items: center;
        padding: 1rem .25rem;
        border-bottom: 1px solid #DDE3DF;
    }

    .prow[hidden] {
        display: none !important;
    }

    .prow.filter-in {
        animation: rowIn .6s var(--ease) var(--d, 0ms) backwards;
    }

    @keyframes rowIn {
        from {
            opacity: 0;
            transform: translateY(16px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    /* area klik seluruh baris */
    .prow-hit {
        position: absolute;
        inset: 0;
        z-index: 3;
        border: 0;
        background: none;
        cursor: pointer;
    }

    .prow-hit:focus-visible {
        outline: 2px solid #0F623F;
        outline-offset: -2px;
        border-radius: .5rem;
    }

    /* sapuan background */
    .prow::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        background: #F1F7F3;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .7s var(--ease);
    }

    .prow-num {
        display: none;
    }

    .prow-cat {
        display: none;
    }

    .prow-thumb {
        grid-row: 1 / span 2;
        width: 76px;
        height: 76px;
        overflow: hidden;
        border-radius: .75rem;
        background: #E7EFEA;
    }

    .prow-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .prow-main {
        grid-column: 2;
        transition: transform .7s var(--ease);
        min-width: 0;
    }

    .prow-title {
        font-size: 1rem;
        font-weight: 700;
        line-height: 1.3;
        color: #111827;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .prow-meta {
        margin-top: .2rem;
        font-size: .75rem;
        color: #6B7280;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .prow-price {
        grid-column: 2;
        margin-top: .4rem;
        font-size: .9375rem;
        font-weight: 700;
        color: #0F623F;
    }

    /* Desktop */
    @media (min-width: 1024px) {
        .prow {
            grid-template-columns: 3.25rem minmax(0, 1fr) 8.5rem auto;
            padding: 1.6rem 1rem;
        }

        .prow-thumb {
            display: none;
        }

        .prow-num {
            display: block;
            font-size: .8125rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: #9CA3AF;
            transition: color .4s ease;
        }

        .prow-num::before {
            content: counter(prow, decimal-leading-zero);
        }

        .prow-main {
            grid-column: auto;
        }

        .prow-title {
            font-size: 1.375rem;
            -webkit-line-clamp: 1;
        }

        .prow-meta {
            margin-top: .3rem;
            font-size: .8125rem;
        }

        .prow-cat-m {
            display: none;
        }

        .prow-cat {
            display: block;
            font-size: .8125rem;
            color: #6B7280;
        }

        .prow-price {
            grid-column: auto;
            margin: 0;
            font-size: 1.125rem;
            text-align: right;
        }

        .prow.is-current::before {
            transform: scaleX(1);
        }

        .prow.is-current .prow-num {
            color: #0F623F;
        }

        .prow.is-current .prow-main {
            transform: translateX(10px);
        }
    }

    /* Panel pratinjau */
    .pview-sticky {
        position: sticky;
        top: 7rem;
    }

    .pview-frame {
        position: relative;
        overflow: hidden;
        aspect-ratio: 4 / 5;
        border-radius: 1.5rem;
        background: #E7EFEA;
    }

    .pview-slide {
        position: absolute;
        inset: 0;
        z-index: 1;
        clip-path: inset(100% 0 0 0);
    }

    .pview-slide.is-prev {
        z-index: 2;
        clip-path: inset(0);
    }

    .pview-slide.is-active {
        z-index: 3;
        clip-path: inset(0);
        transition: clip-path .9s var(--ease);
    }

    .pview-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transform: scale(1.18);
        transition: transform 1.5s var(--ease);
    }

    .pview-slide.is-active img,
    .pview-slide.is-prev img {
        transform: scale(1);
    }

    .pview-chip {
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 5;
        padding: .4rem .85rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
        color: #0F623F;
        background: rgba(255, 255, 255, .94);
    }

    /* Mobile */
    @media (max-width: 767px) {
        .bgword {
            display: none;
        }

        /* Filter jadi satu baris yang bisa digeser */
        #product-filters {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-left: -1.25rem;
            margin-right: -1.25rem;
            padding: 0 1.25rem 4px;
            scrollbar-width: none;
        }

        #product-filters::-webkit-scrollbar {
            display: none;
        }
    }

    @media (max-width: 399px) {
        .pcard-more {
            display: none;
        }
    }

    /* Kartu UMKM
       Hover pakai `translate` (bukan `transform`) supaya nggak bentrok
       sama transform yang dipasang GSAP. */
    .ucard {
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        background: #fff;
        border: 1px solid #EEF0F2;
        transition: translate .6s var(--ease), box-shadow .6s var(--ease), border-color .4s ease;
    }

    .ucard:hover {
        translate: 0 -6px;
        border-color: rgba(15, 98, 63, .35);
        box-shadow: 0 30px 50px -28px rgba(15, 98, 63, .35);
    }

    .ucard::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        background: radial-gradient(380px circle at var(--mx, 50%) var(--my, 50%), rgba(15, 98, 63, .09), transparent 45%);
        opacity: 0;
        transition: opacity .4s ease;
    }

    .ucard:hover::before {
        opacity: 1;
    }

    .ucard:hover .plx img {
        transform: scale(1.07);
    }

    .ucard-body {
        position: relative;
        z-index: 2;
    }

    .ucard-arrow {
        display: flex;
        height: 2.5rem;
        width: 2.5rem;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        border: 1px solid #E5E7EB;
        color: #111827;
        transition: all .5s var(--ease);
    }

    .ucard:hover .ucard-arrow {
        background: #0F623F;
        border-color: #0F623F;
        color: #fff;
        transform: rotate(-45deg);
    }

    /* Fallback reduced motion: pastikan semua konten tetap kelihatan */
    @media (prefers-reduced-motion: reduce) {

        .gsap-on .rv {
            opacity: 1;
        }

        .gsap-on .mask>span {
            visibility: visible;
        }

        .prow.filter-in {
            animation: none;
        }
    }
</style>


{{-- ==================== 01 · HERO CAROUSEL ==================== --}}
<section id="home" class="relative h-screen min-h-[640px] w-full flex items-center overflow-hidden bg-gray-900 isolate">

    {{-- Slide 1: Sawah Indonesia --}}
    <div id="slide-bg-0" class="slide-bg active absolute inset-0 bg-cover bg-center opacity-100"
        style="background-image:url('https://images.unsplash.com/photo-1558005530-a7958896ec60?q=80&w=871&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D')"></div>

    {{-- Slide 2: Pemandangan desa Jawa --}}
    <div id="slide-bg-1" class="slide-bg absolute inset-0 bg-cover bg-center opacity-0"
        style="background-image:url('https://images.unsplash.com/photo-1609773036433-e941b471a6b7?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D')"></div>

    {{-- Slide 3: Pertanian lokal --}}
    <div id="slide-bg-2" class="slide-bg absolute inset-0 bg-cover bg-center opacity-0"
        style="background-image:url('https://images.unsplash.com/photo-1597129778410-0e4932adbd77?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D')"></div>

    {{-- Slide 4: Komunitas desa --}}
    <div id="slide-bg-3" class="slide-bg absolute inset-0 bg-cover bg-center opacity-0"
        style="background-image:url('https://plus.unsplash.com/premium_photo-1752140697548-999dfafe5b2b?q=80&w=871&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D')"></div>

    {{-- Slide 5: Produk pangan lokal --}}
    <div id="slide-bg-4" class="slide-bg absolute inset-0 bg-cover bg-center opacity-0"
        style="background-image:url('https://images.unsplash.com/photo-1696572777898-765c7f33a4d5?q=80&w=1031&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D')"></div>

    <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/60 to-black/10"></div>
    <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/50 to-transparent"></div>
    <div class="relative z-10 mx-auto flex h-full w-full max-w-7xl flex-col justify-center px-6 pb-20 pt-24 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">

            <h1 class="font-hero text-4xl font-semibold leading-[1.15] tracking-[-0.03em] text-white md:text-5xl lg:text-6xl">
                Kenali dan dukung<br>
                <span>UMKM lokal</span><br>
                bersama <span class="font-wordmark italic text-[#4CAF50] font-extrabold">Djajan.</span>
            </h1>

            <p class="mx-auto mt-5 max-w-lg text-base leading-relaxed text-white/75 md:text-lg">
                Temukan berbagai usaha dan produk lokal dari Desa Kreyongan Atas dalam satu platform. Kenali produknya, dukung usahanya, dan ikut menggerakkan ekonomi lokal.
            </p>

            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="#produk" class="group inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-medium text-[#022c22] shadow-md transition-all duration-300 hover:bg-emerald-50">
                    Lihat Produk
                    <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
                <a href="#umkm" class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-5 py-2.5 text-sm font-medium text-white backdrop-blur-md transition-all duration-300 hover:bg-white/15">
                    Jelajahi UMKM
                </a>
            </div>

        </div>
    </div>

</section>


{{-- ==================== 02 · MENGAPA DJAJAN ==================== --}}
<section id="tentang" class="px-6 lg:px-8 py-20 lg:py-28 bg-white scroll-reveal ">
    <div class="mx-auto max-w-7xl">

        <div class="flex flex-col lg:flex-row gap-10 lg:gap-14 items-stretch">

            {{-- KOLOM KIRI: Teks + CTA --}}
            <div class="lg:w-[42%] flex-shrink-0 flex flex-col justify-center reveal-item">

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
                <h2 class="font-display text-3xl md:text-4xl font-bold leading-[1.1] text-[#111827] tracking-tight mb-5 reveal-item">
                    Platform yang Menghubungkan
                    <span class="text-[#0F623F]">Desa</span>
                    dengan
                    <span class="text-[#0F623F]">Dunia.</span>
                </h2>

                {{-- Subtitle --}}
                <p class="text-ms md:text-base text-[#6B7280] leading-relaxed mb-8 max-w-sm reveal-item">
                    Tiga alasan mengapa Djajan menjadi pilihan tepat untuk mendukung UMKM lokal Desa Kreyongan Atas.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-wrap items-center gap-4 reveal-item">
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
<section id="about" class="relative px-6 lg:px-8 py-20 lg:py-28 pb-20 lg:pb-40 bg-[#FAFAF9] overflow-hidden isolate">

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
@php
$displayProducts = $products->take(8);
$kategoriList = $displayProducts->pluck('kategori')->filter()->unique()->values();
@endphp
<section id="produk" class="relative overflow-hidden bg-white px-5 py-16 sm:px-6 lg:px-8 lg:py-28">

    <span class="bgword" data-parallax-x="0.1" aria-hidden="true">Produk</span>

    <div class="relative mx-auto max-w-7xl">

        <div class="mb-8 flex flex-col gap-5 lg:mb-14 lg:flex-row lg:items-end lg:justify-between lg:gap-8">
            <div class="rv-group max-w-2xl">
                <p class="rv mb-4 text-xs font-bold uppercase tracking-[.2em] text-[#0F623F]">Produk Lokal</p>

                <h2 class="font-display text-3xl font-bold leading-[1.1] tracking-tight text-[#111827] sm:text-4xl lg:text-5xl">
                    <span class="mask"><span>Dari dapur desa,</span></span>
                    <span class="mask"><span style="--i:1">untuk <em class="not-italic text-[#0F623F]">meja Anda.</em></span></span>
                </h2>

                <p class="rv mt-4 max-w-xl text-sm leading-relaxed text-[#4B5563] sm:text-base lg:mt-5 lg:text-lg" style="--i:2">
                    Produk pilihan dari UMKM Desa Kreyongan Atas, dibuat dengan tangan dan ditanam dengan hati.
                </p>
            </div>

            <a href="#" class="rv link-line self-start text-sm font-semibold text-[#111827] hover:text-[#0F623F] lg:self-auto">
                Lihat semua produk
            </a>
        </div>

        {{-- Filter kategori --}}
        @if($kategoriList->count() > 1)
        <div class="rv mb-6 lg:mb-8" id="product-filters" role="tablist" aria-label="Filter kategori produk">
            <button type="button" class="fchip is-active" data-filter="all">Semua</button>
            @foreach($kategoriList as $kat)
            <button type="button" class="fchip" data-filter="{{ \Illuminate\Support\Str::slug($kat) }}">{{ $kat }}</button>
            @endforeach
        </div>
        @endif

        @if($displayProducts->isNotEmpty())
        <div class="lg:grid lg:grid-cols-12 lg:gap-14">

            {{-- Daftar produk --}}
            <ol id="product-list" class="plist lg:col-span-7">
                @foreach($displayProducts as $product)
                <li class="prow rv" style="--i: {{ $loop->index % 6 }}"
                    data-slide="{{ $loop->index }}"
                    data-kategori="{{ \Illuminate\Support\Str::slug($product->kategori ?: 'produk') }}">

                    <button type="button" data-detail-modal class="prow-hit"
                        data-modal-title="{{ $product->nama_produk }}"
                        data-modal-category="{{ $product->kategori ?: 'Produk' }}"
                        data-modal-description="{{ $product->deskripsi ?: 'Produk lokal dari UMKM Desa Kreyongan Atas.' }}"
                        data-modal-price="Rp {{ number_format($product->harga, 0, ',', '.') }}"
                        data-modal-label="Harga"
                        data-modal-image="{{ $product->gambar_produk ? $product->urlGambar() : '' }}">
                        <span class="sr-only">Lihat detail {{ $product->nama_produk }}</span>
                    </button>

                    <span class="prow-num" aria-hidden="true"></span>

                    <div class="prow-thumb">
                        @if($product->gambar_produk)
                        <img src="{{ $product->urlGambar() }}" alt="" loading="lazy">
                        @endif
                    </div>

                    <div class="prow-main">
                        <h3 class="prow-title font-display">{{ $product->nama_produk }}</h3>
                        <p class="prow-meta">
                            {{ $product->umkm?->nama_usaha ?? 'UMKM Lokal' }}<span class="prow-cat-m"> &middot; {{ $product->kategori ?: 'Produk' }}</span>
                        </p>
                    </div>

                    <p class="prow-cat">{{ $product->kategori ?: 'Produk' }}</p>
                    <p class="prow-price font-display">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>
                </li>
                @endforeach
            </ol>

            {{-- Panel pratinjau (desktop) --}}
            <aside class="hidden lg:col-span-5 lg:block" aria-hidden="true">
                <div class="pview-sticky">
                    <div class="pview-frame">
                        @foreach($displayProducts as $product)
                        <div class="pview-slide"
                            data-name="{{ $product->nama_produk }}"
                            data-umkm="{{ $product->umkm?->nama_usaha ?? 'UMKM Lokal' }}"
                            data-cat="{{ $product->kategori ?: 'Produk' }}">
                            @if($product->gambar_produk)
                            <img src="{{ $product->urlGambar() }}" alt="" loading="lazy">
                            @else
                            <div class="flex h-full w-full items-center justify-center text-sm text-[#8AA093]">Belum ada gambar</div>
                            @endif
                        </div>
                        @endforeach
                        <span class="pview-chip" id="pview-chip"></span>
                    </div>

                    <div class="mt-5 flex items-baseline justify-between gap-4 border-t border-[#DDE3DF] pt-4">
                        <p id="pview-name" class="font-display font-bold text-[#111827]"></p>
                        <p id="pview-umkm" class="text-sm text-[#6B7280]"></p>
                    </div>
                </div>
            </aside>
        </div>
        @else
        <div class="rounded-3xl border border-dashed border-gray-200 bg-[#FAFAF9] px-6 py-16 text-center">
            <h3 class="mb-2 font-display text-xl font-semibold text-[#111827]">Belum Ada Produk</h3>
            <p class="text-sm text-[#6B7280]">Produk UMKM Desa Kreyongan Atas akan ditampilkan di sini.</p>
        </div>
        @endif

    </div>
</section>


{{-- ==================== 05 · UMKM ==================== --}}
<section id="umkm" class="relative overflow-hidden bg-[#FAFAF9] px-6 py-20 lg:px-8 lg:py-28">

    <span class="bgword" data-parallax-x="0.1" aria-hidden="true" style="left:auto;right:-2rem">UMKM</span>

    <div class="relative mx-auto max-w-7xl">

        <div class="mb-12 flex flex-col gap-8 lg:mb-16 lg:flex-row lg:items-end lg:justify-between">
            <div class="rv-group max-w-2xl">
                <p class="rv mb-4 text-xs font-bold uppercase tracking-[.2em] text-[#0F623F]">Produk Lokal</p>
                <h2 class="font-display text-4xl font-bold leading-[1.08] tracking-tight text-[#111827] lg:text-5xl">
                    <span class="mask"><span>Wajah-wajah di balik</span></span>
                    <span class="mask"><span style="--i:1" class="text-[#0F623F]">UMKM Kreyongan.</span></span>
                </h2>

                <p class="rv mt-5 max-w-xl text-base leading-relaxed text-[#4B5563] lg:text-lg" style="--i:2">
                    Setiap usaha punya cerita. Kenali pelaku UMKM Desa Kreyongan Atas dan dukung perjalanan mereka.
                </p>
            </div>

            <a href="#" class="rv group inline-flex items-center gap-3 self-start text-sm font-semibold text-[#111827] transition-colors hover:text-[#0F623F] lg:self-auto">
                Lihat semua UMKM
                <span class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 transition-all duration-500 group-hover:border-[#0F623F] group-hover:bg-[#0F623F] group-hover:text-white">
                    <svg class="h-4 w-4 transition-transform duration-500 group-hover:-rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </span>
            </a>
        </div>

        {{-- Featured --}}
        @if($featuredUmkm)
        <article class="rv relative mb-8 overflow-hidden rounded-[2rem] bg-[#0B1512] text-white">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[#0F623F]/30 blur-3xl"></div>

            <div class="relative grid lg:grid-cols-12">
                <div class="relative aspect-[16/11] overflow-hidden lg:col-span-7 lg:aspect-auto lg:min-h-[500px]">
                    <div class="plx" data-parallax="0.09">
                        @if($featuredUmkm->foto_profil)
                        <img src="{{ $featuredUmkm->urlFotoProfil() }}" alt="{{ $featuredUmkm->nama_usaha }}" class="h-full w-full object-cover">
                        @else
                        <div class="flex h-full w-full items-center justify-center bg-[#12201B] text-sm text-white/40">Belum ada gambar</div>
                        @endif
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent lg:bg-gradient-to-r lg:from-transparent lg:via-transparent lg:to-[#0B1512]"></div>

                    <span class="absolute left-5 top-5 rounded-full bg-white px-4 py-1.5 text-xs font-semibold text-[#0F623F] shadow-lg">UMKM Unggulan</span>
                </div>

                <div class="relative flex flex-col justify-between p-8 lg:col-span-5 lg:p-12">
                    <div>
                        <p class="mb-4 text-[11px] font-bold uppercase tracking-[.18em] text-[#4CAF50]">
                            {{ $featuredUmkm->spesialis ?: 'UMKM Lokal' }}
                        </p>
                        <h3 class="mb-4 font-display text-3xl font-bold leading-tight tracking-tight lg:text-4xl">{{ $featuredUmkm->nama_usaha }}</h3>
                        <p class="line-clamp-4 leading-relaxed text-white/65">{{ $featuredUmkm->deskripsi_usaha ?: 'UMKM lokal Desa Kreyongan Atas.' }}</p>
                    </div>

                    <div class="mt-10">
                        <div class="mb-8 grid grid-cols-2 gap-6 border-t border-white/10 pt-8">
                            <div>
                                <p class="font-display text-4xl font-bold" data-count="{{ $featuredUmkm->produk_count }}">0</p>
                                <p class="mt-1 text-xs text-white/50">Produk</p>
                            </div>
                            @if($featuredUmkm->spesialis)
                            <div class="border-l border-white/10 pl-6">
                                <p class="line-clamp-2 font-display text-sm font-bold leading-snug">{{ $featuredUmkm->spesialis }}</p>
                                <p class="mt-1 text-xs text-white/50">Spesialisasi</p>
                            </div>
                            @endif
                        </div>

                        <button type="button" data-detail-modal
                            data-modal-title="{{ $featuredUmkm->nama_usaha }}"
                            data-modal-category="{{ $featuredUmkm->spesialis ?: 'UMKM Lokal' }}"
                            data-modal-description="{{ $featuredUmkm->deskripsi_usaha ?: 'UMKM lokal Desa Kreyongan Atas.' }}"
                            data-modal-price="{{ $featuredUmkm->produk_count }} Produk"
                            data-modal-label="Etalase"
                            data-modal-image="{{ $featuredUmkm->foto_profil ? $featuredUmkm->urlFotoProfil() : '' }}"
                            class="group/btn flex w-full items-center justify-between rounded-full bg-white py-2 pl-7 pr-2 text-sm font-semibold text-[#111827] transition-colors duration-500 hover:bg-[#E7F4EC]">
                            Kunjungi UMKM
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#0F623F] text-white transition-transform duration-500 group-hover/btn:-rotate-45">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </article>
        @endif

        {{-- Lainnya --}}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
            @php
            $otherUmkms = $umkms->filter(fn ($umkm) => !$featuredUmkm || $umkm->id_umkm !== $featuredUmkm->id_umkm);
            @endphp

            @forelse($otherUmkms as $umkm)
            <article class="ucard rv" style="--i: {{ $loop->index % 3 }}">
                <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
                    <div class="plx" data-parallax="0.07">
                        @if($umkm->foto_profil)
                        <img src="{{ $umkm->urlFotoProfil() }}" alt="{{ $umkm->nama_usaha }}" loading="lazy" class="h-full w-full object-cover">
                        @else
                        <div class="flex h-full w-full items-center justify-center bg-[#F3F4F6] text-sm text-gray-400">Belum ada gambar</div>
                        @endif
                    </div>

                    @if($umkm->status_verifikasi === 'terverifikasi')
                    <span class="absolute left-4 top-4 z-[2] inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-[#0F623F] shadow-sm">
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                        </svg>
                        Terverifikasi
                    </span>
                    @elseif($umkm->spesialis)
                    <span class="absolute left-4 top-4 z-[2] rounded-full bg-white/90 px-3 py-1.5 text-xs font-semibold text-[#374151] shadow-sm backdrop-blur">{{ $umkm->spesialis }}</span>
                    @endif
                </div>

                <div class="ucard-body p-6">
                    <h3 class="mb-2 font-display text-xl font-bold leading-snug text-[#111827]">{{ $umkm->nama_usaha }}</h3>
                    <p class="mb-5 line-clamp-2 text-sm leading-relaxed text-[#6B7280]">{{ $umkm->deskripsi_usaha ?: 'UMKM lokal Desa Kreyongan Atas.' }}</p>

                    <div class="flex items-center justify-between border-t border-gray-100 pt-5">
                        <div class="flex items-center gap-4 text-xs text-[#6B7280]">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                </svg>
                                Kreyongan Atas
                            </span>
                            <span><b class="font-semibold text-[#111827]" data-count="{{ $umkm->produk_count }}">0</b> Produk</span>
                        </div>

                        <button type="button" data-detail-modal class="pcard-hit ucard-link"
                            data-modal-title="{{ $umkm->nama_usaha }}"
                            data-modal-category="{{ $umkm->spesialis ?: 'UMKM Lokal' }}"
                            data-modal-description="{{ $umkm->deskripsi_usaha ?: 'UMKM lokal Desa Kreyongan Atas.' }}"
                            data-modal-price="{{ $umkm->produk_count }} Produk"
                            data-modal-label="Etalase"
                            data-modal-image="{{ $umkm->foto_profil ? $umkm->urlFotoProfil() : '' }}">
                            <span class="ucard-arrow">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                            <span class="sr-only">Lihat {{ $umkm->nama_usaha }}</span>
                        </button>
                    </div>
                </div>
            </article>
            @empty
            <div class="rounded-3xl border border-dashed border-gray-200 bg-white px-6 py-12 text-center md:col-span-2 lg:col-span-3">
                <h3 class="mb-2 font-display text-xl font-semibold text-[#111827]">Belum Ada UMKM Lain</h3>
                <p class="text-sm text-[#6B7280]">UMKM lainnya akan ditampilkan di sini.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>


{{-- ==================== 06 · CTA (DOWNLOAD APP) ==================== --}}
<section id="cta" class="relative py-24 lg:py-32 px-6 lg:px-8 overflow-hidden isolate">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
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
            window.djajanModalIn?.(); 
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
<script>
    (function() {
        /* Katalog produk: preview + filter */
        (function() {
            const list = document.getElementById('product-list');
            if (!list) return;

            const rows = [...list.querySelectorAll('.prow')];
            const slides = [...document.querySelectorAll('.pview-slide')];
            const chip = document.getElementById('pview-chip');
            const nameEl = document.getElementById('pview-name');
            const umkmEl = document.getElementById('pview-umkm');
            let current = -1;

            const setActive = (i) => {
                if (i === current || !slides[i]) return;
                slides.forEach(s => s.classList.remove('is-prev'));
                if (slides[current]) {
                    slides[current].classList.remove('is-active');
                    slides[current].classList.add('is-prev');
                }
                slides[i].classList.remove('is-prev');
                slides[i].classList.add('is-active');
                rows.forEach(r => r.classList.toggle('is-current', +r.dataset.slide === i));
                const d = slides[i].dataset;
                if (chip) chip.textContent = d.cat;
                if (nameEl) nameEl.textContent = d.name;
                if (umkmEl) umkmEl.textContent = d.umkm;
                current = i;
            };

            if (matchMedia('(min-width: 1024px)').matches) {
                rows.forEach(r => {
                    const i = +r.dataset.slide;
                    r.addEventListener('mouseenter', () => setActive(i));
                    r.addEventListener('focusin', () => setActive(i));
                });
            }
            setActive(0);

            const filters = document.getElementById('product-filters');
            if (!filters) return;
            filters.addEventListener('click', (ev) => {
                const btn = ev.target.closest('.fchip');
                if (!btn) return;
                filters.querySelectorAll('.fchip').forEach(b => b.classList.toggle('is-active', b === btn));

                const key = btn.dataset.filter;
                let n = 0,
                    first = null;
                rows.forEach(row => {
                    const show = key === 'all' || row.dataset.kategori === key;
                    row.hidden = !show;
                    if (!show) return;
                    row.classList.add('is-in');
                    row.classList.remove('filter-in');
                    void row.offsetWidth;
                    row.style.setProperty('--d', (n++ * 60) + 'ms');
                    row.classList.add('filter-in');
                    if (first === null) first = +row.dataset.slide;
                });
                if (first !== null) setActive(first);
                if (window.ScrollTrigger) ScrollTrigger.refresh();
            });
        })();

        /* Filter kategori grid */
        const filters = document.getElementById('product-filters');
        if (filters) {
            const cards = [...document.querySelectorAll('#product-grid .pcard')];
            if (cards.length) {
                filters.addEventListener('click', (ev) => {
                    const btn = ev.target.closest('.fchip');
                    if (!btn) return;
                    filters.querySelectorAll('.fchip').forEach(b => b.classList.toggle('is-active', b === btn));
                    const key = btn.dataset.filter;
                    let n = 0;
                    cards.forEach(card => {
                        const show = key === 'all' || card.dataset.kategori === key;
                        card.hidden = !show;
                        if (!show) return;
                        card.classList.add('is-in');
                        card.classList.remove('filter-in');
                        void card.offsetWidth;
                        card.style.setProperty('--d', (n++ * 60) + 'ms');
                        card.classList.add('filter-in');
                    });
                    if (window.ScrollTrigger) ScrollTrigger.refresh();
                });
            }
        }

        /* Glow kartu UMKM ngikutin kursor */
        document.querySelectorAll('.ucard').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const r = card.getBoundingClientRect();
                card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
                card.style.setProperty('--my', (e.clientY - r.top) + 'px');
            });
        });
    })();
</script>

<!-- GSAP Animations for Home -->
<script src="{{ asset('js/home-gsap.js') }}"></script>


@endpush