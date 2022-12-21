import { on } from '../utility/on';

export class LinkPicker {
    constructor() {
        this.modal = document.getElementById('wp-link-wrap');

        if (!this.modal) {
            return;
        }

        this.modal.classList.add('ag_link-modal');
        this.select = this.modal.querySelector('#wp-link-submit');

        this.select.addEventListener('click', this.update);

        Array.from(document.querySelectorAll('.button--link')).forEach((button) => {
            button.addEventListener('click', this.open);
        });


        on('#wpwrap', 'click', '.ag-field--link .button--remove', this.clear);
    }

    open = (e) => {
        this.input = e.target.parentNode.querySelector('input');
        wpLink.open('wpwrap');
    }

    update = (e) => {
        const { href } = wpLink.getAttrs();
        this.input.value = href;

        let display = this.input.parentNode.querySelector('.link-display');

        if (display) {
            display.textContent = href;
        } else {
            display = document.createElement('span');
            display.className = 'link-display';
            display.textContent = href;
            this.input.insertAdjacentElement('afterend', display);
        }


        if (href) {
            this.appendButton(e.target);
        }

        return true;
    }

    appendButton = () => {
        if (!this.input.parentNode.querySelector('.button--remove')) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'button button--remove';
            button.textContent = 'Remove link';

            // this.input.parentNode.appendChild(button);

            this.input.insertAdjacentElement('beforebegin', button);
        }
    }

    clear = (e) => {
        e.target.parentNode.querySelector('input').value = '';

        e.target.parentNode.removeChild(e.target.parentNode.querySelector('.link-display'));
        e.target.parentNode.removeChild(e.target);
    }
};
