import React, { JSX } from 'react';

interface Props extends React.HTMLProps<HTMLDivElement> {
    position?: 'top' | 'bottom';
}

export const CardLabel: React.FC<Props> = ({ children, className, position = 'top', style, ...rest }): JSX.Element => {
    const positionClass = position === 'top' ? 'card-label' : 'bottom-card-label';
    
    return (
        <div
            {...rest}
            style={{ backgroundColor: '#f3f3f4', ...style }}
            className={`${positionClass} ${className || ''}`}
        >
            {children}
        </div>
    );
};
