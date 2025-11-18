import React from 'react';

export const ListItemBody: React.FC<React.HTMLProps<HTMLDivElement>> = (props) => {
    const { children, className, ...rest } = props;
    
    return (
        <div {...rest} className={`body ${className || ''}`}>
            {children}
        </div>
    );
};
