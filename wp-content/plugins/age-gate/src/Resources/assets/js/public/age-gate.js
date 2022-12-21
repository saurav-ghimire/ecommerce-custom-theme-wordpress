import axios from 'axios';
import { Crawler } from 'es6-crawler-detect';
import Cookies from 'js-cookie';
import { fireEvent } from './utility';

const { AgeGateHooks } = window;

const submitter = (form) => {
    form.querySelectorAll('button').forEach((btn) => {
        // btn.addEventListener('touchstart', (e) => form.dataset.btn = e.currentTarget.value);

        btn.addEventListener('click', (e) => form.querySelector('input[name="age_gate[confirm]"]').value = e.currentTarget.value);
    })
}

const submit = async (form, submitter) => {
    const formData = new FormData(form);

    // const confirm = submitter?.value || form.dataset.btn;

    // console.log(confirm);
    // formData.append('age_gate[confirm]', confirm);

    const asString = new URLSearchParams(formData).toString();
    console.log(asString);
    const response = await axios.get(`${uri}?${asString}`).catch(async (err) => {

        const { message } = err;

        formData.append('action', 'ag_check');

        if (fallback) {
            return await axios.post(fallback, formData).catch(err => {

                const { message } = err;

                return {
                    data: {
                        'errors': {
                            'generic': apiError || message
                        },
                        'status': false,
                        'redirect': null,
                        'values': {},
                        'set_cookie': false,
                    }
                };
            });

        } else {
            return {
                data: {
                    'errors': {
                        'generic': apiError || message
                    },
                    'status': false,
                    'redirect': null,
                    'values': {},
                    'set_cookie': false,
                }
            };
        }
    });

    const { data } = response;
    return data;
}


const munged = document.querySelector('[data-ag-munge]');

if (munged) {
    global.age_gate = JSON.parse(window.atob(munged.dataset.agMunge));
}

const {
    age_gate: {
        cookieDomain,
        cookieName,
        age,
        userAgents,
        customTitle,
        viewport,
        rechallenge,
        error: rechallengeError,
        generic: apiError,
        uri,
        fallback,
    },
} = window;

const cookieOptions = {
    domain: cookieDomain,
    path: '/',
    secure: window.location.protocol.match(/https/) ? true : false,
    sameSite: window.location.protocol.match(/https/) ? 'None' : false,
}

const pageTitle = document.title;

if (customTitle) {

    window.addEventListener('age_gate_shown', () => {
        let safeTitle = null;

        if (munged) {
            safeTitle = document.querySelector('.age-gate').dataset?.title;
        }

        document.title = safeTitle || customTitle;
    })

    window.addEventListener('age_gate_passed', () => {
        document.title = pageTitle;
    })
}

if (viewport) {
    // width=device-width, minimum-scale=1, maximum-scale=1
    const metaTag = document.querySelector('meta[name="viewport"]');
    const freshTag = document.createElement('meta');

    let metaValue = null;

    if (metaTag) {
        metaValue = metaTag.content;
    }

    window.addEventListener('age_gate_shown', () => {
        if (metaTag) {
            metaTag.content = 'width=device-width, minimum-scale=1, maximum-scale=1';
        } else {
            freshTag.content = 'width=device-width, minimum-scale=1, maximum-scale=1';
            document.head.appendChild(freshTag);
        }
    })

    window.addEventListener('age_gate_passed', () => {
        if (metaTag) {
            metaTag.content = metaValue;
        } else {
            document.head.removeChild(freshTag);
        }
    })

}

const noRechallenge = (event) => {
    const { type } = event;

    if (
        rechallenge === '' && Cookies.get(`${cookieName}_failed`)
        || rechallenge === '' && type === 'age_gate_failed'
    ) {

        Array.from(document.querySelectorAll('.age-gate__fields, .age-gate-fields, .age-gate__remember-wrapper, .age-gate-remember-wrapper, .age-gate__extra, .age-gate-extra, .age-gate__submit, .age-gate-submit')).forEach(el => el.parentNode.removeChild(el));

        const p = document.createElement('p');
        p.innerHTML = rechallengeError;
        p.className = 'age-gate__error';

        document.querySelector('.age-gate__errors, .age-gate-errors').appendChild(p);
    }
}

