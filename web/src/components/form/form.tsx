import React from 'react';

export const Form: React.FC<React.HTMLProps<HTMLFormElement>> = ({ children, noValidate = true, ...rest }) => {
    return (
        <form {...rest} noValidate={noValidate}>
            {children}
        </form>
    );
};
