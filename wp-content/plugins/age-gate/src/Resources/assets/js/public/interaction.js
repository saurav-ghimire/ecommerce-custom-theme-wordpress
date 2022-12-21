const preventKey = (event) => {
    console.log(event);
    // metaKey
    // altKey
    if (event.key !== undefined) {
        if (event.key.toLowerCase() === 'f12') {
            event.preventDefault();
        }

        // Mac
        if (event.metaKey && event.altKey && event.key.toLowerCase() === 'dead') {
            event.preventDefault();
        }

        // Windows
        if (event.ctrlKey && event.shiftKey && event.key.toLowerCase() === 'dead') {
            event.preventDefault();
        }

    } else if (event.keyCode !== undefined) {
        // Handle the event with KeyboardEvent.keyCode and set handled true.
        if (event.keyCode === 123) {
            event.preventDefault();
        }

        // Mac
        if (event.metaKey && event.altKey && event.keyCode === 73) {
            event.preventDefault();
        }

        // Windows
        if (event.ctrlKey && event.shiftKey && event.keyCode === 73) {
            event.preventDefault();
        }
    }
}

const preventContext = (event) => {
    event.preventDefault();
}

window.addEventListener('age_gate_shown', () => {
    document.addEventListener('keydown', preventKey);
    document.addEventListener('contextmenu', preventContext);
});

window.addEventListener('age_gate_passed', () => {
    document.removeEventListener('keydown', preventKey);
    document.removeEventListener('contextmenu', preventContext);
});
