import React from 'react';
import { createRoot } from 'react-dom/client';
import Gallery from './react/gallery';

class Modal {

    constructor() {
        this.triggers = document.querySelectorAll('[data-modal]');
        this.close = document.querySelectorAll('.js-close-modal');
        this.modals = document.querySelectorAll('.modal');
        this.modalInners = document.querySelectorAll('.modal-inner');
        this.select = document.querySelectorAll('.modal .button-primary');
        this.clearButtons = document.querySelectorAll('.ag-media-clear');
        this.selected = false;

        this.listeners();
    }

    listeners() {
        window.addEventListener('keydown', this.keyDown);

        this.triggers.forEach(el => {
            el.addEventListener('click', this.openModal, false);
        });

        this.modals.forEach(el => {
            el.addEventListener('transitionend', this.revealModal, false);
            el.addEventListener('click', this.backdropClose, false);
        });

        this.close.forEach(el => {
            el.addEventListener('click', Modal.hideModal, false);
        });

        this.modalInners.forEach(el => {
            el.addEventListener('transitionend', this.closeModal, false);
        });

        this.select.forEach(el => {
            el.addEventListener('click', this.choose, false);
        });

        this.clearButtons.forEach(el => {
            el.addEventListener('click', this.clear, false);
        });
    }

    clear = (e) => {
        const root = e.currentTarget.parentNode;
        root.querySelector('input').value = '';
        root.querySelector('.ag-preview').innerHTML = '';
    }

    keyDown = (e) => {
        if (27 === e.keyCode && document.body.classList.contains('modal-body')) {
            Modal.hideModal();
        }
    }

    backdropClose = (el) => {
        if (!el.target.classList.contains('modal-visible')) {
            return;
        }

        let backdrop = el.currentTarget.dataset.backdrop !== undefined ? el.currentTarget.dataset.backdrop : true;

        if (backdrop === true) {
            Modal.hideModal();
        }
    }

    choose = () => {
        const root = this.selected.parentNode;
        const preview = root.querySelector('.ag-preview');
        const checked = document.querySelector('#ag-media-gallery input:checked');


        preview.innerHTML = '';

        if (!checked) {
            root.querySelector('input').value = '';
        } else {
            root.querySelector('input').value = checked.value;
            const img = document.createElement(checked.dataset.type.match(/video/) ? 'video' : 'img');

            img.src = checked.dataset.full;

            preview.appendChild(img);

        }

        Modal.hideModal();
    }

    static hideModal = () => {
        let modalOpen = document.querySelector('.modal.modal-visible');

        modalOpen.querySelector('.modal-inner').classList.remove('modal-reveal');
        document.querySelector('.modal-body').addEventListener('transitionend', Modal.modalBody, false);
        document.body.classList.add('modal-fadeOut');
    }

    closeModal = (el) => {
        if ('opacity' === el.propertyName && !el.target.classList.contains('modal-reveal')) {
            document.querySelector('.modal.modal-visible').classList.remove('modal-visible');
        }
    }

    openModal = (el) => {
        if (!el.currentTarget.dataset.modal) {
            console.error('No data-modal attribute defined!');
            return;
        }

        window.agPreSelected = el.currentTarget.parentNode.querySelector('input').value;
        this.selected = el.currentTarget;

        console.log(this.selected);

        const modalID = el.currentTarget.dataset.modal;
        const modal = document.getElementById(modalID);

        document.body.classList.add('modal-body');
        modal.classList.add('modal-visible');

        if (!modal.classList.contains('modal-init')) {
            modal.classList.add('modal-init')
            const wrapper = document.getElementById('ag-media-gallery');
            const root = createRoot(wrapper);
            root.render(<Gallery />);
        }

        const event = new Event('ag_gallery_update');
        window.dispatchEvent(event);
    }

    revealModal = (el) => {
        if ('opacity' === el.propertyName && el.target.classList.contains('modal-visible')) {
            el.target.querySelector('.modal-inner').classList.add('modal-reveal');
        }
    }

    static modalBody = (el) => {
        if ('opacity' === el.propertyName && el.target.classList.contains('modal') && !el.target.classList.contains('modal-visible')) {
            document.body.classList.remove('modal-body', 'modal-fadeOut');
        }
    }

}

export default Modal;
