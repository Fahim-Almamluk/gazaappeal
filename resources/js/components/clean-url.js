export function registerCleanUrl() {
    if (!window.location.search || !document.querySelector('[data-clean-url]')) {
        return;
    }

    window.history.replaceState(window.history.state, '', window.location.pathname + window.location.hash);
}
