import React, { JSX } from 'react';

export const SectionTitleColumn: React.FC<React.HTMLProps<HTMLDivElement>> = ({ children, className, ...rest }): JSX.Element => (
    <div {...rest} className={`section-column ${className || ''}`}>
        {children}
    </div>
);
