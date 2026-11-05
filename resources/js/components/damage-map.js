export default () => ({
    governorate: '',
    category: '',

    init() {
        this.governorate = this.$root.dataset.governorate;
        this.category = this.$root.dataset.category;
        this.sync();
    },

    select(event) {
        this.governorate = event.currentTarget.dataset.governorate;
        this.sync();
    },

    pick(event) {
        this.category = event.currentTarget.dataset.category;
        this.sync();
    },

    sync() {
        this.$root.querySelectorAll('[data-governorate]').forEach((region) => {
            const active = region.dataset.governorate === this.governorate;
            region.dataset.active = String(active);
            region.setAttribute('aria-pressed', String(active));
        });

        this.$root.querySelectorAll('[data-governorate-panel]').forEach((panel) => {
            panel.hidden = panel.dataset.governoratePanel !== this.governorate;
        });

        this.$root.querySelectorAll('[data-category]').forEach((tab) => {
            tab.setAttribute('aria-selected', String(tab.dataset.category === this.category));
        });

        this.$root.querySelectorAll('[data-category-panel]').forEach((panel) => {
            panel.hidden = panel.dataset.categoryPanel !== this.category;
        });
    },
});
