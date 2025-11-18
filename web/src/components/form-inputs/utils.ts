import React from 'react';

type SupportedElementTypes = HTMLInputElement | HTMLTextAreaElement;

export const onFocus = (event: React.FocusEvent<SupportedElementTypes>) => {
    event.target?.offsetParent?.classList.add('focused');
};
export const onBlur = (event: React.FocusEvent<SupportedElementTypes>) => {
    event.target?.offsetParent?.classList.remove('focused');
};

/** Add one or more listeners to an element
 ** @param {DOMElement} element - DOM element to add listeners to
 ** @param {string} eventNames - space separated list of event names, e.g. 'click change'
 ** @param {Function} listener - function to attach for each event as a listener
 */
const addMultipleEventListener = (
    element: HTMLTextAreaElement,
    eventNames: string,
    listener: (event: unknown) => void,
) => {
    const events = eventNames.split(' ');
    // eslint-disable-next-line no-plusplus
    for (let i = 0, iLen = events.length; i < iLen; i++) {
        element.addEventListener(events[i], listener, false);
    }
};

export const autoResizeTextArea = () => {
    document.querySelectorAll('[data-autoresize]').forEach((element) => {
        const textAreaElement = element as HTMLTextAreaElement;
        textAreaElement.style.boxSizing = 'border-box';
        const offset = textAreaElement.offsetHeight - element.clientHeight;

        addMultipleEventListener(
            textAreaElement,
            'input change propertychange keydown keyup paste cut',
            (event: unknown) => {
                // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                // @ts-ignore
                // eslint-disable-next-line no-param-reassign
                event.target.style.height = 'auto';
                // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                // @ts-ignore
                // eslint-disable-next-line no-param-reassign
                event.target.style.height = `${event.target.scrollHeight + offset}px`;
            },
        );

        textAreaElement.removeAttribute('data-autoresize');
    });
};
