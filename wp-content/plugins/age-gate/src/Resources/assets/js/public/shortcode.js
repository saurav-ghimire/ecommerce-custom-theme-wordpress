import axios from 'axios';
import { Crawler } from 'es6-crawler-detect';
import Cookies from 'js-cookie';

const shortcodes = Array.from(document.querySelectorAll('.age-gate-shortcode-js'));

const replace = (element, content) => {
    const markup = atob(content);
    const parser = new DOMParser();
    const doc = parser.parseFromString(markup, 'text/html');
    element.parentNode.replaceChild(doc.body.firstChild, element);
    // const slider = doc.body.querySelector('.selector__compare');
}

const submit = async (form, submitter) => {

    const formData = new FormData(form);
    formData.append('age_gate[confirm]', submitter.value);

    const asString = new URLSearchParams(formData).toString();
    const response = await axios.get(`/wp-json/age-gate/v3/check?${asString}`);

    const { data } = response;
    return data;
}

const form = (el, content, cookieName, cookieDomain) => {
    const form = el.querySelector('script').textContent;

    el.insertAdjacentHTML('beforeend', form);
    el.querySelector('form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = await submit(e.target, e.submitter);

        const { status } = data;

        if (status !== false) {
            const { data: { user_age } } = data;
            Cookies.set(cookieName, user_age, {
                domain: cookieDomain,
                path: '/',
                secure: true,
                sameSite: 'None',
            });
            replace(el, content);

            const ev = new CustomEvent('age_gate_sc', { detail: { age: user_age }} );
            window.dispatchEvent(ev);
        } else {
            const { errors } = data;

            const messages = `<p class="age-gate__error">${errors[Object.keys(errors).pop()]}</p>`;

            el.querySelector('.age-gate__errors, .age-gate-errors').innerHTML = messages;

            // localStorage.setItem(`${cookieName}_failed`, 1);
            Cookies.set(`${cookieName}_failed`, 1, cookieOptions);
        }
    });
}

window.addEventListener('age_gate_sc', ({ detail: { age } }) => {
    // get them again as orginal const may not exists now
    Array.from(document.querySelectorAll('.age-gate-shortcode-js')).forEach((gate) => {
        const {
            dataset: {
                agegate: content,
                data
            }
        } = gate;

        const settings = JSON.parse(atob(data));
        const { age: required } = settings;

        if (age >= required) {
            return replace(gate, content);
        }
    });
});

shortcodes.forEach((gate) => {

    const c = new Crawler;
    const {
        dataset: {
            agegate: content,
            data
        }
    } = gate;

    if (c.isCrawler(navigator.userAgent)) {
        return replace(gate, content);
    }

    const settings = JSON.parse(atob(data));

    const { age, cookieName, cookieDomain } = settings;

    if (Cookies.get(cookieName) >= age) {

        return replace(gate, content);
    }


    form(gate, content, cookieName, cookieDomain);
});
