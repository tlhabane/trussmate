import React, { JSX } from 'react';

export const DropdownToggle = React.forwardRef<HTMLButtonElement, React.HTMLProps<HTMLButtonElement>>((props, ref): JSX.Element => {
    const { children, ...rest } = props;
    return (
        <button
            {...rest}
            data-toggle='dropdown'
            aria-haspopup='true'
            aria-expanded='false'
            aria-label='dropdown'
            ref={ref}
            type='button'
        >
            {children}
        </button>
    );
});
