import {
    ClickOrTouchEvent,
    ClipboardEvent,
    DragEvent,
    FormEvent,
    FormInputEvent,
    KeyboardEvent,
    InputChangeEvent,
    InputFocusEvent,
    ScrollEvent
} from './events';

export type ClickFn<TElement extends HTMLElement = HTMLElement, TReturn = any> = (
    event: ClickOrTouchEvent<TElement>,
) => TReturn;
export type ClipboardEventFn<TElement extends HTMLElement = HTMLElement, TReturn = any> = (event: ClipboardEvent<TElement>) => TReturn;
export type DragEventFn<TElement extends HTMLElement = HTMLElement, TReturn = any> = (event: DragEvent<TElement>) => TReturn;
export type ButtonClickFn<TReturn = any> = ClickFn<HTMLButtonElement, TReturn>;
export type LinkClickFn<TReturn = any> = ClickFn<HTMLAnchorElement, TReturn>;
export type HTMLElementClickFn<TReturn = any> = ClickFn<HTMLElement, TReturn>;
export type FormSubmitFn<TReturn = any> = (event: FormEvent) => TReturn;
export type FormInputChangeFn<TElement extends HTMLElement = HTMLFormElement, TReturn = any> = (
    event: FormInputEvent<TElement>,
) => TReturn;
export type KeyboardEventFn<TElement extends HTMLElement = HTMLElement, TReturn = any> = (
    event: KeyboardEvent<TElement>,
) => TReturn;
export type InputChangeFn<TElement extends HTMLElement = HTMLInputElement, TReturn = any> = (
    event: InputChangeEvent<TElement>,
) => TReturn;
export type InputFocusFn<TElement extends HTMLElement = HTMLInputElement, TReturn = any> = (
    event: InputFocusEvent<TElement>,
) => TReturn;
export type ScrollEventFn<TElement extends HTMLElement = HTMLElement, TReturn = any> = (event: ScrollEvent<TElement>) => TReturn;
export type SignaturePadChangeFn<TReturn = any> = (name: string, signature: string) => TReturn;
