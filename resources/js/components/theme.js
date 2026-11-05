const STORAGE_KEY = 'gaza.theme';
const media = window.matchMedia('(prefers-color-scheme: dark)');

export function storedTheme() {
    try {
        return localStorage.getItem(STORAGE_KEY) ?? 'system';
    } catch {
        return 'system';
    }
}

export function applyTheme(mode) {
    const root = document.documentElement;
    const dark = mode === 'dark' || (mode === 'system' && media.matches);

    root.classList.add('theme-switching');
    root.dataset.theme = dark ? 'dark' : 'light';
    requestAnimationFrame(() => requestAnimationFrame(() => root.classList.remove('theme-switching')));
}

export default () => ({
    mode: storedTheme(),

    init() {
        media.addEventListener('change', () => this.mode === 'system' && applyTheme('system'));
    },

    choose(mode) {
        this.mode = mode;
        try {
            localStorage.setItem(STORAGE_KEY, mode);
        } catch {}
        applyTheme(mode);
    },

    setLight() {
        this.choose('light');
    },

    setDark() {
        this.choose('dark');
    },

    setSystem() {
        this.choose('system');
    },

    cycle() {
        const order = ['light', 'dark', 'system'];
        this.choose(order[(order.indexOf(this.mode) + 1) % order.length]);
    },

    get isLight() {
        return this.mode === 'light';
    },

    get isDark() {
        return this.mode === 'dark';
    },

    get isSystem() {
        return this.mode === 'system';
    },
});
