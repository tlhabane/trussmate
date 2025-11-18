import React from 'react';

export const EmptyListContainer: React.FC<React.HTMLProps<HTMLDivElement>> = ({ children, className, ...rest}) => (
    <div {...rest} className={`p-5 bg-transparent border-0 ${className || ''}`}>
        <div className="d-flex flex-column align-items-center justify-content-center flex-fill">{children}</div>
    </div>
)
