import Swal from 'sweetalert2/dist/sweetalert2.esm.js';
import { notify } from './flash';

const FIELDS = 'input[data-req], select[data-req], textarea[data-req]';
const ERROR_CLASS = 'app-client-error';

const text = (key) => document.documentElement.dataset[`v${key}`] ?? '';

const isEmpty = (field) => {
    if (field.type === 'checkbox' || field.type === 'radio') {
        return !field.checked;
    }

    return field.value.trim() === '';
};

const reason = (field) => {
    if (field.hasAttribute('data-req') && isEmpty(field)) {
        return text('Required');
    }

    const state = field.validity;

    if (state.valid || field.value === '') {
        return null;
    }

    if (state.tooShort) return text('Short');
    if (state.tooLong) return text('Long');
    if (state.rangeUnderflow || state.rangeOverflow || state.stepMismatch) return text('Range');

    return text('Invalid');
};

const clear = (field) => {
    field.removeAttribute('aria-invalid');
    field.closest('[data-field], div')?.querySelectorAll(`.${ERROR_CLASS}`).forEach((node) => node.remove());
};

const show = (field, message) => {
    clear(field);
    field.setAttribute('aria-invalid', 'true');

    const note = document.createElement('p');
    note.className = `${ERROR_CLASS} mt-1.5 flex items-center gap-1.5 text-[13px] font-medium text-danger`;
    note.setAttribute('role', 'alert');
    note.textContent = message;

    (field.closest('.relative') ?? field).insertAdjacentElement('afterend', note);
};

const validate = (form) => {
    const invalid = [...form.querySelectorAll('input, select, textarea')]
        .filter((field) => !field.disabled && field.type !== 'hidden')
        .map((field) => ({ field, message: reason(field) }))
        .filter(({ message }) => message !== null);

    form.querySelectorAll('input, select, textarea').forEach(clear);
    invalid.forEach(({ field, message }) => show(field, message));

    return invalid.map(({ field }) => field);
};

export const registerFormGuard = () => {
    document.addEventListener(
        'submit',
        (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement) || form.method.toLowerCase() === 'get') {
                return;
            }

            const invalid = validate(form);

            if (invalid.length === 0) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            invalid.forEach((field) => field.dispatchEvent(new CustomEvent('reveal-field', { bubbles: true })));
            invalid[0].focus({ preventScroll: false });

            notify({ type: 'warning', title: text('Summary') });
        },
        true,
    );

    document.addEventListener('input', (event) => {
        const field = event.target;

        if (field instanceof HTMLElement && field.getAttribute('aria-invalid') === 'true') {
            clear(field);
        }
    });

    let warned = false;

    new MutationObserver((records) => {
        records.forEach((record) => {
            const field = record.target;

            if (!(field instanceof HTMLElement) || !record.attributeName) {
                return;
            }

            const stripped = field.getAttribute(record.attributeName) === null;

            if (record.attributeName === 'required' && stripped && field.hasAttribute('data-req')) {
                field.setAttribute('required', '');
            } else if (record.attributeName === 'data-req' && stripped && record.oldValue !== null) {
                field.setAttribute('data-req', record.oldValue);
                field.setAttribute('required', '');
            } else {
                return;
            }

            if (!warned) {
                warned = true;
                Swal.fire({ icon: 'warning', text: text('Tamper'), confirmButtonText: text('Ok'), buttonsStyling: false, customClass: { popup: 'app-swal', confirmButton: 'app-swal-btn app-swal-confirm-warning' } });
            }
        });
    }).observe(document.documentElement, { subtree: true, attributes: true, attributeOldValue: true, attributeFilter: ['required', 'data-req'] });
};
