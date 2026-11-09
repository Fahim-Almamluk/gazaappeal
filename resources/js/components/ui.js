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

const normalize = (text) =>
    text
        .normalize('NFD')
        .replace(/\p{M}/gu, '')
        .replace(/[أإآ]/g, 'ا')
        .replace(/ى/g, 'ي')
        .replace(/ة/g, 'ه')
        .toLocaleLowerCase()
        .trim();

export const combobox = () => ({
    open: false,

    toggle() {
        if (this.open) {
            this.close();
            return;
        }

        this.open = true;
        this.$root.querySelector('[data-search]').value = '';
        this.filter();
        this.$nextTick(() => this.$root.querySelector('[data-search]').focus());
    },

    close() {
        this.open = false;
    },

    visibleOptions() {
        return [...this.$root.querySelectorAll('[data-option]')].filter((option) => !option.parentElement.hidden);
    },

    filter() {
        const term = normalize(this.$root.querySelector('[data-search]').value);

        this.$root.querySelectorAll('[data-option]').forEach((option) => {
            option.parentElement.hidden = term !== '' && !normalize(option.dataset.text).includes(term);
        });

        this.$root.querySelector('[data-none]').hidden = this.visibleOptions().length > 0;
    },

    move(event) {
        const options = this.visibleOptions();

        if (options.length === 0) {
            return;
        }

        const step = event.key === 'ArrowDown' ? 1 : -1;
        const index = options.indexOf(document.activeElement);
        const next = index === -1 ? (step === 1 ? 0 : options.length - 1) : (index + step + options.length) % options.length;

        options[next].focus();
    },

    first() {
        const [option] = this.visibleOptions();

        if (option) {
            option.click();
        }
    },

    pick(event) {
        const option = event.currentTarget;

        this.$root.querySelector('[data-input]').value = option.dataset.value;
        this.$root.querySelector('[data-label]').textContent = option.dataset.text;
        this.$root.querySelectorAll('[data-option]').forEach((item) => item.setAttribute('aria-selected', String(item === option)));
        this.close();
        this.$root.querySelector('[data-trigger]').focus();
    },
});

export const bulkSelect = () => ({
    count: 0,

    init() {
        this.sync();
    },

    rows() {
        return [...this.$root.querySelectorAll('[data-row-check]')];
    },

    sync() {
        const rows = this.rows();
        const checked = rows.filter((row) => row.checked).length;
        const all = this.$root.querySelector('[data-all-check]');

        this.count = checked;

        if (all) {
            all.checked = rows.length > 0 && checked === rows.length;
            all.indeterminate = checked > 0 && checked < rows.length;
        }
    },

    toggleAll(event) {
        this.rows().forEach((row) => {
            row.checked = event.target.checked;
        });
        this.sync();
    },

    clear() {
        this.rows().forEach((row) => {
            row.checked = false;
        });
        this.sync();
    },

    get any() {
        return this.count > 0;
    },

    get label() {
        return this.$root.querySelector('[data-template]').dataset.template.replace('{count}', this.count);
    },
});
