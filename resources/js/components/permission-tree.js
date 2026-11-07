const ACTION = '[data-level="action"]';

const syncBox = (box, actions) => {
    const checked = actions.filter((action) => action.checked).length;

    if (box) {
        box.checked = actions.length > 0 && checked === actions.length;
        box.indeterminate = checked > 0 && checked < actions.length;
    }

    return [checked, actions.length];
};

const actionsIn = (scope, selector = ACTION) => [...scope.querySelectorAll(selector)];

const editable = (action) => !action.disabled && action.closest('[data-module]').dataset.filtered !== 'true';

const stateOf = (action) => {
    if (action.dataset.inherited === undefined) {
        return action.checked ? 'on' : 'off';
    }

    const inherited = action.dataset.inherited === '1';

    if (action.checked) {
        return inherited ? 'on' : 'granted';
    }

    return inherited ? 'revoked' : 'off';
};

export default () => ({
    query: '',

    init() {
        this.refresh();
    },

    change(event) {
        const box = event.target;
        const level = box.dataset.level;
        const scopes = {
            group: () => actionsIn(box.closest('[data-group]')),
            module: () => actionsIn(box.closest('[data-module]')),
        };

        if (level in scopes) {
            scopes[level]().filter(editable).forEach((action) => {
                action.checked = box.checked;
            });
        }

        this.refresh();
    },

    setAll(value) {
        actionsIn(this.$root).filter(editable).forEach((action) => {
            action.checked = value;
        });
        this.refresh();
    },

    selectAll() {
        this.setAll(true);
    },

    clearAll() {
        this.setAll(false);
    },

    toggle(event) {
        const group = event.currentTarget.closest('[data-group]');

        group.setAttribute('aria-expanded', String(group.getAttribute('aria-expanded') !== 'true'));
        this.layout();
    },

    filter(event) {
        this.query = event.target.value.trim().toLowerCase();

        if (this.query !== '') {
            this.$root.querySelectorAll('[data-group]').forEach((group) => group.setAttribute('aria-expanded', 'true'));
        }

        this.layout();
    },

    layout() {
        this.$root.querySelectorAll('[data-group]').forEach((group) => {
            const open = group.getAttribute('aria-expanded') === 'true';
            const modules = [...group.querySelectorAll('[data-module]')];
            const matches = (module) => this.query === '' || module.dataset.search.toLowerCase().includes(this.query);

            modules.forEach((module) => {
                module.dataset.filtered = String(!matches(module));
                module.hidden = !open || !matches(module);
            });

            group.hidden = !modules.some(matches);
        });

        this.refresh();
    },

    refresh() {
        this.$root.querySelectorAll('[data-module]').forEach((module) => syncBox(module.querySelector('[data-level="module"]'), actionsIn(module)));

        this.$root.querySelectorAll('[data-group]').forEach((group) => {
            const [checked, total] = syncBox(group.querySelector('[data-level="group"]'), actionsIn(group));
            group.querySelector('[data-group-count]').textContent = `${checked}/${total}`;
        });

        actionsIn(this.$root).forEach((action) => {
            action.closest('[data-chip]').dataset.state = stateOf(action);
        });

        const [checked, total] = syncBox(null, actionsIn(this.$root));
        this.$root.querySelector('[data-counter]').textContent = `${checked} / ${total}`;
    },
});
