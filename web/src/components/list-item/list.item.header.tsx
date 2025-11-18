import React from 'react';

export const ListItemHeader: React.FC<React.HTMLProps<HTMLDivElement>> = (props) => {
    const { children, className, ...rest } = props;
    
    return (
        <div {...rest} className={`header ${className || ''}`}>
            {children}
        </div>
    );
};
