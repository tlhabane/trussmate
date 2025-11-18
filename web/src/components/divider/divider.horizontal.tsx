import React from 'react';

export const DividerHorizontal: React.FC<React.HTMLProps<HTMLDivElement>> = ({ children, className, ...rest }) => (
    <div
        {...rest}
        className={`rs-divider rs-divider-horizontal rs-divider-with-text border-light ${className || ''}`}
    >
        {children && <span className="rs-divider-inner-text">{children}</span>}
    </div>
);
