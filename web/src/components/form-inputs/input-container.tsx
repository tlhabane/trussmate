import React from 'react';

export const InputContainer: React.FC<React.HTMLProps<HTMLDivElement>> = ({ required, className, children }) => (
    <div className={`form-group form-group-default${required ? ' required' : ''}${className ? ` ${className}` : ''}`}>
        {children}
    </div>
);
