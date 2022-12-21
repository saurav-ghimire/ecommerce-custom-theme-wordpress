import { on } from './utility/on';

window.addEventListener('DOMContentLoaded', () => {
    on('#the-list', 'click', '.age-gate-enable-update', (e) => {
        if (confirm('I understand this is a breaking change and have backed up my settings')) {
            const link = document.querySelector('#age-gate-update .update-link');
            if (link) {
                link.style.pointerEvents = 'initial';
                link.style.opacity = 1;
                link.style.cursor = 'pointer';
            }
        }
    });
});

