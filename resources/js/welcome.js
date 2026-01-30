/**
 * Landing page: mobile menu, FAQ accordion, smooth scroll, scroll-to-top.
 */
document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initFAQ();
    initSmoothScroll();
    initScrollToTop();
});

function initMobileMenu() {
    const openBtn = document.getElementById('landing-mobile-menu-button');
    const closeBtn = document.getElementById('landing-mobile-menu-close');
    const menu = document.getElementById('landing-mobile-menu');
    const overlay = document.getElementById('landing-mobile-overlay');
    const links = document.querySelectorAll('.landing-mobile-nav-link');

    if (openBtn) openBtn.addEventListener('click', openMobileMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMobileMenu);
    if (overlay) overlay.addEventListener('click', closeMobileMenu);
    links.forEach((el) => el.addEventListener('click', closeMobileMenu));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menu?.classList.contains('open')) closeMobileMenu();
    });
}

function openMobileMenu() {
    const menu = document.getElementById('landing-mobile-menu');
    const overlay = document.getElementById('landing-mobile-overlay');
    if (menu) menu.classList.add('open');
    if (overlay) overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    const menu = document.getElementById('landing-mobile-menu');
    const overlay = document.getElementById('landing-mobile-overlay');
    if (menu) menu.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
    document.body.style.overflow = '';
}

function initFAQ() {
    document.querySelectorAll('.landing-faq-question').forEach((btn) => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.landing-faq-item');
            if (!item) return;
            const active = item.classList.contains('active');
            document.querySelectorAll('.landing-faq-item').forEach((i) => i.classList.remove('active'));
            if (!active) item.classList.add('active');
        });
    });
}

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const href = a.getAttribute('href');
            if (href === '#' || !href) return;
            const target = document.querySelector(href);
            if (!target) return;
            e.preventDefault();
            const top = target.getBoundingClientRect().top + window.scrollY - 80;
            window.scrollTo({ top, behavior: 'smooth' });
            history.pushState(null, null, href);
        });
    });
}

function initScrollToTop() {
    const btn = document.getElementById('landing-scroll-top');
    if (!btn) return;
    const update = () => {
        btn.classList.toggle('visible', window.scrollY > 300);
    };
    update();
    window.addEventListener('scroll', update, { passive: true });
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}
