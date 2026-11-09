/* MĀRGHĀ Wellness — admin panel behaviour (no dependencies) */
(() => {
    'use strict';

    // Mobile sidebar toggle
    const menu = document.querySelector('[data-menu]');
    if (menu) {
        menu.addEventListener('click', () => {
            const open = document.body.classList.toggle('menu-open');
            menu.setAttribute('aria-expanded', String(open));
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') { document.body.classList.remove('menu-open'); menu.setAttribute('aria-expanded', 'false'); }
        });
    }

    // Section tabs: on small screens keep the current tab visible in the scrolling row
    const tabs = document.querySelector('.section-nav ol');
    const current = tabs?.querySelector('a[aria-current="page"]');
    if (tabs && current && tabs.scrollWidth > tabs.clientWidth) {
        tabs.scrollLeft = current.offsetLeft - (tabs.clientWidth - current.offsetWidth) / 2;
    }

    // Selects that save as soon as they change (e.g. a user's access level)
    document.querySelectorAll('select[data-autosubmit]').forEach(sel => {
        sel.addEventListener('change', () => sel.form?.requestSubmit());
    });

    // Confirm destructive actions (data-confirm on the form or on the clicked button)
    document.addEventListener('submit', e => {
        const msg = e.submitter?.dataset.confirm || e.target.dataset.confirm;
        if (msg && !window.confirm(msg)) e.preventDefault();
    });

    // Live preview for image uploads + filename edits
    document.querySelectorAll('.image-field').forEach(field => {
        const preview = field.querySelector('.image-field__preview');
        const file = field.querySelector('input[type=file]');
        const text = field.querySelector('input[type=text]');
        file?.addEventListener('change', () => {
            const f = file.files?.[0];
            if (!f) return;
            preview.src = URL.createObjectURL(f);
            preview.hidden = false;
        });
        text?.addEventListener('change', () => {
            const v = text.value.trim();
            if (!v) { preview.hidden = true; return; }
            const base = preview.dataset.base ?? '';
            preview.src = /^https?:\/\//.test(v) ? v : base + v;
            preview.hidden = false;
        });
    });

    // Warn before leaving a form with unsaved changes
    document.querySelectorAll('form.form').forEach(form => {
        let dirty = false;
        form.addEventListener('input', () => { dirty = true; });
        form.addEventListener('submit', () => { dirty = false; });
        window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
    });
})();
