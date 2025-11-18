import React from 'react';

type Props = React.HTMLProps<HTMLDivElement>;

export const FullPageSpinner: React.FC<Props> = ({ children, className, style }) => (
    <div className={`progress-wrapper ${className || ''}`} style={{ ...style }}>
        <div className="spinner-container">
            <div style={{ paddingTop: 100 }} className="spinner-text small-text">
                {children || 'Loading...'}
            </div>
        </div>
    </div>
);
