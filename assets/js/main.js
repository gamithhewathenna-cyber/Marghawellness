/* MĀRGHĀ Wellness — front-end behaviour (no dependencies) */
(() => {
    'use strict';
    const doc = document.documentElement;
    doc.classList.add('js');
    const $  = (s, c = document) => c.querySelector(s);
    const $$ = (s, c = document) => [...c.querySelectorAll(s)];
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Header: solid background after scrolling ---------- */
    const header = $('#siteHeader');
    if (header) {
        const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 40);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* ---------- Mobile navigation ---------- */
    const toggle = $('#navToggle');
    if (toggle) {
        const setOpen = (open) => {
            document.body.classList.toggle('nav-open', open);
            toggle.setAttribute('aria-expanded', String(open));
            toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            document.body.style.overflow = open ? 'hidden' : '';
        };
        toggle.addEventListener('click', () => setOpen(!document.body.classList.contains('nav-open')));
        $$('#mainNav a').forEach(a => a.addEventListener('click', () => setOpen(false)));
        document.addEventListener('keydown', e => { if (e.key === 'Escape') setOpen(false); });
        window.matchMedia('(min-width: 1025px)').addEventListener('change', e => { if (e.matches) setOpen(false); });
    }

    /* ---------- Hero slider ---------- */
    const hero = $('[data-hero]');
    if (hero) {
        const slides = $$('.hero__slide', hero);
        let index = 0, timer = null;
        const show = (i) => {
            if (slides.length < 2) return;
            slides[index].classList.remove('is-active');
            slides[index].setAttribute('aria-hidden', 'true');
            index = (i + slides.length) % slides.length;
            slides[index].classList.add('is-active');
            slides[index].removeAttribute('aria-hidden');
        };
        const autoplay = () => {
            clearInterval(timer);
            if (!reduceMotion && slides.length > 1) timer = setInterval(() => show(index + 1), 7000);
        };
        $('[data-hero-prev]')?.addEventListener('click', () => { show(index - 1); autoplay(); });
        $('[data-hero-next]')?.addEventListener('click', () => { show(index + 1); autoplay(); });
        autoplay();
    }

    /* ---------- Drag-to-scroll (retreat cards) ---------- */
    $$('[data-drag-scroll]').forEach(track => {
        let down = false, startX = 0, startLeft = 0, moved = false;
        track.addEventListener('pointerdown', e => {
            if (e.pointerType !== 'mouse') return;
            down = true; moved = false; startX = e.clientX; startLeft = track.scrollLeft;
        });
        window.addEventListener('pointermove', e => {
            if (!down) return;
            const dx = e.clientX - startX;
            if (Math.abs(dx) > 4) { moved = true; track.classList.add('is-dragging'); }
            track.scrollLeft = startLeft - dx;
        });
        window.addEventListener('pointerup', () => {
            if (!down) return;
            down = false;
            setTimeout(() => track.classList.remove('is-dragging'), 0);
        });
        track.addEventListener('click', e => { if (moved) { e.preventDefault(); moved = false; } }, true);
        track.addEventListener('keydown', e => {
            if (e.key === 'ArrowRight') track.scrollBy({ left: 330, behavior: 'smooth' });
            if (e.key === 'ArrowLeft')  track.scrollBy({ left: -330, behavior: 'smooth' });
        });
    });

    /* ---------- Journey filters ---------- */
    const grid = $('[data-journey-grid]');
    if (grid) {
        const cards = $$('.journey-card', grid);
        const empty = $('.journeys__empty');
        $$('.filter').forEach(btn => btn.addEventListener('click', () => {
            $$('.filter').forEach(b => { b.classList.toggle('is-active', b === btn); b.setAttribute('aria-selected', String(b === btn)); });
            const f = btn.dataset.filter;
            let visible = 0;
            cards.forEach(c => {
                const show = f === 'all' || c.dataset.path === f;
                c.classList.toggle('is-hidden', !show);
                if (show) visible++;
            });
            if (empty) empty.hidden = visible > 0;
        }));
    }

    /* ---------- Journal carousel ---------- */
    const jTrack = $('[data-journal-track]');
    if (jTrack) {
        const prev = $('[data-journal-prev]'), next = $('[data-journal-next]');
        const step = () => (jTrack.firstElementChild?.getBoundingClientRect().width || 300) + parseFloat(getComputedStyle(jTrack).columnGap || 0);
        // Wraps around at either end so the arrows (always visible, per design) never dead-end.
        const go = (dir) => {
            const max = jTrack.scrollWidth - jTrack.clientWidth;
            if (max <= 2) return;
            let left = jTrack.scrollLeft + dir * step();
            if (dir > 0 && jTrack.scrollLeft >= max - 2) left = 0;
            if (dir < 0 && jTrack.scrollLeft <= 2) left = max;
            jTrack.scrollTo({ left, behavior: reduceMotion ? 'auto' : 'smooth' });
        };
        prev.addEventListener('click', () => go(-1));
        next.addEventListener('click', () => go(1));
    }

    /* ---------- Newsletter subscribe (AJAX, progressive enhancement) ---------- */
    $$('.subscribe-form').forEach(form => {
        const msg = $('.subscribe-form__msg', form);
        const btn = $('button[type="submit"]', form);
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const email = form.email.value.trim();
            msg.classList.remove('is-error');
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                msg.textContent = 'Please enter a valid email address.';
                msg.classList.add('is-error');
                form.email.focus();
                return;
            }
            btn.disabled = true;
            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'fetch', 'Accept': 'application/json' },
                });
                const data = await res.json().catch(() => ({}));
                msg.textContent = data.message || (res.ok ? 'Thank you for subscribing.' : 'Something went wrong. Please try again.');
                msg.classList.toggle('is-error', !res.ok || data.ok === false);
                if (res.ok && data.ok !== false) form.reset();
            } catch {
                msg.textContent = 'Network error. Please try again.';
                msg.classList.add('is-error');
            } finally {
                btn.disabled = false;
            }
        });
    });

    /* ---------- Reveal on scroll ---------- */
    if ('IntersectionObserver' in window && !reduceMotion) {
        const targets = $$('.pillar, .retreat-card, .step, .journey-card, .post-card, .destination__text > *, .founder__text, .cta-band__content');
        targets.forEach(t => t.classList.add('reveal'));
        const io = new IntersectionObserver(entries => {
            entries.forEach(en => {
                if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); }
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
        targets.forEach(t => io.observe(t));
    }
})();
