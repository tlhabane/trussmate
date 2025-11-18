import React from 'react';

export type ClickOrTouchEvent<T extends HTMLElement> = React.MouseEvent<T> | React.TouchEvent<T>;
export type ClipboardEvent<T extends HTMLElement = HTMLElement> = React.ClipboardEvent<T>;
export type DragEvent<T extends HTMLElement = HTMLElement> = React.DragEvent<T>;
export type KeyboardEvent<T extends HTMLElement = HTMLElement> = React.KeyboardEvent<T>;
export type InputFocusEvent<T = HTMLInputElement | HTMLTextAreaElement> = React.FocusEvent<T>;
export type InputChangeEvent<T = HTMLInputElement | HTMLTextAreaElement> = React.ChangeEvent<T>;
export type FormEvent = React.FormEvent<HTMLFormElement>;
export type FormInputEvent<T = HTMLInputElement | HTMLTextAreaElement> = React.FormEvent<T>;
export type ScrollEvent<T extends HTMLElement = HTMLElement> = React.UIEvent<T>;
