const ACTION = '[data-level="action"]';

const syncBox = (scope, box) => {
    const actions = [...scope.querySelectorAll(ACTION)];
    const checked = actions.filter((action) => action.checked).length;

    if (box) {
        box.checked = actions.length > 0 && checked === actions.length;
        box.indeterminate = checked > 0 && checked < actions.length;
    }

    return [checked, actions.length];
};

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
    init() {
        this.refresh();
    },

    change(event) {
        const box = event.target;
        const level = box.dataset.level;

        if (level === 'group' || level === 'module') {
            box.closest(level === 'group' ? '[data-group]' : '[data-module]')
                .querySelectorAll(ACTION)
                .forEach((action) => {
                    if (!action.disabled && !action.closest('[data-module]').hidden) {
                        action.checked = box.checked;
                    }
                });
        }

        this.refresh();
    },

    setAll(value) {
        this.$root.querySelectorAll(ACTION).forEach((action) => {
            if (!action.disabled) {
                action.checked = value;
            }
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
        const open = group.getAttribute('aria-expanded') !== 'true';

        group.setAttribute('aria-expanded', String(open));
        group.querySelector('[data-group-panel]').hidden = !open;
    },

    filter(event) {
        const query = event.target.value.trim().toLowerCase();

        this.$root.querySelectorAll('[data-group]').forEach((group) => {
            const modules = [...group.querySelectorAll('[data-module]')];

            modules.forEach((module) => {
                module.hidden = query !== '' && !module.dataset.search.toLowerCase().includes(query);
            });

            group.hidden = modules.every((module) => module.hidden);

            if (query !== '' && !group.hidden) {
                group.setAttribute('aria-expanded', 'true');
                group.querySelector('[data-group-panel]').hidden = false;
            }
        });
    },

    refresh() {
        this.$root.querySelectorAll('[data-module]').forEach((module) => syncBox(module, module.querySelector('[data-level="module"]')));

        this.$root.querySelectorAll('[data-group]').forEach((group) => {
            const [checked, total] = syncBox(group, group.querySelector('[data-level="group"]'));
            group.querySelector('[data-group-count]').textContent = `${checked}/${total}`;
        });

        this.$root.querySelectorAll(ACTION).forEach((action) => {
            action.closest('[data-chip]').dataset.state = stateOf(action);
        });

        const [checked, total] = syncBox(this.$root, null);
        this.$root.querySelector('[data-counter]').textContent = `${checked} / ${total}`;
    },
});
