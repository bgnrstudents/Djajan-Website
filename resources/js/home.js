import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ScrollToPlugin } from 'gsap/ScrollToPlugin';

gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

const EASE = 'power3.out';

function initHome() {
  if (!document.getElementById('home')) return;
  if (window.__homeGsapInitialized) return;
  window.__homeGsapInitialized = true;

  const mm = gsap.matchMedia();

  mm.add(
    {
      reduce: '(prefers-reduced-motion: reduce)',
      ok: '(prefers-reduced-motion: no-preference)',
      desktop: '(min-width: 768px)',
    },
    (ctx) => {
      const { reduce, desktop } = ctx.conditions;

      /* ---------- Count-up (selalu jalan, versi reduce = langsung final) ---------- */
      gsap.utils.toArray('[data-count]').forEach((el) => {
        const end = parseInt(el.dataset.count, 10) || 0;
        if (reduce || end === 0) { el.textContent = end; return; }
        const o = { v: 0 };
        ScrollTrigger.create({
          trigger: el,
          start: 'top 92%',
          once: true,
          onEnter: () =>
            gsap.to(o, {
              v: end, duration: 1.6, ease: 'power2.out',
              onUpdate: () => (el.textContent = Math.round(o.v)),
            }),
        });
      });

      /* ---------- Reduced motion: tampilkan semuanya, selesai ---------- */
      if (reduce) {
        gsap.set('.rv', { opacity: 1 });
        gsap.set('.mask > span', { autoAlpha: 1 });
        return;
      }

      /* =====================================================
         01 · HERO
      ===================================================== */
      const slides = gsap.utils.toArray('#home .slide-bg');
      let idx = 0;

      const kenBurns = (el) =>
        gsap.fromTo(el, { scale: 1.12 }, { scale: 1.02, duration: 7, ease: 'none' });

      gsap.set(slides, { autoAlpha: 0, zIndex: -2, scale: 1.12 });
      gsap.set(slides[0], { autoAlpha: 1, zIndex: -1 });
      kenBurns(slides[0]);

      const nextSlide = () => {
        const cur = slides[idx];
        idx = (idx + 1) % slides.length;
        const nxt = slides[idx];

        gsap.set(cur, { zIndex: -1 });
        gsap.set(nxt, { autoAlpha: 1, zIndex: -2 });
        kenBurns(nxt);
        gsap.to(cur, {
          autoAlpha: 0, duration: 1.6, ease: 'power2.inOut',
          onComplete: () => gsap.set(cur, { zIndex: -2 }),
        });
      };
      const loop = () => { nextSlide(); gsap.delayedCall(5, loop); };
      gsap.delayedCall(5, loop);

      gsap.timeline({ defaults: { ease: 'power4.out' } })
        .from('#home h1', { y: 40, opacity: 0, filter: 'blur(8px)', duration: 1.2, clearProps: 'filter,transform,opacity' })
        .from('#home p', { y: 24, opacity: 0, duration: 0.9, clearProps: 'transform,opacity' }, '-=0.75')
        .from('#home a', { y: 20, opacity: 0, duration: 0.8, stagger: 0.12, clearProps: 'transform,opacity' }, '-=0.6');

      gsap.to('#home > .z-10', {
        yPercent: -10, opacity: 0.25, ease: 'none',
        scrollTrigger: { trigger: '#home', start: 'top top', end: 'bottom top', scrub: true },
      });

      /* =====================================================
         GENERIC · .rv (fade-up berurutan per batch)
      ===================================================== */
      ScrollTrigger.batch('.rv', {
        start: 'top 90%',
        once: true,
        onEnter: (els) =>
          gsap.fromTo(
            els,
            { y: 40, opacity: 0 },
            { y: 0, opacity: 1, duration: 1, ease: EASE, stagger: 0.08, clearProps: 'transform' }
          ),
      });

      /* Judul mask-reveal per baris */
      gsap.utils.toArray('.rv-group').forEach((group) => {
        const spans = group.querySelectorAll('.mask > span');
        if (!spans.length) return;
        gsap.fromTo(
          spans,
          { yPercent: 110, autoAlpha: 0 },
          {
            yPercent: 0, autoAlpha: 1, duration: 1.1, ease: 'power4.out', stagger: 0.12,
            scrollTrigger: { trigger: group, start: 'top 85%', once: true },
          }
        );
      });

      /* =====================================================
         02 · MENGAPA DJAJAN
      ===================================================== */
      const leftCol = gsap.utils.toArray('#tentang .flex-col.justify-center > *');
      if (leftCol.length) {
        gsap.from(leftCol, {
          y: 36, opacity: 0, duration: 1, ease: EASE, stagger: 0.1, clearProps: 'transform,opacity',
          scrollTrigger: { trigger: '#tentang', start: 'top 70%', once: true },
        });
      }

      const whyCards = gsap.utils.toArray('#tentang .group');
      if (whyCards.length) {
        const tl = gsap.timeline({
          scrollTrigger: { trigger: whyCards[0].parentElement, start: 'top 80%', once: true },
        });
        tl.from(whyCards, {
          y: 70, opacity: 0, scale: 0.96, duration: 1.1, ease: EASE, stagger: 0.15, clearProps: 'transform,opacity',
        });
        whyCards.forEach((card, i) => {
          tl.from(card.querySelectorAll('.p-5 > *'), {
            y: 16, opacity: 0, duration: 0.7, ease: EASE, stagger: 0.07, clearProps: 'transform,opacity',
          }, 0.35 + i * 0.15);
        });
      }

      /* =====================================================
         03 · TENTANG (butuh id="about" di section)
      ===================================================== */
      const photo = document.querySelector('#about [style*="aspect-ratio"]');
      if (photo) {
        gsap.fromTo(
          photo,
          { clipPath: 'inset(10% 6% 10% 6% round 24px)' },
          {
            clipPath: 'inset(0% 0% 0% 0% round 16px)', ease: 'none',
            scrollTrigger: { trigger: photo, start: 'top 90%', end: 'top 35%', scrub: true },
          }
        );
        gsap.fromTo(
          photo.querySelector('img'),
          { scale: 1.25 },
          {
            scale: 1, ease: 'none',
            scrollTrigger: { trigger: photo, start: 'top 90%', end: 'bottom 40%', scrub: true },
          }
        );
      }

      const aboutCard = document.querySelector('#about .lg\\:absolute');
      if (aboutCard) {
        const tl = gsap.timeline({
          scrollTrigger: { trigger: aboutCard, start: 'top 85%', once: true },
        });
        tl.from(aboutCard, { y: 80, opacity: 0, duration: 1.1, ease: 'power4.out', clearProps: 'transform,opacity' })
          .from('#about .space-y-3 > div', {
            x: -20, opacity: 0, duration: 0.7, ease: EASE, stagger: 0.12, clearProps: 'transform,opacity',
          }, '-=0.6');
      }

      /* =====================================================
         04 · PRODUK
      ===================================================== */
      const pframe = document.querySelector('.pview-frame');
      if (pframe) {
        gsap.fromTo(
          pframe,
          { clipPath: 'inset(0% 0% 100% 0% round 24px)' },
          {
            clipPath: 'inset(0% 0% 0% 0% round 24px)', duration: 1.3, ease: 'power4.inOut', clearProps: 'clipPath',
            scrollTrigger: { trigger: '#product-list', start: 'top 80%', once: true },
          }
        );
      }

      /* Kata raksasa background */
      gsap.utils.toArray('.bgword').forEach((w, i) => {
        gsap.fromTo(
          w,
          { x: i % 2 ? -80 : 80 },
          {
            x: i % 2 ? 120 : -120, ease: 'none',
            scrollTrigger: { trigger: w.closest('section'), start: 'top bottom', end: 'bottom top', scrub: true },
          }
        );
      });

      /* =====================================================
         05 · UMKM · parallax gambar (desktop)
      ===================================================== */
      if (desktop) {
        gsap.utils.toArray('.plx').forEach((el) => {
          gsap.fromTo(
            el,
            { yPercent: -6 },
            {
              yPercent: 6, ease: 'none',
              scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true },
            }
          );
        });
      }

      /* =====================================================
         06 · CTA (butuh id="cta" di section)
      ===================================================== */
      if (document.getElementById('cta')) {
        gsap.fromTo(
          '#cta img',
          { scale: 1.2, y: -40 },
          {
            y: 40, ease: 'none',
            scrollTrigger: { trigger: '#cta', start: 'top bottom', end: 'bottom top', scrub: true },
          }
        );
        gsap.from('#cta .text-center > *', {
          y: 40, opacity: 0, duration: 1, ease: EASE, stagger: 0.1, clearProps: 'transform,opacity',
          scrollTrigger: { trigger: '#cta', start: 'top 65%', once: true },
        });
      }

      /* =====================================================
         MODAL · dipanggil dari open()
      ===================================================== */
      window.djajanModalIn = () => {
        gsap.fromTo(
          '#detail-modal .dm-chip, #detail-modal .dm-title, #detail-modal .dm-desc, #detail-modal .dm-footer',
          { y: 18, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.6, ease: EASE, stagger: 0.07, delay: 0.1, clearProps: 'transform,opacity' }
        );
        gsap.fromTo('#detail-modal .dm-media img', { scale: 1.15 }, { scale: 1, duration: 1.2, ease: EASE, clearProps: 'transform' });
      };

      /* =====================================================
         SMOOTH ANCHOR SCROLL
      ===================================================== */
      const onAnchor = (e) => {
        const a = e.target.closest('a[href^="#"]:not([href="#"])');
        if (!a) return;
        const href = a.getAttribute('href');
        const target = document.querySelector(href);
        if (!target) return;
        e.preventDefault();
        gsap.to(window, {
          duration: 0.75,
          ease: 'power2.out',
          scrollTo: { y: target, offsetY: 80, autoKill: true },
          overwrite: 'auto',
        });
        if (window.history && window.history.pushState) {
          window.history.pushState(null, '', href);
        }
      };
      document.addEventListener('click', onAnchor);
      return () => document.removeEventListener('click', onAnchor);
    }
  );

  window.addEventListener('load', () => ScrollTrigger.refresh());
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initHome);
} else {
  initHome();
}