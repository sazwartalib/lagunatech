// Scroll-triggered reveal for elements marked with [data-reveal].
// Progressive enhancement: elements are fully visible by default (so
// no-JS visitors and crawlers see everything). JS arms the hidden
// starting state at runtime, then reveals each element as it scrolls
// into view. Runs on every page but only does anything where such
// elements exist (the marketing site), so it's a no-op elsewhere.
function initScrollReveal() {
    const targets = document.querySelectorAll('[data-reveal]:not(.reveal-armed)');

    if (!targets.length) {
        return;
    }

    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const delay = entry.target.dataset.revealDelay || 0;
                setTimeout(() => entry.target.classList.add('is-revealed'), Number(delay));
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -10% 0px' }
    );

    targets.forEach((el) => {
        el.classList.add('reveal-armed');
        observer.observe(el);
    });
}

// Sticky navbar: transparent over the hero, glass once the page scrolls.
function initMarketingNav() {
    const nav = document.querySelector('[data-marketing-nav]');

    if (!nav) {
        return;
    }

    const applyState = () => {
        nav.classList.toggle('is-scrolled', window.scrollY > 24);
    };

    applyState();
    window.addEventListener('scroll', applyState, { passive: true });
}

document.addEventListener('DOMContentLoaded', () => {
    initScrollReveal();
    initMarketingNav();
});

// Livewire full-page navigations swap the DOM without a hard reload.
document.addEventListener('livewire:navigated', () => {
    initScrollReveal();
    initMarketingNav();
});
