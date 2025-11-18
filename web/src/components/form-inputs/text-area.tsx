import React, { useEffect, useState } from 'react';
import { InputContainer } from './input-container';
import { autoResizeTextArea, onFocus as handleOnFocus, onBlur as handleOnBlur } from './utils';

interface Props extends React.HTMLProps<HTMLTextAreaElement> {
    error?: string;
    toggleRequiredClass?: boolean;
}

export const TextArea = React.forwardRef<HTMLTextAreaElement, Props>((props, ref) => {
    const { className, defaultValue, id, label, name, onChange, onFocus, onBlur, required, tabIndex, toggleRequiredClass, error, ...rest } = props;
    const [textAreaValue, setTextAreaValue] = useState(defaultValue || '');

    useEffect(() => {
        autoResizeTextArea();
    }, [textAreaValue]);

    const onUpdateTextAreaValue = (event: React.ChangeEvent<HTMLTextAreaElement>) => {
        setTextAreaValue(event.target.value);
        onChange && onChange(event);
    };

    const onBlurReducer = (event: React.FocusEvent<HTMLTextAreaElement>) => {
        handleOnBlur(event);
        if (onBlur) {
            onBlur(event);
        }
    };

    const onFocusReducer = (event: React.FocusEvent<HTMLTextAreaElement>) => {
        handleOnFocus(event);
        if (onFocus) {
            onFocus(event);
        }
    };
    
    const hasError = Boolean(error);
    
    return (
        <>
            <InputContainer
                className={`${(hasError && 'has-error') || ''} ${(toggleRequiredClass && ' toggle-required') || ''}`}
                required={required}
            >
                {label && <label htmlFor={id || name}>{label}</label>}
                <textarea
                    ref={ref}
                    {...rest}
                    id={id || name}
                    name={name}
                    onFocus={onFocusReducer}
                    onBlur={onBlurReducer}
                    onChange={onUpdateTextAreaValue}
                    className={`form-control ${(hasError && 'error') || ''} ${className || ''}`}
                    required={required}
                    tabIndex={tabIndex}
                />
            </InputContainer>
            {hasError && <label className="error">{error}</label>}
        </>
    );
});
