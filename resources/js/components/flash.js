import Swal from 'sweetalert2/dist/sweetalert2.esm.js';

const TYPES = ['success', 'error', 'warning', 'info'];
const TIMEOUTS = { success: 4500, info: 5000, warning: 7000, error: 8000 };
const MAX_DETAILS = 3;

export const notify = ({ type, title, lines = [] }) => {
    const details = document.createElement('ul');
    details.className = 'app-flash-list';

    lines.forEach((line) => {
        const item = document.createElement('li');
        item.textContent = line;
        details.append(item);
    });

    return Swal.fire({
        toast: true,
        position: 'top',
        icon: type,
        title,
        html: lines.length > 0 ? details : undefined,
        showConfirmButton: false,
        showCloseButton: true,
        timer: TIMEOUTS[type],
        timerProgressBar: true,
        customClass: { popup: 'app-swal-toast' },
    });
};

const parse = (source) => {
    try {
        const payload = JSON.parse(source.textContent ?? '');

        if (!TYPES.includes(payload?.type) || typeof payload.title !== 'string') {
            return null;
        }

        const details = Array.isArray(payload.details) ? payload.details.map((entry) => String(entry?.text ?? '')).filter(Boolean) : [];

        return { type: payload.type, title: payload.title, details };
    } catch {
        return null;
    }
};

const inlineMessages = () => new Set([...document.querySelectorAll('[role="alert"]')].map((node) => node.textContent.trim()));

export const focusFirstInvalid = () => {
    const field = document.querySelector('[aria-invalid="true"]');

    if (field instanceof HTMLElement) {
        field.dispatchEvent(new CustomEvent('reveal-field', { bubbles: true }));
        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
        field.focus({ preventScroll: true });
    }
};

export const registerFlash = () => {
    const source = document.querySelector('script[data-flash]');

    if (!source) {
        return;
    }

    const payload = parse(source);
    source.remove();

    if (payload === null) {
        return;
    }

    const shown = inlineMessages();
    const lines = payload.details.filter((text) => !shown.has(text)).slice(0, MAX_DETAILS);

    notify({ type: payload.type, title: payload.title, lines });

    if (payload.details.length > 0) {
        focusFirstInvalid();
    }
};
