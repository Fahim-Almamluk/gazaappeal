const STORAGE_KEY = 'gaza.sidebar.collapsed';

function readCollapsed() {
    try {
        return localStorage.getItem(STORAGE_KEY) === '1';
    } catch {
        return false;
    }
}

export default () => ({
    collapsed: readCollapsed(),
    mobileOpen: false,

    toggleCollapse() {
        this.collapsed = !this.collapsed;
        try {
            localStorage.setItem(STORAGE_KEY, this.collapsed ? '1' : '0');
        } catch {}
    },

    openMobile() {
        this.mobileOpen = true;
    },

    closeMobile() {
        this.mobileOpen = false;
    },

    get expanded() {
        return !this.collapsed;
    },
});
