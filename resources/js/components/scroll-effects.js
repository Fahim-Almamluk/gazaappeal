const NUMBER = /\d[\d,]*(\.\d+)?/;
const COUNT_MS = 1100;

const reduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function revealWithObserver() {
    const items = [...document.querySelectorAll('.reveal')];
    const watcher = new IntersectionObserver(
        (entries) => entries.forEach(({ target, isIntersecting }) => {
            if (!isIntersecting) return;
            watcher.unobserve(target);
            target.dataset.in = '';
            delete target.dataset.pending;
            setTimeout(() => delete target.dataset.in, 1200);
        }),
        { rootMargin: '0px 0px -8% 0px' },
    );

    items.forEach((item) => {
        item.dataset.pending = '';
        watcher.observe(item);
    });
}

function zeroed(text) {
    return text.replace(NUMBER, (found, decimals) => (0).toFixed(decimals ? decimals.length - 1 : 0));
}

function count(element, final) {
    const [found, decimals] = final.match(NUMBER);
    const places = decimals ? decimals.length - 1 : 0;
    const target = parseFloat(found.replaceAll(',', ''));
    const began = performance.now();

    const step = (now) => {
        const t = Math.min(1, (now - began) / COUNT_MS);
        const value = target * (1 - Math.pow(1 - t, 3));
        element.textContent = final.replace(NUMBER, value.toLocaleString('en-US', { minimumFractionDigits: places, maximumFractionDigits: places }));
        if (t < 1) requestAnimationFrame(step);
    };

    requestAnimationFrame(step);
}

function countUp() {
    const items = [...document.querySelectorAll('[data-count]')].filter((item) => NUMBER.test(item.textContent));
    const finals = new Map(items.map((item) => [item, item.textContent]));
    const watcher = new IntersectionObserver(
        (entries) => entries.forEach(({ target, isIntersecting }) => {
            if (!isIntersecting) return;
            watcher.unobserve(target);
            count(target, finals.get(target));
        }),
        { threshold: 0.6 },
    );

    items.forEach((item) => {
        item.textContent = zeroed(finals.get(item));
        watcher.observe(item);
    });
}

export function registerScrollEffects() {
    if (reduced()) return;

    if (!CSS.supports('animation-timeline: view()')) revealWithObserver();
    countUp();
}
