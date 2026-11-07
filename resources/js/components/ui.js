export const dropdown = () => ({
    open: false,

    show() {
        this.open = true;
    },

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

const hasCase = (value) => /\p{Ll}/u.test(value) && /\p{Lu}/u.test(value);

export const passwordStrength = () => ({
    measure(event) {
        const value = event.target.value;
        const rules = { min: value.length >= 8, case: hasCase(value), digit: /\p{N}/u.test(value) };
        const accepted = rules.min && rules.case && rules.digit;
        const bonus = Number(value.length >= 12) + Number(/[^\p{L}\p{N}]/u.test(value));
        const level = value === '' ? 0 : accepted ? 2 + bonus : 1;
        const names = ['', 'weak', 'medium', 'strong', 'veryStrong'];

        this.$root.dataset.tone = ['', 'danger', 'warning', 'success', 'success'][level];
        this.$root.querySelectorAll('[data-segment]').forEach((segment) => {
            segment.dataset.on = String(level >= Number(segment.dataset.segment));
        });
        this.$refs.label.textContent = level === 0 ? '' : this.$root.dataset[names[level]];
        this.$root.querySelectorAll('[data-rule]').forEach((item) => {
            item.dataset.met = String(rules[item.dataset.rule]);
        });
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

export const menuFields = () => ({
    type: 'page',

    init() {
        this.type = this.$refs.type.value;
    },

    sync() {
        this.type = this.$refs.type.value;
    },

    get isPage() {
        return this.type === 'page';
    },

    get isUrl() {
        return this.type === 'url';
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
