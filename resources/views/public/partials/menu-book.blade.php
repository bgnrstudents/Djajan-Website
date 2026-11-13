{{--
    BUKU MENU 3D — katalog produk Djajan, disusun per UMKM.
    Data: $products (Eloquent, sudah difilter di HomeController: produk tersedia,
    milik UMKM terverifikasi & aktif) beserta relasi ->umkm. Tidak ada data dummy.
    Library: StPageFlip (public/js/vendor/page-flip.browser.js). Logika: public/js/menu-book.js.

    Susunan halaman (dinamis, mengikuti data):
      0 sampul | 1 selamat datang | 2 daftar isi
      per UMKM : halaman identitas (kiri) + 1 halaman per produk (+ halaman penutup bila perlu genap)
      penutup  : 2 halaman | sampul belakang
    Setiap UMKM dimulai di halaman kiri, sehingga identitas & produk pertamanya selalu satu spread.
--}}
@php
    // 1) Kelompokkan produk per UMKM; UMKM dengan menu terbanyak tampil lebih dulu.
    $umkmGroups = $products->groupBy('id_umkm')->sort(function ($a, $b) {
        return [$b->count(), strtolower((string) ($a->first()->umkm?->nama_usaha ?? ''))]
            <=> [$a->count(), strtolower((string) ($b->first()->umkm?->nama_usaha ?? ''))];
    })->values();

    // 2) Batasi total ±80 produk agar buku tetap ringan (UMKM utuh, tidak dipotong di tengah).
    $bookLimit = 80;
    $picked = collect();
    $sum = 0;
    foreach ($umkmGroups as $g) {
        if ($picked->isNotEmpty() && $sum + $g->count() > $bookLimit) {
            continue;
        }
        $picked->push($g->take($bookLimit));
        $sum += min($g->count(), $bookLimit);
    }

    // 3) Data tiap bab UMKM (semua dari relasi Eloquent).
    $bookChapters = $picked->values()->map(function ($grup, $i) {
        $u    = $grup->first()->umkm;
        $nama = trim((string) ($u?->nama_usaha ?: 'UMKM Lokal'));
        return [
            'no'      => $i + 1,
            'id'      => $u?->id_umkm ?? ('x' . $i),
            'slug'    => 'u' . ($u?->id_umkm ?? ('x' . $i)),
            'nama'    => $nama,
            'ini'     => collect(preg_split('/\s+/u', $nama))->filter()->take(2)
                            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'D',
            'foto'    => ($u && $u->foto_profil) ? $u->urlFotoProfil() : null,
            'desc'    => trim((string) ($u?->deskripsi_usaha ?? '')),
            'spes'    => trim((string) ($u?->spesialis ?? '')),
            'produk'  => $grup->values(),
            'jumlah'  => $grup->count(),
            'kategori'=> $grup->pluck('kategori')->filter()->unique()->values(),
            // identitas (1 halaman) + produk; bila ganjil tambah 1 halaman penutup agar bab berakhir di kanan
            'butuhPenutup' => (1 + $grup->count()) % 2 === 1,
        ];
    });
    $totalUmkm   = $bookChapters->count();
    $totalProduk = $bookChapters->sum('jumlah');
@endphp

