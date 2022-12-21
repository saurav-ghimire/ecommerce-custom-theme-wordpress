import debounce from 'lodash.debounce';

const menu = document.querySelector('.age-gate-toolbar');
const HIDDEN_CLASS = 'age-gate-toolbar__extra--hidden'
const BUTTON_HIDDEN_CLASS = 'age-gate-toolbar__button--hidden';

if (menu) {
    const button = menu.querySelector('.age-gate-toolbar__button');
    const links = menu.querySelector('.age-gate-toolbar__tabs');
    const extra = menu.querySelector('.age-gate-toolbar__extra');
    const title = menu.querySelector('.age-gate-toolbar__title');

    let numOfItems = 0;
    let totalSpace = 0;
    const breakWidths = [];

    Array.from(links.children).forEach((item) => {
        totalSpace = totalSpace + (item.clientWidth + 10);
        numOfItems += 1;
        breakWidths.push(totalSpace);
    });

    // console.log(numOfItems, totalSpace, breakWidths);

    let availableSpace, numOfVisibleItems, requiredSpace;

    const check = () => {

        availableSpace = menu.clientWidth - (title.clientWidth + 10) - 20 - button.clientWidth;
        console.log(availableSpace);
        console.log(breakWidths);

        numOfVisibleItems = links.children.length;
        requiredSpace = links.clientWidth + (title.clientWidth + 10) + 20;

        console.log(requiredSpace > breakWidths[numOfVisibleItems - 1], requiredSpace, breakWidths[numOfVisibleItems - 1]);
        if (requiredSpace > breakWidths[numOfVisibleItems - 1] && links.children.length) {
            const eat = Array.from(links.children)[links.children.length - 1];

            if (eat) {
                extra.insertAdjacentElement('afterbegin', eat);
            }
            numOfVisibleItems -= 1;

            check();
        } else if (availableSpace > requiredSpace && extra.children.length) {
            const spit = Array.from(extra.children)[0];

            if (spit) {
                links.insertAdjacentElement('beforeend', spit);
            }
            numOfVisibleItems += 1;
        }

        button.dataset.count = numOfItems - numOfVisibleItems;

        if (numOfVisibleItems === numOfItems) {
            button.classList.add(BUTTON_HIDDEN_CLASS);
        } else {
            button.classList.remove(BUTTON_HIDDEN_CLASS);
        }
    }

    window.addEventListener('resize', debounce(check, 100));

    button.addEventListener('click', e => extra.classList.toggle(HIDDEN_CLASS));

    window.addEventListener('DOMContentLoaded', check);
}
