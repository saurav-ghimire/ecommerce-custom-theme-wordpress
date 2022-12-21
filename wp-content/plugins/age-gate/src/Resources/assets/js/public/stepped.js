// TODO STEPPED INPUT
const { age } = window.ag_stepped;

class AgeGateSteps {
    constructor() {
        const date = new Date();
        date.setFullYear(date.getFullYear() - age);
        const dateTime = date.toISOString().split('T').slice(0, 1)[0].split('-');

        // elements
        this.form = document.querySelector('.age-gate-form, .age-gate__form');

        if (('requestSubmit' in this.form) === false) {
            return;
        }

        this.passed = false;

        this.inputDay = this.form.querySelector('[name="age_gate[d]"]');
        this.inputMonth = this.form.querySelector('[name="age_gate[m]"]');
        this.inputYear = this.form.querySelector('[name="age_gate[y]"]');
        this.inputSubmit = this.form.querySelector('.age-gate-submit, .age-gate__submit');
        this.elements = this.form.querySelector('.age-gate-form-elements, .age-gate__form-elements');
        this.wrapper = this.form.parentNode;

        this.inputDay.tabIndex = '-1';
        this.inputMonth.tabIndex = '-1';

        // values
        // values
        this.current = 'y';
        this.userYear = 0;
        this.userMonth = 0;
        this.userDay = 0;
        this.minYear = parseInt(dateTime[0]);
        this.minMonth = parseInt(dateTime[1]);
        this.minDay = parseInt(dateTime[2]);

        this.init();
    }

    init = () => {
        console.log('bindings')
        this.form.setAttribute('novalidate', true);

        // hide elements
        this.inputSubmit.style.display = 'none';

        this.wrapper.classList.add('age-gate--stepped');

        // events
        this.inputYear.addEventListener('keyup', this.handleYear);
        this.inputMonth.addEventListener('keyup', this.handleMonth);
    }

    handleYear = (e) => {
        if (this.inputYear.value.length === this.inputYear.maxLength) {
            if (parseInt(this.inputYear.value) !== this.minYear) {
                if (!this.passed) {
                    this.passed = true;
                    this.inputDay.value = '01';
                    this.inputMonth.value = '01';
                    this.form.requestSubmit();
                }
            } else {
                this.inputMonth.value = '';
                this.inputDay.value = '';
                this.inputMonth.focus();
            }
        }
    }

    handleMonth = (e) => {
        if (this.inputMonth.value.length === this.inputMonth.maxLength) {
            if (this.inputMonth.value < this.minMonth) {
                console.log('wibble');
                this.inputDay.value = '01';

                if (!this.passed) {
                    this.form.requestSubmit();
                }
            } else {
                // this.inputDay.parentNode.style.display = 'block';
                // this.inputYear.parentNode.style.display = 'block';
                this.inputSubmit.style.display = '';
                this.wrapper.classList.remove('age-gate--stepped');


                this.inputYear.removeEventListener('keyup', this.handleYear);
                this.inputMonth.removeEventListener('keyup', this.handleMonth);

                this.inputDay.value = '';
                this.inputDay.focus();
            }
        }
    }

}


window.addEventListener('age_gate_shown', () => {
    new AgeGateSteps
});

// console.log(age);
