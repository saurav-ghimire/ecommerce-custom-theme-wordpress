import vhCheck from 'vh-check';
import SimpleBar from 'simplebar';
const test = vhCheck();

window.addEventListener('age_gate_shown', () => {

    if (!navigator.cookieEnabled) {
        const {
            cookies,
        } = age_gate_common;

        document.querySelector('.age-gate-form, .age-gate__form').insertAdjacentHTML('afterbegin', `<p class="age-gate__error">${cookies}</p>`);

    }

    const {
        simple,
    } = age_gate_common;

    console.log(simple);

    if (simple) {
        new SimpleBar(document.querySelector('.age-gate'), {});
    }
});
