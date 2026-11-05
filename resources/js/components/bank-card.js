const still = window.matchMedia('(prefers-reduced-motion: reduce)');

export default () => ({
    flipped: false,
    frame: 0,

    toggle() {
        this.flipped = !this.flipped;
    },

    tilt(event) {
        if (event.pointerType !== 'mouse' || still.matches) return;

        const { left, top, width, height } = this.$refs.stage.getBoundingClientRect();
        const x = Math.min(Math.max((event.clientX - left) / width, 0), 1);
        const y = Math.min(Math.max((event.clientY - top) / height, 0), 1);

        cancelAnimationFrame(this.frame);
        this.frame = requestAnimationFrame(() => {
            const card = this.$refs.card;
            card.dataset.tilting = 'true';
            card.style.setProperty('--tilt-x', `${((0.5 - y) * 20).toFixed(2)}deg`);
            card.style.setProperty('--tilt-y', `${((x - 0.5) * 24).toFixed(2)}deg`);
            card.style.setProperty('--glare-x', `${(x * 100).toFixed(1)}%`);
            card.style.setProperty('--glare-y', `${(y * 100).toFixed(1)}%`);
        });
    },

    rest() {
        cancelAnimationFrame(this.frame);
        const card = this.$refs.card;
        card.dataset.tilting = 'false';
        ['--tilt-x', '--tilt-y', '--glare-x', '--glare-y'].forEach((name) => card.style.removeProperty(name));
    },
});
