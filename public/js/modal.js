const modal = document.getElementById('confirmModal');
const modalText = document.getElementById('modalText');
const modalConfirm = document.getElementById('modalConfirm');
const modalCancel = document.getElementById('modalCancel');

let pendingForm = null;

document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', e => {
        e.preventDefault();
        pendingForm = form;
        modalText.textContent = form.dataset.confirm;
        modalConfirm.textContent = form.dataset.confirmLabel || 'Potwierdź';
        modal.showModal();
    });
});

modalCancel.addEventListener('click', () => {
    pendingForm = null;
    modal.close();
});

modalConfirm.addEventListener('click', () => {
    modal.close();
    if (pendingForm) pendingForm.submit();
});

modal.addEventListener('click', e => {
    if (e.target === modal) modal.close();
});