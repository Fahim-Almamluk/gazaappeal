const isRtl = () => getComputedStyle(document.documentElement).direction === 'rtl';
const SWIPE_DISTANCE = 50;

export default () => ({
    current: -1,
    startX: 0,
    items: [],

    init() {
        this.items = [...this.$root.querySelectorAll('[data-gallery-item]')];
    },

    get isOpen() {
        return this.current >= 0;
    },

    get src() {
        return this.items[this.current]?.dataset.src ?? '';
    },

    get caption() {
        return this.items[this.current]?.dataset.caption ?? '';
    },

    get counter() {
        return `${this.current + 1} / ${this.items.length}`;
    },

    open(event) {
        this.current = Number(event.currentTarget.dataset.index);
        this.preload();
    },

    close() {
        this.current = -1;
    },

    step(offset) {
        const total = this.items.length;
        this.current = (this.current + offset + total) % total;
        this.preload();
    },

    next() {
        this.step(1);
    },

    prev() {
        this.step(-1);
    },

    preload() {
        const total = this.items.length;

        [1, -1].forEach((offset) => {
            new Image().src = this.items[(this.current + offset + total) % total].dataset.src;
        });
    },

    key(event) {
        if (!this.isOpen) return;

        const forward = isRtl() ? 'ArrowLeft' : 'ArrowRight';
        const backward = isRtl() ? 'ArrowRight' : 'ArrowLeft';

        if (event.key === 'Escape') this.close();
        if (event.key === forward) this.next();
        if (event.key === backward) this.prev();
    },

    down(event) {
        this.startX = event.clientX;
    },

    up(event) {
        const distance = event.clientX - this.startX;

        if (Math.abs(distance) < SWIPE_DISTANCE) return;

        this.step(distance < 0 !== isRtl() ? 1 : -1);
    },
});
