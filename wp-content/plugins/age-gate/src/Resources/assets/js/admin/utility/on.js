export const on = (selector, eventType, childSelector, eventHandler) => {
    const elements = Array.from(document.querySelectorAll(selector));
    elements.forEach(element => {
        element.addEventListener(eventType, eventOnElement => {
            if (eventOnElement.target.matches(childSelector)) {
                eventHandler(eventOnElement)
            }
        })
    });
}
