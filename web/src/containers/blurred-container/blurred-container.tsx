import React from 'react';

export const BlurredContainer: React.FC<React.HTMLProps<HTMLDivElement>> = ({ children, className, ...rest }) => (
    <div {...rest} className={`blurred-container ${className || ''}`}>
        {children}
    </div>
);