<div id="menu-book-stage" class="mb-stage" tabindex="0" role="region" aria-roledescription="buku menu"
    aria-label="Buku menu produk Djajan per UMKM. Gunakan tombol panah kiri dan kanan untuk membalik halaman.">

    <div class="mb-table">

        {{-- ===== Latar: meja marmer putih + cahaya jendela (dekoratif) ===== --}}
        <div class="mb-room" aria-hidden="true">
            <div class="mb-marble"></div>
            <div class="mb-window"></div>

            {{-- Ranting hijau kecil (kiri bawah) --}}
            <svg class="mb-prop mb-prop--sprig" viewBox="0 0 340 250">
                <defs><filter id="mbSoft" x="-30%" y="-30%" width="160%" height="160%"><feGaussianBlur stdDeviation="7"/></filter></defs>
                <g id="mbSprigShape">
                    <path d="M18 238 C90 200 170 150 318 34" fill="none" stroke="#4d7d62" stroke-width="3.2" stroke-linecap="round"/>
                    <g fill="#5f9378">
                        <ellipse cx="62" cy="196" rx="25" ry="10" transform="rotate(-48 62 196)"/>
                        <ellipse cx="86" cy="206" rx="25" ry="10" transform="rotate(8 86 206)" fill="#7aa88e"/>
                        <ellipse cx="116" cy="170" rx="26" ry="10" transform="rotate(-52 116 170)"/>
                        <ellipse cx="142" cy="182" rx="26" ry="10" transform="rotate(4 142 182)" fill="#7aa88e"/>
                        <ellipse cx="172" cy="140" rx="26" ry="10" transform="rotate(-55 172 140)"/>
                        <ellipse cx="196" cy="152" rx="26" ry="10" transform="rotate(-2 196 152)" fill="#7aa88e"/>
                        <ellipse cx="226" cy="106" rx="24" ry="9.5" transform="rotate(-58 226 106)"/>
                        <ellipse cx="250" cy="118" rx="24" ry="9.5" transform="rotate(-8 250 118)" fill="#7aa88e"/>
                        <ellipse cx="280" cy="68" rx="22" ry="9" transform="rotate(-60 280 68)"/>
                        <ellipse cx="302" cy="78" rx="21" ry="9" transform="rotate(-14 302 78)" fill="#7aa88e"/>
                        <ellipse cx="316" cy="30" rx="18" ry="8" transform="rotate(-64 316 30)"/>
                    </g>
                </g>
                <use href="#mbSprigShape" fill="#1f3a2b" opacity=".16" filter="url(#mbSoft)" transform="translate(14 22)" style="mix-blend-mode:multiply"/>
            </svg>

            {{-- Cangkir putih + teh hijau (kanan bawah) --}}
            <svg class="mb-prop mb-prop--cup" viewBox="0 0 230 210">
                <defs>
                    <radialGradient id="mbSau" cx=".4" cy=".35" r=".85"><stop offset="0" stop-color="#ffffff"/><stop offset="1" stop-color="#e3e7e2"/></radialGradient>
                    <radialGradient id="mbCup" cx=".38" cy=".3" r=".9"><stop offset="0" stop-color="#ffffff"/><stop offset="1" stop-color="#dfe4de"/></radialGradient>
                    <radialGradient id="mbTea" cx=".4" cy=".35" r=".8"><stop offset="0" stop-color="#cfe0b5"/><stop offset="1" stop-color="#93ad74"/></radialGradient>
                </defs>
                <ellipse cx="118" cy="128" rx="98" ry="92" fill="#243b2e" opacity=".22" filter="url(#mbSoft)"/>
                <circle cx="108" cy="108" r="92" fill="url(#mbSau)"/>
                <circle cx="108" cy="108" r="78" fill="none" stroke="#d2d8d0" stroke-width="2"/>
                <path d="M162 98 C206 84 214 134 168 132" fill="none" stroke="#f3f5f2" stroke-width="13" stroke-linecap="round"/>
                <path d="M162 98 C206 84 214 134 168 132" fill="none" stroke="#c9d0c8" stroke-width="2" stroke-linecap="round" opacity=".8"/>
                <circle cx="108" cy="108" r="58" fill="url(#mbCup)"/>
                <circle cx="108" cy="108" r="49" fill="url(#mbTea)"/>
                <ellipse cx="94" cy="92" rx="22" ry="10" fill="#fff" opacity=".35" transform="rotate(-28 94 92)"/>
                <g transform="rotate(-24 108 108)"><rect x="24" y="105" width="102" height="5" rx="2.5" fill="#d7dcd8"/><ellipse cx="134" cy="107.5" rx="14" ry="8" fill="#e8ecea"/></g>
            </svg>
        </div>

        <div class="mb-scene">
            <div class="mb-loading" role="status">
                <span class="mb-spinner" aria-hidden="true"></span>
                <span>Menyiapkan buku menu&hellip;</span>
            </div>

            <div class="mb-tilt">
                <div class="mb-shift">
                    <span class="mb-under" aria-hidden="true"></span>
                    <div id="menu-book">

                        {{-- 0 · Sampul depan --}}
                        <div class="mb-page mb-cover" data-density="hard">
                            <div class="mb-cover-in">
                                <div class="mb-badge">
                                    <img src="{{ asset('logo_djajan_mark.png') }}" alt="" width="512" height="492" decoding="async">
                                </div>
                                <p class="mb-kicker">Desa Kreyongan Atas</p>
                                <h3 class="mb-title">Buku Menu</h3>
                                <span class="mb-orn" aria-hidden="true"></span>
                                <p class="mb-sub">Makanan &amp; minuman pilihan UMKM lokal, langsung dari dapur desa.</p>
                                <p class="mb-open-hint">Klik untuk membuka</p>
                            </div>
                        </div>

                        {{-- 1 · Selamat datang --}}
                        <div class="mb-page">
                            <div class="mb-paper mb-center">
                                {{-- <div class="mb-welcome-gallery" role="img" aria-label="Foto aktivitas UMKM lokal di Malang dan Bali, berganti otomatis">
                                    <img src="https://images.unsplash.com/photo-1667380385292-ad611bbe2296?auto=format&amp;fit=crop&amp;w=900&amp;q=85" alt="" aria-hidden="true" decoding="async">
                                    <img src="https://images.unsplash.com/photo-1712129462553-83e362ea5788?auto=format&amp;fit=crop&amp;w=900&amp;q=85" alt="" aria-hidden="true" decoding="async">
                                    <img src="https://images.unsplash.com/photo-1582247371634-cc060c909a78?auto=format&amp;fit=crop&amp;w=900&amp;q=85" alt="" aria-hidden="true" decoding="async">
                                </div>
                                <p class="mb-kicker mb-kicker--ink">Selamat datang</p> --}}
                                <h3 class="mb-h2" style="margin:0">Dapur desa, dalam satu buku</h3>
                                <span class="mb-orn mb-orn--paper" aria-hidden="true"></span>
                                <p class="mb-lead">Setiap bagian dibuka dengan perkenalan penjualnya, lalu diikuti menu yang mereka buat dengan tangan sendiri.</p>
                                <div class="mb-stats">
                                    <div><b>{{ $totalUmkm }}</b><span>UMKM</span></div>
                                    <div><b>{{ $totalProduk }}</b><span>Menu</span></div>
                                </div>
                            </div>
                        </div>

                        {{-- 2 · Daftar isi (per UMKM) --}}
                        <div class="mb-page">
                            <div class="mb-paper">
                                <div class="mb-head"><span class="mb-cat">Djajan</span></div>
                                <h3 class="mb-h2">Daftar Isi</h3>
                                <p class="mb-lead">Pilih UMKM untuk langsung menuju halamannya.</p>
                                <ul class="mb-toc">
                                    @foreach($bookChapters->take(12) as $ch)
                                    <li>
                                        <button type="button" data-goto-chapter="{{ $ch['slug'] }}">
                                            <span class="mb-mini">{{ $ch['ini'] }}@if($ch['foto'])<img src="{{ $ch['foto'] }}" alt="" loading="lazy" decoding="async" onerror="this.remove()">@endif</span>
                                            <span class="mb-toc-name">{{ $ch['nama'] }}</span>
                                            <span class="mb-toc-dots"></span>
                                            <span class="mb-count">{{ $ch['jumlah'] }} menu</span>
                                            <span class="mb-toc-page"></span>
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                                <span class="mb-folio" data-mb-no></span>
                            </div>
                        </div>

                        {{-- Bab per UMKM --}}
                        @foreach($bookChapters as $ch)

                        {{-- Halaman identitas UMKM (kiri) --}}
                        <div class="mb-page" data-chapter="{{ $ch['slug'] }}" data-umkm-name="{{ $ch['nama'] }}">
                            <div class="mb-paper">
                                <div class="mb-head"><span class="mb-cat">Penjual {{ $ch['no'] }} dari {{ $totalUmkm }}</span></div>

                                <div class="mb-ident">
                                    <div class="mb-avatar"><span class="mb-avatar-ini">{{ $ch['ini'] }}</span>@if($ch['foto'])<img src="{{ $ch['foto'] }}" alt="Foto {{ $ch['nama'] }}" decoding="async" onerror="this.remove()">@endif</div>
                                    <div class="mb-ident-text">
                                        <p class="mb-ident-kicker">{{ $ch['spes'] ?: 'UMKM Desa Kreyongan Atas' }}</p>
                                        <h3 class="mb-ident-title">{{ $ch['nama'] }}</h3>
                                    </div>
                                </div>

                                <p class="mb-desc mb-desc--ident">{{ $ch['desc'] ?: 'UMKM lokal Desa Kreyongan Atas.' }}</p>

                                <ul class="mb-tags">
                                    <li>{{ $ch['jumlah'] }} menu</li>
                                    @foreach($ch['kategori']->take(2) as $kat)<li>{{ $kat }}</li>@endforeach
                                </ul>

                                <h4 class="mb-index-title">Menu dari dapur ini</h4>
                                <ol class="mb-index">
                                    @foreach($ch['produk']->take(6) as $ip)
                                    <li>
                                        <button type="button" data-goto-product="{{ $ip->id_produk }}">
                                            <span class="mb-index-name">{{ $ip->nama_produk }}</span>
                                            <span class="mb-toc-dots"></span>
                                            <span class="mb-index-price">Rp {{ number_format($ip->harga, 0, ',', '.') }}</span>
                                        </button>
                                    </li>
                                    @endforeach
                                </ol>
                                @if($ch['jumlah'] > 6)<p class="mb-more">+ {{ $ch['jumlah'] - 6 }} menu lainnya di halaman berikut</p>@endif
                                <span class="mb-folio" data-mb-no></span>
                            </div>
                        </div>

                        {{-- Satu halaman per produk milik UMKM ini --}}
                        @foreach($ch['produk'] as $p)
                        @php
                            $kat   = $p->kategori ?: 'Produk';
                            $mode  = $p->mode_penjualan;
                            $label = match ($mode) { 'ready' => 'Ready', 'pre_order' => 'Pre-Order', 'keduanya' => 'Ready & Pre-Order', default => null };
                            $harga = 'Rp ' . number_format($p->harga, 0, ',', '.');
                            $foto  = $p->gambar_produk ? $p->urlGambar() : null;
                        @endphp
                        <div class="mb-page" data-chapter="{{ $ch['slug'] }}" data-umkm-name="{{ $ch['nama'] }}" data-product="{{ $p->id_produk }}">
                            <div class="mb-paper">
                                <div class="mb-head">
                                    <span class="mb-who">
                                        <span class="mb-mini">{{ $ch['ini'] }}@if($ch['foto'])<img src="{{ $ch['foto'] }}" alt="" loading="lazy" decoding="async" onerror="this.remove()">@endif</span>
                                        <span class="mb-who-name">{{ $ch['nama'] }}</span>
                                    </span>
                                    <span class="mb-cat">{{ $kat }}</span>
                                </div>

                                <div class="mb-photo {{ $foto ? '' : 'is-broken' }}">
                                    @if($foto)
                                    <img src="{{ $foto }}" alt="{{ $p->nama_produk }}" loading="lazy" decoding="async"
                                        onload="this.classList.add('is-loaded');this.parentNode.classList.add('is-loaded')"
                                        onerror="this.parentNode.classList.add('is-broken')">
                                    @endif
                                    <div class="mb-photo-fallback">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11h18M5 11a7 7 0 0 1 14 0M4 15h16M6 19h12"/></svg>
                                        <span>Foto belum tersedia</span>
                                    </div>
                                </div>

                                <div class="mb-line">
                                    <h4 class="mb-name">{{ $p->nama_produk }}</h4>
                                    <span class="mb-toc-dots"></span>
                                    <span class="mb-price">{{ $harga }}</span>
                                </div>
                                <p class="mb-desc">{{ $p->deskripsi ?: 'Produk lokal dari UMKM Desa Kreyongan Atas.' }}</p>

                                <ul class="mb-tags">
                                    @if($label)<li class="{{ $mode === 'pre_order' ? 'is-po' : '' }}">{{ $label }}</li>@endif
                                    @if($mode !== 'pre_order' && (int) $p->stok > 0)<li>Stok {{ (int) $p->stok }}</li>@endif
                                    @if($mode !== 'ready' && (int) $p->min_order_po > 0)<li class="is-po">Min. PO {{ (int) $p->min_order_po }}</li>@endif
                                    @if($mode !== 'ready' && filled($p->waktu_masak_po))<li class="is-po">{{ $p->waktu_masak_po }}</li>@endif
                                </ul>

                                <div class="mb-foot">
                                    <button type="button" class="mb-detail" data-detail-modal
                                        data-modal-title="{{ $p->nama_produk }}"
                                        data-modal-category="{{ $kat }}"
                                        data-modal-description="{{ $p->deskripsi ?: 'Produk lokal dari UMKM Desa Kreyongan Atas.' }}"
                                        data-modal-price="{{ $harga }}"
                                        data-modal-label="Harga"
                                        data-modal-image="{{ $foto ?? '' }}">Lihat detail</button>
                                </div>
                                <span class="mb-folio" data-mb-no></span>
                            </div>
                        </div>
                        @endforeach

                        {{-- Halaman penutup bab (hanya bila jumlah halaman bab ganjil) --}}
                        @if($ch['butuhPenutup'])
                        <div class="mb-page" data-chapter="{{ $ch['slug'] }}" data-umkm-name="{{ $ch['nama'] }}">
                            <div class="mb-paper mb-center">
                                <div class="mb-avatar mb-avatar--sm"><span class="mb-avatar-ini">{{ $ch['ini'] }}</span>@if($ch['foto'])<img src="{{ $ch['foto'] }}" alt="" decoding="async" onerror="this.remove()">@endif</div>
                                <span class="mb-orn mb-orn--paper" aria-hidden="true"></span>
                                <p class="mb-blank-note">Terima kasih telah mampir di</p>
                                <h3 class="mb-h2" style="margin:0">{{ $ch['nama'] }}</h3>
                                <p class="mb-lead">{{ $ch['jumlah'] }} menu buatan tangan, siap dipesan lewat aplikasi Djajan.</p>
                            </div>
                        </div>
                        @endif

                        @endforeach

                        {{-- Penutup: 2 halaman --}}
                        <div class="mb-page">
                            <div class="mb-paper mb-center">
                                <span class="mb-orn mb-orn--paper" aria-hidden="true"></span>
                                <h3 class="mb-h2" style="margin:0">Selamat menikmati</h3>
                                <p class="mb-lead">Terima kasih telah mengenal {{ $totalUmkm }} UMKM dan {{ $totalProduk }} menu dari Desa Kreyongan Atas.</p>
                            </div>
                        </div>
                        <div class="mb-page">
                            <div class="mb-paper mb-center">
                                <h3 class="mb-h2" style="margin:0">Pesan lewat aplikasi</h3>
                                <p class="mb-lead">Pesan menu favorit Anda melalui aplikasi Djajan, atau kenali lebih dekat para pembuatnya.</p>
                                <a class="mb-link" href="#umkm">Jelajahi UMKM</a>
                            </div>
                        </div>

                        {{-- Sampul belakang --}}
                        <div class="mb-page mb-back" data-density="hard">
                            <div class="mb-back-in">
                                <div class="mb-badge" style="width:18cqw"><img src="{{ asset('logo_djajan_mark.png') }}" alt="" width="512" height="492" decoding="async"></div>
                                <span class="mb-orn" aria-hidden="true"></span>
                                <p class="mb-sub">Dari dapur desa, untuk meja Anda.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="mb-controls">
            <button type="button" class="mb-btn" data-mb-prev aria-label="Halaman sebelumnya">&larr; Sebelumnya</button>
            <span class="mb-status"><b class="mb-status-umkm" data-mb-umkm></b><span data-mb-status aria-live="polite">Sampul depan</span></span>
            <button type="button" class="mb-btn mb-btn--primary" data-mb-open>Buka buku</button>
            <button type="button" class="mb-btn" data-mb-next aria-label="Halaman berikutnya">Berikutnya &rarr;</button>
        </div>
        <p class="mb-hint">Klik sudut halaman atau geser untuk membalik &middot; tombol panah keyboard juga bisa dipakai</p>
    </div>
</div>
