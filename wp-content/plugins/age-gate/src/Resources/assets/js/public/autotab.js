
// const method = [...document.body.parentNode.classList].some(r => ['age-gate-restricted-standard', 'age-gate__restricted--standard'].indexOf(r) >= 0) ? 'standard' : 'js';

// console.log(method);
// const method = document.body.parentNode.classList.contains('age-gate__restricted--standard') ? 'standard' : 'js';


const autotab = () => {
    const inputs = Array.from(document.querySelectorAll('.age-gate-form-elements input, .age-gate__form-elements input'));
    let current = 0;

    if (inputs) {
        const region = document.querySelector('.age-gate__region');

        if (region) {
            region.addEventListener('change', () => {
                if (region.value) {
                    document.querySelector('.age-gate__button, .age-gate-button').focus()
                }
            });
        }

        inputs.forEach((input, idx) => {
            input.addEventListener('keyup', (e) => {
                if (e.target.value.length >= e.target.maxLength) {
                    if ((idx) !== inputs.length - 1) {
                        const next = inputs[current + 1];
                        next.focus();
                        current = current + 1;
                    } else if (document.querySelector('.age-gate__region')) {
                        document.querySelector('.age-gate__region').focus();
                    } else {
                        document.querySelector('.age-gate__button, .age-gate-button').focus()
                    }
                }
            });
        })
    }

}



window.addEventListener('age_gate_shown', () => {
    autotab();
});

