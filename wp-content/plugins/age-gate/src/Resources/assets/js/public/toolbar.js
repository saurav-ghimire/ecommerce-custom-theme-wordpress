import Cookies from "js-cookie";
import { on } from '../admin/utility/on';

window.addEventListener('DOMContentLoaded', () => {

    on('#wp-admin-bar-age-gate-toggle', 'click', '.ab-item', (e) => {
        e.preventDefault();

        const { ag_cookie_domain, ag_cookie_name } = window;

        console.log(ag_cookie_domain, ag_cookie_name);
        if (Cookies.get(ag_cookie_name)) {
            const data = new FormData;
            data.append('action', 'ag_clear_cookie');

            Cookies.set(ag_cookie_name, 1, {
                path: '/',
                domain: ag_cookie_domain,
                expires: -1,
                secure: window.location.protocol.match(/https/) ? true : false,
                sameSite: window.location.protocol.match(/https/) ? 'None' : false,
            });
        } else {
            Cookies.set(ag_cookie_name, '99', {
                path: '/',
                domain: ag_cookie_domain,
                secure: window.location.protocol.match(/https/) ? true : false,
                sameSite: window.location.protocol.match(/https/) ? 'None' : false,

            });
        }

        window.location.reload();
    });
});
