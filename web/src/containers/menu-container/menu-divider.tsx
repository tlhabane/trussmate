import React from 'react';

export const MenuDivider: React.FC<React.HTMLProps<HTMLDivElement>> = ({ className, ...rest }) => (
    <div {...rest} className={`menu-divider ${className || ''}`} />
);
