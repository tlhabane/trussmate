import React, { JSX } from 'react';

export const LabeledCard = React.forwardRef<HTMLDivElement, React.HTMLProps<HTMLDivElement>>(
    ({ className, children, style, ...rest }, ref): JSX.Element => (
        <div
            {...rest}
            className={`labeled-card ${className || ''}`}
            style={{ backgroundColor: '#f3f3f4', ...style }}
            ref={ref}
        >
            {children}
        </div>
    ),
);
