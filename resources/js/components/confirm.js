import Swal from 'sweetalert2/dist/sweetalert2.esm.js';

const ICONS = { danger: 'warning', warning: 'question', success: 'question', info: 'info' };

const ask = (source) => {
    const { confirmTone: tone = 'danger', confirmTitle, confirmText, confirmOk, confirmCancel } = source.dataset;

    return Swal.fire({
        icon: ICONS[tone] ?? 'question',
        titleText: confirmTitle,
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
            const source = event.submitter?.hasAttribute('data-confirm') ? event.submitter : form;

            if (!(form instanceof HTMLFormElement) || !source.hasAttribute('data-confirm') || form.dataset.confirmed === '1') {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            const result = await ask(source);

            if (result.isConfirmed) {
                form.dataset.confirmed = '1';
                form.requestSubmit(event.submitter ?? undefined);
            }
        },
        true,
    );
};
