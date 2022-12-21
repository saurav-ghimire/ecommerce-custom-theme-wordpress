export const fireEvent = (name, data = {}) => {
    const event = new CustomEvent(name, {
        detail: data
    });
    window.dispatchEvent(event);
}
