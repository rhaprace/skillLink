const Modal = {
    show(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.warn(`Modal with ID "${modalId}" not found`);
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        modal.setAttribute('aria-hidden', 'false');

        const firstInput = modal.querySelector('input, button, textarea, select');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    },

    hide(modalId, event = null) {
        if (event && event.target.id !== modalId) {
            return;
        }

        const modal = document.getElementById(modalId);
        if (!modal) {
            console.warn(`Modal with ID "${modalId}" not found`);
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
        modal.setAttribute('aria-hidden', 'true');

        const forms = modal.querySelectorAll('form');
        forms.forEach(form => form.reset());
    },

    toggle(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.warn(`Modal with ID "${modalId}" not found`);
            return;
        }

        if (modal.classList.contains('hidden')) {
            this.show(modalId);
        } else {
            this.hide(modalId);
        }
    },

    init() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
                openModals.forEach(modal => {
                    this.hide(modal.id);
                });
            }
        });
    }
}

function showDeleteModal(event) {
    if (event) event.preventDefault();
    Modal.show('deleteModal');
}

function hideDeleteModal(event) {
    Modal.hide('deleteModal', event);
}

document.addEventListener('DOMContentLoaded', () => {
    Modal.init();
});
