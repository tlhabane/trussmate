import React from 'react';

export type FormInput<T = string> = {
    value: T;
    defaultValue?: T;
    error: string;
    type?: 'date' | 'text' | 'email' | 'tel' | 'password' | 'checkbox' | 'radio' | 'select' | 'signature' | 'textarea' | string;
    label?: string;
    placeholder?: string;
    required?: boolean;
    options?: Array<{ label: string; value: any }>; // used for select, radio
    multiple?: boolean; // for multi-select
    loading?: boolean; // for async select options
    disabled?: boolean;
    style?: React.CSSProperties;
    className?: string;
    validation?: {
        pattern?: RegExp;
        errorMessage?: string;
        custom?: (value: T) => boolean;
    };
};

export type FormState<T extends Record<string, any>> = {
    [K in keyof T]: FormInput<T[K]>;
};
