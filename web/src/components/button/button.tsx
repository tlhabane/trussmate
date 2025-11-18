import React, { JSX } from 'react';
import './styles.scss';

interface Props extends React.HTMLProps<HTMLButtonElement> {
    loading?: boolean;
}

type ButtonType = 'button' | 'submit' | 'reset' | undefined;

export const Button = React.forwardRef<HTMLButtonElement, Props>(
    ({ children, className = '', disabled = false, loading = false, type = 'button', ...props }, ref): JSX.Element => (
        <button
            {...props}
            type={(type || 'button') as ButtonType}
            className={`btn ${className} ${loading && 'loading'}`}
            disabled={disabled || loading}
            ref={ref}
        >
            <span style={{ display: 'none', visibility: 'hidden' }}>Fake button text</span>
            <div className='btn-content'>{children}</div>
        </button>
    ),
);
