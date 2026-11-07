const STORAGE_KEY = 'gaza.theme';
const media = window.matchMedia('(prefers-color-scheme: dark)');

const stored = () => {
    try {
        return localStorage.getItem(STORAGE_KEY) ?? 'system';
    } catch {
        return 'system';
    }
};

const resolve = (mode) => (mode === 'dark' || (mode === 'system' && media.matches) ? 'dark' : 'light');

const apply = (theme) => {
    const root = document.documentElement;

    root.classList.add('theme-switching');
    root.dataset.theme = theme;
    requestAnimationFrame(() => requestAnimationFrame(() => root.classList.remove('theme-switching')));
};

export default () => ({
    dark: document.documentElement.dataset.theme === 'dark',

    init() {
        media.addEventListener('change', () => {
            if (stored() === 'system') {
                this.dark = resolve('system') === 'dark';
                apply(this.dark ? 'dark' : 'light');
            }
        });
    },

    toggle() {
        this.dark = !this.dark;

        try {
            localStorage.setItem(STORAGE_KEY, this.dark ? 'dark' : 'light');
        } catch {}

        apply(this.dark ? 'dark' : 'light');
    },

    get light() {
        return !this.dark;
    },

    get label() {
        return this.dark ? this.$root.dataset.toLight : this.$root.dataset.toDark;
    },
});
