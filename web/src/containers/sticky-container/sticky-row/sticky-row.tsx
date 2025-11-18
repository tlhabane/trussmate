import React from 'react';

interface Props extends React.HTMLProps<HTMLDivElement> {
    fixed?: boolean;
}

export const StickyRow: React.FC<Props> = ({ children, className, fixed = false, ...rest }) => (
    <div {...rest} className={`row-wrapper${fixed ? ' fixed' : ''} ${className || ''}`}>{children}</div>
);
