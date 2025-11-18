import React, { JSX } from 'react';

export const SectionTitleLabel: React.FC<React.HTMLProps<HTMLSpanElement>> = ({ children, className, ...rest }): JSX.Element => (
    <span {...rest} className={`section-label ${className || ''}`}>
        {children}
    </span>
);
