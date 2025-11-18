import React from 'react';

export const MenuContainer: React.FC<React.HTMLProps<HTMLDivElement>> = ({ children, className, ...rest }) => (
    <div {...rest} className={`menu-container ${className || ''}`}>
        <div className="menu">
            {children}
        </div>
    </div>
);
