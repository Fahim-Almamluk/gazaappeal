const SHOW_AFTER = 480;

export default () => ({
    shown: false,
    ticking: false,

    init() {
        this.onScroll = () => {
            if (this.ticking) return;
            this.ticking = true;
            requestAnimationFrame(() => {
                this.ticking = false;
                this.sync();
            });
        };
        window.addEventListener('scroll', this.onScroll, { passive: true });
        this.sync();
    },

    destroy() {
        window.removeEventListener('scroll', this.onScroll);
    },

    sync() {
        const shown = window.scrollY > SHOW_AFTER;
        if (shown === this.shown) return;

        this.shown = shown;
        this.$el.dataset.show = String(shown);
        this.$el.inert = !shown;
    },

    top() {
        const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.scrollTo({ top: 0, behavior: calm ? 'auto' : 'smooth' });
        this.$refs.button.blur();
    },
});
