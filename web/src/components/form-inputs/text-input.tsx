import React, { useState } from 'react';
import { InputContainer } from './input-container';
import { PasswordInputIcon } from './password-input-icon';
import { onFocus as handleOnFocus, onBlur as handleOnBlur } from './utils';

interface TogglePasswordVisibilityProps extends React.HTMLProps<HTMLAnchorElement> {
    showPassword: boolean;
}
const { HidePassword, ShowPassword } = PasswordInputIcon;
const TogglePasswordVisibility: React.FC<TogglePasswordVisibilityProps> = ({ onClick, showPassword }) => (
    <div className="toggle-show-password">
        <a
            href="/pswt-basic/src/pages"
            onClick={onClick}
            title={`${showPassword ? 'Hide' : 'Show'} password`}
            aria-label="Show or hide password"
        >
            <img src={showPassword ? HidePassword : ShowPassword} alt="Toggle password visibility" />
        </a>
    </div>
);

interface Props extends React.HTMLProps<HTMLInputElement> {
    error?: string;
    toggleRequiredClass?: boolean;
}

export const TextInput = React.forwardRef<HTMLInputElement, Props>((props, ref) => {
    const { id, type, required, label, name, className, onBlur, onFocus, tabIndex, toggleRequiredClass, error } = props;

    const onBlurReducer = (event: React.FocusEvent<HTMLInputElement>) => {
        handleOnBlur(event);
        if (onBlur) {
            onBlur(event);
        }
    };

    const onFocusReducer = (event: React.FocusEvent<HTMLInputElement>) => {
        handleOnFocus(event);
        if (onFocus) {
            onFocus(event);
        }
    };

    const [showPassword, setShowPassword] = useState(false);
    const toggleShowPassword = (event: React.MouseEvent<HTMLAnchorElement>) => {
        event.preventDefault();
        setShowPassword((passwordVisible) => !passwordVisible);
    };

    const hasError = Boolean(error);
    return (
        <>
            <InputContainer
                className={`${(hasError && 'has-error') || ''} ${(toggleRequiredClass && ' toggle-required') || ''}`}
                required={required}
            >
                {label && <label htmlFor={id || name}>{label}</label>}
                <input
                    ref={ref}
                    {...props}
                    id={id || name}
                    tabIndex={tabIndex}
                    type={showPassword ? 'text' : type || 'text'}
                    name={name}
                    onFocus={onFocusReducer}
                    onBlur={onBlurReducer}
                    required={required}
                    className={`form-control ${(hasError && 'error') || ''} ${className || ''}`}
                />
                {type === 'password' ? (
                    <TogglePasswordVisibility onClick={toggleShowPassword} showPassword={showPassword} />
                ) : null}
            </InputContainer>
            {hasError && <label className="error">{error}</label>}
        </>
    );
});
