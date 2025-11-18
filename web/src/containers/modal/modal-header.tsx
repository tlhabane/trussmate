import React from 'react';

export const ModalHeader: React.FC<React.HTMLProps<HTMLButtonElement>> = ({ children, onClick }) => (
    <header>
        <div className="header-inner">
            <button type="button" onClick={onClick}>
                <i className="custom-icon icon mr-auto" aria-label="Close" />
                <span className="d-none">Close</span>
            </button>
            {children}
        </div>
    </header>
);
