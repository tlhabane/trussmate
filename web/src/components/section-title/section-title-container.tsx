import React, { JSX } from 'react';

export const SectionTitleContainer: React.FC<React.HTMLProps<HTMLDivElement>> = ({ children, className, ...rest}): JSX.Element => (
    <div {...rest} className={`section-title ${className || ''}`}>
        <div className="section-content">
            {children}
        </div>
    </div>
);
