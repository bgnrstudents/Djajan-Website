/* =====================================================================
   Djajan - Buku Menu 3D
   Mengubah halaman-halaman yang sudah dirender Blade (data dari database
   lewat HomeController) menjadi buku interaktif dengan StPageFlip.
   Bila library / inisialisasi gagal -> daftar produk klasik ditampilkan
   kembali (class "mb-failed" pada <html>).
   ===================================================================== */
(function () {
    'use strict';

    window.djajanMenuBook = true;
    var html = document.documentElement;
    var stage = document.getElementById('menu-book-stage');
    var root = document.getElementById('menu-book');
    if (!stage || !root) return;

    function fail(reason) {
        if (window.console) console.warn('[menu-book] ' + reason);
        html.classList.add('mb-failed');
    }

    if (!window.St || !window.St.PageFlip) { fail('StPageFlip tidak termuat'); return; }

    try {
        var pages = Array.prototype.slice.call(root.querySelectorAll(':scope > .mb-page'));
        if (pages.length < 4) { fail('halaman buku tidak lengkap'); return; }

        var reduceMotion = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
        var finePointer = window.matchMedia && matchMedia('(hover: hover) and (pointer: fine)').matches;
        var total = pages.length;

        /* ---------- Nomor halaman, daftar isi & indeks menu ---------- */
        var firstOfChapter = {};
        var pageOfProduct = {};
        pages.forEach(function (p, i) {
            var no = p.querySelector('[data-mb-no]');
            if (no) no.textContent = String(i + 1);
            var ch = p.getAttribute('data-chapter');
            if (ch && !(ch in firstOfChapter)) firstOfChapter[ch] = i;
            var pid = p.getAttribute('data-product');
            if (pid) pageOfProduct[pid] = i;
        });
        stage.querySelectorAll('[data-goto-product]').forEach(function (el) {
            var idx = pageOfProduct[el.getAttribute('data-goto-product')];
            if (idx === undefined) { el.disabled = true; return; }
            el.setAttribute('data-goto', idx);
        });
        stage.querySelectorAll('[data-goto-chapter]').forEach(function (el) {
            var idx = firstOfChapter[el.getAttribute('data-goto-chapter')];
            if (idx === undefined) { el.hidden = true; return; }
            el.setAttribute('data-goto', idx);
            var label = el.querySelector('.mb-toc-page');
            if (label) label.textContent = idx + 1;
        });

        /* ---------- Preload gambar halaman sekitar ---------- */
        var preloaded = {};
        function preload(center) {
            for (var i = Math.max(0, center - 1); i <= Math.min(total - 1, center + 4); i++) {
                if (preloaded[i]) continue;
                preloaded[i] = true;
                var img = pages[i].querySelector('img[src]');
                if (img) { var im = new Image(); im.decoding = 'async'; im.src = img.getAttribute('src'); }
            }
        }

        /* ---------- Buat buku ---------- */
        var flip = new window.St.PageFlip(root, {
            width: 440,
            height: 620,
            size: 'stretch',
            minWidth: 280,
            maxWidth: 500,
            minHeight: 394,
            maxHeight: 704,
            showCover: true,
            maxShadowOpacity: 0.55,
            drawShadow: true,
            flippingTime: reduceMotion ? 250 : 950,
            usePortrait: true,
            startPage: 0,
            autoSize: true,
            mobileScrollSupport: true,
            swipeDistance: 28,
            clickEventForward: true
        });
        flip.loadFromHTML(pages);

        /* ---------- Elemen kontrol ---------- */
        var btnPrev = stage.querySelector('[data-mb-prev]');
        var btnNext = stage.querySelector('[data-mb-next]');
        var btnOpen = stage.querySelector('[data-mb-open]');
        var statusEl = stage.querySelector('[data-mb-status]');
        var umkmEl = stage.querySelector('[data-mb-umkm]');

        function isLandscape() { return flip.getOrientation() === 'landscape'; }

        function statusText(i) {
            if (i <= 0) return 'Sampul depan';
            if (i >= total - 1) return 'Sampul belakang';
            if (isLandscape()) {
                var right = Math.min(i % 2 === 1 ? i + 1 : i, total - 2);
                var left = right - 1;
                return 'Halaman ' + (left + 1) + '\u2013' + (right + 1) + ' dari ' + total;
            }
            return 'Halaman ' + (i + 1) + ' dari ' + total;
        }

        // Nama UMKM pada halaman yang sedang dibaca (halaman kanan bila buku terbuka lebar)
        function umkmAt(i) {
            var idx = isLandscape() ? Math.min(i % 2 === 1 ? i + 1 : i, total - 2) : i;
            var el = pages[idx];
            return (el && el.getAttribute('data-umkm-name')) || (pages[i] && pages[i].getAttribute('data-umkm-name')) || '';
        }

        function applyState(i) {
            var land = isLandscape();
            stage.classList.toggle('is-portrait', !land);
            stage.classList.toggle('is-front-closed', land && i <= 0);
            stage.classList.toggle('is-back-closed', land && i >= total - 1);
            stage.classList.toggle('is-open', i > 0 && i < total - 1);
            if (statusEl) statusEl.textContent = statusText(i);
            if (umkmEl) umkmEl.textContent = umkmAt(i);
            if (btnPrev) btnPrev.disabled = i <= 0;
            if (btnNext) btnNext.disabled = i >= total - 1;
            if (btnOpen) btnOpen.hidden = i > 0;
            preload(i);
        }

        flip.on('init', function () { stage.classList.add('is-ready'); html.classList.add('mb-ready'); applyState(flip.getCurrentPageIndex()); });
        flip.on('flip', function (e) { applyState(e.data); });
        flip.on('changeOrientation', function () { applyState(flip.getCurrentPageIndex()); });
        flip.on('changeState', function (e) {
            // Buku bergeser ke tengah begitu sampul mulai dibuka / ditutup.
            if (e.data !== 'flipping' || !isLandscape()) return;
            var i = flip.getCurrentPageIndex();
            if (i <= 0 || i >= total - 1) {
                stage.classList.remove('is-front-closed', 'is-back-closed');
                stage.classList.add('is-open');
            }
        });

        // Cadangan: bila event 'init' tidak sempat tertangkap
        setTimeout(function () {
            if (!stage.classList.contains('is-ready')) { stage.classList.add('is-ready'); html.classList.add('mb-ready'); applyState(flip.getCurrentPageIndex()); }
        }, 600);

        /* ---------- Tombol & navigasi ---------- */
        function next() { flip.flipNext(); }
        function prev() { flip.flipPrev(); }
        if (btnNext) btnNext.addEventListener('click', next);
        if (btnPrev) btnPrev.addEventListener('click', prev);
        if (btnOpen) btnOpen.addEventListener('click', next);

        function goTo(idx) {
            idx = parseInt(idx, 10);
            if (isNaN(idx)) return;
            if (idx === flip.getCurrentPageIndex()) return;
            flip.flip(idx);
        }
        stage.addEventListener('click', function (e) {
            var t = e.target.closest ? e.target.closest('[data-goto]') : null;
            if (!t) return;
            e.preventDefault();
            goTo(t.getAttribute('data-goto'));
            // fokus kembali ke buku supaya tombol panah tetap bekerja setelah halaman berpindah
            try { stage.focus({ preventScroll: true }); } catch (err) { stage.focus(); }
        });

        stage.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowRight') { e.preventDefault(); next(); }
            else if (e.key === 'ArrowLeft') { e.preventDefault(); prev(); }
            else if (e.key === 'Home') { e.preventDefault(); goTo(0); }
            else if (e.key === 'End') { e.preventDefault(); goTo(total - 1); }
        });

        /* ---------- Muncul saat terlihat di layar ---------- */
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) {
                    if (en.isIntersecting) { stage.classList.add('is-in-view'); io.disconnect(); }
                });
            }, { threshold: 0.12 });
            io.observe(stage);
        } else {
            stage.classList.add('is-in-view');
        }

        /* ---------- Efek miring 3D mengikuti kursor (desktop) ---------- */
        var tilt = stage.querySelector('.mb-tilt');
        if (tilt && finePointer && !reduceMotion) {
            var raf = 0, tx = 0, ty = 0;
            var scene = stage.querySelector('.mb-scene');
            scene.addEventListener('mousemove', function (e) {
                var r = scene.getBoundingClientRect();
                tx = ((e.clientX - r.left) / r.width - 0.5) * 2;
                ty = ((e.clientY - r.top) / r.height - 0.5) * 2;
                if (raf) return;
                raf = requestAnimationFrame(function () {
                    raf = 0;
                    tilt.style.setProperty('--mb-ry', (tx * 5).toFixed(2) + 'deg');
                    tilt.style.setProperty('--mb-rx', (-ty * 3.5).toFixed(2) + 'deg');
                });
            });
            scene.addEventListener('mouseleave', function () {
                tilt.style.setProperty('--mb-ry', '0deg');
                tilt.style.setProperty('--mb-rx', '0deg');
            });
        }

        applyState(0);
    } catch (err) {
        fail(err && err.message ? err.message : String(err));
    }
})();
