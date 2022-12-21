import * as focusTrap from 'focus-trap';

class AgeGateTrap {
    constructor()
    {
        const { elements, focus } = agfocus;

        this.elements = elements;
        this.focus = focus;

        this.init();
    }

    init = () => {
        if (!this.elements.length) {
            return;
        }

        console.log (this.focus);

        window.addEventListener('age_gate_shown', () => {
            const options = {
                escapeDeactivates: false,
            };

            if (this.focus) {
                Object.assign(options, { initialFocus: `[name="${this.focus}"]` });
            }

            this.trap = focusTrap.createFocusTrap(this.elements, options);

            this.trap.activate();
        });

        window.addEventListener('age_gate_passed', () => {
            this.trap.deactivate();
        });

    }
}

new AgeGateTrap;
