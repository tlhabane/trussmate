import React from 'react';

export const DividerVertical: React.FC<React.HTMLProps<HTMLDivElement>> = ({ children, className, ...rest }) => (
    <div {...rest} className={`vertical-divider ${className || ''}`}>
        {children && <div className="center-element">{children}</div>}
    </div>
);
