import { notify } from './flash';

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const send = async (root, items) => {
    try {
        const response = await fetch(root.dataset.url, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
            body: JSON.stringify({ items }),
        });

        if (!response.ok) {
            throw new Error(String(response.status));
        }

        notify({ type: 'success', title: root.dataset.saved });
    } catch {
        notify({ type: 'error', title: root.dataset.failed });
        setTimeout(() => window.location.reload(), 1500);
    }
};

export const sortList = () => ({
    dragged: null,
    before: '',
    queue: Promise.resolve(),

    items() {
        return [...this.$root.children].filter((node) => node.dataset.id !== undefined);
    },

    signature() {
        return this.items()
            .map((node) => node.dataset.id)
            .join(',');
    },

    grab(event) {
        const item = event.target.closest('[data-id]');

        if (item) {
            item.draggable = true;
        }
    },

    start(event) {
        const item = event.target.closest?.('[data-id]');

        if (!item || item.parentElement !== this.$root) {
            return;
        }

        this.dragged = item;
        this.before = this.signature();
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', item.dataset.id);
        requestAnimationFrame(() => (item.dataset.dragging = 'true'));
    },

    over(event) {
        if (!this.dragged || this.dragged.parentElement !== this.$root) {
            return;
        }

        event.preventDefault();

        const target = this.items().find((node) => node.contains(event.target));

        if (!target || target === this.dragged) {
            return;
        }

        const box = (target.querySelector('[data-row]') ?? target).getBoundingClientRect();
        const after = event.clientY > box.top + box.height / 2;

        this.$root.insertBefore(this.dragged, after ? target.nextElementSibling : target);
    },

    drop(event) {
        if (this.dragged) {
            event.preventDefault();
        }
    },

    end(event) {
        if (!this.dragged || event.target !== this.dragged) {
            return;
        }

        const item = this.dragged;
        this.dragged = null;
        item.draggable = false;
        delete item.dataset.dragging;

        if (this.signature() !== this.before) {
            this.save();
        }
    },

    move(event) {
        const item = event.currentTarget.closest('[data-id]');
        const step = Number(event.currentTarget.dataset.step);
        const sibling = step < 0 ? item.previousElementSibling : item.nextElementSibling;

        if (!sibling?.dataset.id) {
            return;
        }

        this.$root.insertBefore(step < 0 ? item : sibling, step < 0 ? sibling : item);
        this.save();
    },

    toggle(event) {
        const item = event.currentTarget.closest('[data-id]');

        item.dataset.visible = String(item.dataset.visible === 'false');
        this.save();
    },

    save() {
        const root = this.$root;
        const items = this.items().map((node) => ({ id: node.dataset.id, visible: node.dataset.visible !== 'false' }));

        window.dispatchEvent(new CustomEvent('sorted', { detail: { group: root.dataset.group, items } }));
        this.queue = this.queue.then(() => send(root, items));
    },
});

export const sortPreview = () => ({
    sync(event) {
        if (event.detail.group !== this.$root.dataset.group) {
            return;
        }

        event.detail.items.forEach(({ id, visible }) => {
            const node = this.$refs.items.querySelector(`[data-id="${CSS.escape(id)}"]`);

            if (node) {
                node.hidden = !visible;
                this.$refs.items.append(node);
            }
        });
    },
});
