import React from 'react';

export const ListItemContainer: React.FC<React.HTMLProps<HTMLDivElement>> = (props) => {
    const { children, className, ...rest } = props;
    return (
        <div {...rest} className={`data-list-item ${className || ''}`}>
            {children}
        </div>
    );
};
