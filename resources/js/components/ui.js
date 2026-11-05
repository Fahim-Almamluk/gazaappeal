export const dropdown = () => ({
    open: false,

    toggle() {
        this.open = !this.open;
    },

    close() {
        this.open = false;
    },
});

export const passwordField = () => ({
    visible: false,

    toggle() {
        this.visible = !this.visible;
    },

    get type() {
        return this.visible ? 'text' : 'password';
    },

    get hidden() {
        return !this.visible;
    },

    get label() {
        return this.visible ? this.$root.dataset.hideLabel : this.$root.dataset.showLabel;
    },
});

export const submitGuard = () => ({
    busy: false,

    submit() {
        this.busy = true;
    },

    get idle() {
        return !this.busy;
    },
});

export const toast = () => ({
    visible: true,
    gone: false,

    init() {
        setTimeout(() => this.dismiss(), Number(this.$el.dataset.timeout ?? 6000));
    },

    dismiss() {
        this.visible = false;
        setTimeout(() => (this.gone = true), 320);
    },

    get shown() {
        return !this.gone;
    },

    get fade() {
        return this.visible ? '' : 'opacity-0';
    },
});

export const copyText = () => ({
    copied: false,

    async copy() {
        try {
            await navigator.clipboard.writeText(this.$root.dataset.text);
            this.copied = true;
            setTimeout(() => (this.copied = false), 2000);
        } catch {}
    },

    get idle() {
        return !this.copied;
    },

    get label() {
        return this.copied ? this.$root.dataset.copied : this.$root.dataset.copy;
    },
});

export const localeTabs = () => ({
    active: '',

    init() {
        this.active = this.$root.dataset.open;
        this.sync();
    },

    select(event) {
        this.active = event.currentTarget.dataset.locale;
        this.sync();
    },

    reveal(event) {
        const panel = event.target.closest('[data-locale-panel]');

        if (panel) {
            this.active = panel.dataset.localePanel;
            this.sync();
        }
    },

    sync() {
        this.$root.querySelectorAll('[data-locale]').forEach((tab) => tab.setAttribute('aria-selected', String(tab.dataset.locale === this.active)));
        this.$root.querySelectorAll('[data-locale-panel]').forEach((panel) => {
            panel.hidden = panel.dataset.localePanel !== this.active;
        });
    },
});

export const tabs = () => ({
    active: '',

    init() {
        this.active = this.$root.dataset.open;
        this.sync();
    },

    select(event) {
        this.active = event.currentTarget.dataset.tab;
        this.sync();
    },

    sync() {
        this.$root.querySelectorAll('[data-tab]').forEach((tab) => tab.setAttribute('aria-selected', String(tab.dataset.tab === this.active)));
        this.$root.querySelectorAll('[data-tab-panel]').forEach((panel) => {
            panel.hidden = panel.dataset.tabPanel !== this.active;
        });
    },
});

export const imagePreview = () => ({
    url: null,

    init() {
        this.url = this.$root.dataset.current || null;
    },

    pick(event) {
        const [file] = event.target.files;
        if (this.url?.startsWith('blob:')) URL.revokeObjectURL(this.url);
        this.url = file ? URL.createObjectURL(file) : this.$root.dataset.current || null;
        this.$refs.image?.setAttribute('src', this.url ?? '');
    },

    get hasImage() {
        return Boolean(this.url);
    },

    get empty() {
        return !this.url;
    },
});

export const methodFields = () => ({
    type: '',

    init() {
        this.type = this.$refs.type.value;
    },

    sync() {
        this.type = this.$refs.type.value;
    },

    get isLink() {
        return this.type === this.$root.dataset.link;
    },

    get isBank() {
        return this.type === this.$root.dataset.bank;
    },
});

export const navGroup = () => ({
    open: false,

    init() {
        this.open = this.$el.dataset.open === 'true';
    },

    toggle() {
        this.open = !this.open;
    },

    get shown() {
        return this.open || (this.collapsed && window.matchMedia('(min-width: 1024px)').matches);
    },

    get closed() {
        return !this.shown;
    },
});
