const ACCORDIONS = Array.from(document.querySelectorAll('.ag-accordion__trigger'));
const ACTIVE_CLASS = 'ag-accordion__trigger--active'

const toggle = (el) => {
    ACCORDIONS.forEach((h) => {
        if (h !== el && h.parentElement === el.parentElement) {
            h.classList.remove(ACTIVE_CLASS);
            h.nextElementSibling.style.maxHeight = 0;
        }
    });

    const panel = el.nextElementSibling;

    if (el.classList.contains(ACTIVE_CLASS)) {
        el.classList.remove(ACTIVE_CLASS)
        panel.style.maxHeight = null;
    } else {
        el.classList.add(ACTIVE_CLASS)
        panel.style.maxHeight = `${panel.scrollHeight}px`;
    }
}

export const accordion = () => {
    ACCORDIONS.forEach((header) => {
        header.addEventListener('click', (e) => {


            toggle(e.currentTarget);



        });
    });
}
