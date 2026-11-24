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

    /* ---------- Package page: highlight the section you are reading in the sticky menu ---------- */
    const pkNav = $('[data-pk-nav]');
    if (pkNav && 'IntersectionObserver' in window) {
        const links = $$('.pk-nav__links a', pkNav);
        const targets = links.map(a => document.getElementById(a.hash.slice(1))).filter(Boolean);
        const setActive = id => links.forEach(a => a.classList.toggle('is-active', a.hash === '#' + id));
        const spy = new IntersectionObserver(entries => {
            entries.forEach(en => { if (en.isIntersecting) setActive(en.target.id); });
        }, { rootMargin: '-45% 0px -50% 0px' });
        targets.forEach(t => spy.observe(t));
    }

    /* ---------- Wellness page: programme filters ---------- */
    const wpFilters = $('[data-wp-filters]');
    if (wpFilters) {
        const cards = $$('.wp-pk', $('[data-wp-programmes]'));
        const empty = $('[data-wp-empty]');
        const apply = () => {
            const f = Object.fromEntries(new FormData(wpFilters));
            let shown = 0;
            cards.forEach(c => {
                const ok = (!f.depth || c.dataset.depth === f.depth)
                    && (!f.path || c.dataset.path === f.path)
                    && (!f.for || c.dataset.for.split(' ').includes(f.for))
                    && (!f.nights || c.dataset.nights === f.nights);
                c.hidden = !ok;
                if (ok) shown++;
            });
            if (empty) empty.hidden = shown > 0;
        };
        wpFilters.addEventListener('change', apply);
    }

    /* ---------- "Enquire Now" full-screen form ---------- */
    const enq = $('.enquire[role="dialog"]');
    if (enq) {
        const form = $('.enquire-form', enq);
        const msg = $('.enquire-form__msg', enq);
        const body = $('[data-enquire-body]', enq);
        const done = $('[data-enquire-done]', enq);
        let lastFocus = null, isOpen = false, hideTimer = null;
        const isEnquireLink = a => a && a.pathname && /\/enquire\/?$/.test(a.pathname) && a.origin === location.origin;
        const focusables = () => $$('button, [href], input:not(.hp), select, textarea', enq).filter(el => !el.disabled && el.offsetParent !== null);

        const open = () => {
            if (isOpen) return;
            isOpen = true; clearTimeout(hideTimer);
            lastFocus = document.activeElement;
            document.body.classList.remove('nav-open');            // close the mobile menu if open
            enq.hidden = false;
            document.documentElement.classList.add('enquire-open');
            requestAnimationFrame(() => { if (isOpen) enq.classList.add('is-open'); });
            if (location.hash !== '#enquire') history.pushState({ enquire: true }, '', '#enquire');
            setTimeout(() => (done.hidden ? $('input:not(.hp)', form) : done)?.focus(), reduceMotion ? 0 : 250);
        };
        const close = (fromHistory = false) => {
            if (!isOpen) return;                                     // already closed / closing
            isOpen = false;
            enq.classList.remove('is-open');
            document.documentElement.classList.remove('enquire-open');
            hideTimer = setTimeout(() => {
                enq.hidden = true;
                if (!done.hidden) { done.hidden = true; body.hidden = false; form.reset(); msg.textContent = ''; }
            }, reduceMotion ? 0 : 300);
            if (!fromHistory && location.hash === '#enquire') history.back();
            lastFocus?.focus?.();
        };

        // Any link to /enquire opens the pop-up (header, mobile menu, call-to-action, hero button …)
        document.addEventListener('click', e => {
            const a = e.target.closest('a');
            if (isEnquireLink(a) && !e.metaKey && !e.ctrlKey && !e.shiftKey) {
                e.preventDefault();
                if (form.elements.package) form.elements.package.value = a.dataset.package || '';   // e.g. "Arogya" from a package page
                open();
            }
        });
        $$('[data-enquire-close]', enq).forEach(b => b.addEventListener('click', () => close()));
        enq.addEventListener('keydown', e => {
            if (e.key === 'Escape') { e.preventDefault(); close(); }
            if (e.key === 'Tab') {                                    // keep focus inside the pop-up
                const f = focusables(); if (!f.length) return;
                if (e.shiftKey && document.activeElement === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
                else if (!e.shiftKey && document.activeElement === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
            }
        });
        window.addEventListener('popstate', () => { if (location.hash !== '#enquire') close(true); else open(); });
        if (location.hash === '#enquire') open();                     // shareable link: yoursite/#enquire

        // Wellness page — Wellness Path Profile: the chosen answer is carried into the enquiry form.
        const profile = $('[data-wp-profile]');
        if (profile) {
            const pmsg = $('[data-wp-profile-msg]', profile);
            profile.addEventListener('submit', e => {
                e.preventDefault();
                const picked = $('input[name="stage"]:checked', profile);
                if (!picked) { pmsg.textContent = 'Choose the answer that feels closest.'; return; }
                pmsg.textContent = '';
                const stage = $('#ef_stage', enq);
                if (stage) stage.value = picked.value;
                open();
            });
            profile.addEventListener('change', () => { pmsg.textContent = ''; });
        }

        form.addEventListener('submit', async e => {
            e.preventDefault();
            $$('.ef-field', form).forEach(f => f.classList.remove('has-error'));
            msg.classList.remove('is-error');
            const errs = {};
            if (!form.full_name.value.trim()) errs.full_name = 'Please tell us your name.';
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email.value.trim())) errs.email = 'Please enter a valid email address.';
            const showErrors = errors => {
                Object.keys(errors).forEach(k => form.elements[k]?.closest('.ef-field')?.classList.add('has-error'));
                msg.textContent = Object.values(errors)[0];
                msg.classList.add('is-error');
                form.elements[Object.keys(errors)[0]]?.focus();
            };
            if (Object.keys(errs).length) return showErrors(errs);

            const btn = $('.enquire-form__submit', form);
            btn.disabled = true; msg.textContent = 'Sending…';
            try {
                const res = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'fetch', 'Accept': 'application/json' } });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.ok !== false) {
                    body.hidden = true; done.hidden = false; done.focus();
                    $('.enquire__main', enq).scrollTop = 0;
                } else if (data.errors && Object.keys(data.errors).length) {
                    showErrors(data.errors);
                } else {
                    msg.textContent = data.message || 'Something went wrong. Please try again.'; msg.classList.add('is-error');
                }
            } catch {
                msg.textContent = 'Network error. Please check your connection and try again.'; msg.classList.add('is-error');
            } finally {
                btn.disabled = false;
                if (!msg.classList.contains('is-error') && !done.hidden) msg.textContent = '';
            }
        });
    }

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
