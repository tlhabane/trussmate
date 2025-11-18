import React, { JSX } from 'react';
import { Button } from '../button';

interface Props extends React.HTMLProps<HTMLButtonElement> {
    loading?: boolean;
}
export const ControlButton: React.FC<Props> = (props) => {
    const { children, className, disabled, loading, style, ...rest } = props;
    
    return (
        <Button
            style={{ height: 52, ...style }}
            className={`btn-default btn-block border-0 tooltip-bottom ${className || ''} ${loading ? 'loading' : ''}`}
            disabled={disabled || loading}
            {...rest}
        >
            <div className="d-flex align-items-center justify-content-center">
                {children}
            </div>
        </Button>
    );
}