window.addEventListener('age_gate_shown', noRechallenge);
window.addEventListener('age_gate_failed', noRechallenge);

const form = () => {
    const form = document.querySelector('.age-gate__form, .age-gate-form');
    submitter(form);
    form.addEventListener('submit', async (e) => {
        console.log('submit');
        e.preventDefault();
        document.body.classList.add('age-restriction--working');


        const data = await submit(e.target, e.submitter);

        const {
            status,
            redirect,
            errors,
            cookieLength,
            transition,
            set_cookie,
        } = data;

        if (cookieLength) {
            Object.assign(cookieOptions, { expires: cookieLength });
        }

        if (status === true) {
            const {
                data: {
                    user_age,
                },

            } = data;


            // localStorage.setItem(`${cookieName}`, user_age);
            if (set_cookie) {
                Cookies.set(cookieName, user_age, cookieOptions);
                Cookies.remove(`${cookieName}_failed`);
            }

            fireEvent('age_gate_passed', data);
            fireEvent('agegatepassed', data);

            hide(transition);

        } else {
            fireEvent('age_gate_failed');
            fireEvent('agegatefailed');

            const messages = `<p class="age-gate__error">${errors[Object.keys(errors).pop()]}</p>`;

            document.querySelector('.age-gate__errors, .age-gate-errors').innerHTML = messages;

            if (set_cookie) {
                Cookies.set(`${cookieName}_failed`, 1, cookieOptions);
            }

            if (redirect) {
                window.location.href = redirect;
            }
        }

        document.body.classList.remove('age-restriction--working');
    });

    fireEvent('age_gate_ready');

}

const bot = () => {
    const CrawlerDetect = new Crawler;


    if (CrawlerDetect.isCrawler(navigator.userAgent)) {
        return true;
    }

    if (userAgents.indexOf(navigator.userAgent) !== -1) {
        return true;
    }

    return false;
}

const show = (force = false) => {

    if (document.body) {

        const { ag_logged_in } = window;
        console.log(force || (Cookies.get(cookieName) || 0) < age && !bot() && !ag_logged_in);
        if (force || (Cookies.get(cookieName) || 0) < age && !bot() && !ag_logged_in) {
            console.log('yes');
        // if ((localStorage.getItem(cookieName) || 0) < age && !bot()) {
            const template = document.getElementById('tmpl-age-gate').innerHTML;
            document.body.insertAdjacentHTML('afterbegin', template);
            document.body.classList.add('age-restriction');
            document.body.parentElement.classList.add('age-gate__restricted');
            document.body.parentElement.classList.add('age-gate__restricted--js');


            fireEvent('age_gate_shown');
            fireEvent('agegateshown');

            form();
        }
    } else {
        setTimeout(show, 1);
    }
}

const hide = (transition = false) => {
    const ageGate = document.querySelector('.age-gate__wrapper, .age-gate-wrapper');

    if (transition) {
        ageGate.addEventListener('transitionend', () => {
            document.body.classList.remove('age-restriction');
            document.body.parentElement.classList.remove('age-gate__restricted');
            document.body.parentElement.classList.remove('age-gate__restricted--js');
            ageGate.parentNode.removeChild(ageGate);

            fireEvent('age_gate_hidden');
            fireEvent('agegatehidden');
        });

        ageGate.classList.add(`age-gate--${transition}`);
    } else {
        document.body.classList.remove('age-restriction');
        document.body.parentElement.classList.remove('age-gate__restricted');
        document.body.parentElement.classList.remove('age-gate__restricted--js');
        ageGate.parentNode.removeChild(ageGate);

        fireEvent('age_gate_hidden');
        fireEvent('agegatehidden');
    }
}


if (AgeGateHooks) {
    console.log('YES!');
} else {
    console.log('No!');
}

if (AgeGateHooks) {
    const { ag_logged_in } = window;
    const shouldShow = AgeGateHooks.applyFilters('age_gate_show', (Cookies.get(cookieName) || 0) < age && !bot() && !ag_logged_in);

    if (shouldShow) {
        setTimeout(() => show(true), AgeGateHooks.applyFilters('age_gate_show_timeout', 1));
    }
} else {
    show();
}

global.age_gate_show = show;
global.age_gate_hide = hide;
