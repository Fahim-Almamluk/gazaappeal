import Swal from 'sweetalert2/dist/sweetalert2.esm.js';

const ICONS = { danger: 'warning', warning: 'question', success: 'question', info: 'info' };

const ask = (form) => {
    const { confirmTone: tone = 'danger', confirmTitle, confirmText, confirmOk, confirmCancel } = form.dataset;

    return Swal.fire({
        icon: ICONS[tone] ?? 'question',
        title: confirmTitle,
        text: confirmText,
        showCancelButton: true,
        confirmButtonText: confirmOk,
        cancelButtonText: confirmCancel,
        reverseButtons: false,
        focusCancel: tone === 'danger',
        buttonsStyling: false,
        heightAuto: false,
        returnFocus: true,
        customClass: {
            popup: `app-swal app-swal-${tone}`,
            title: 'app-swal-title',
            htmlContainer: 'app-swal-text',
            actions: 'app-swal-actions',
            confirmButton: `app-swal-btn app-swal-confirm-${tone}`,
            cancelButton: 'app-swal-btn app-swal-cancel',
        },
    });
};

export const registerConfirmations = () => {
    document.addEventListener(
        'submit',
        async (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm') || form.dataset.confirmed === '1') {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            const result = await ask(form);

            if (result.isConfirmed) {
                form.dataset.confirmed = '1';
                form.requestSubmit(event.submitter ?? undefined);
            }
        },
        true,
    );
};
