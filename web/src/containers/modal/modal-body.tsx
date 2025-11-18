import React from 'react';

export const ModalBody: React.FC<React.HTMLProps<HTMLDivElement>> = ({ className, children, ...rest }) => (
    <div {...rest} className={`modal-body ${className || ''}`} >
        {children}
    </div>
);
