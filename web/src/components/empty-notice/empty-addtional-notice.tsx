import React from 'react';
import { DividerHorizontal } from '../divider';

export const EmptyAdditionalNotice: React.FC<React.HTMLProps<HTMLDivElement>> = ({ children }) => (
    <>
        <div style={{ width: '100%' }}>
            <DividerHorizontal>OR</DividerHorizontal>
        </div>
        <p className="small text-center">{children}</p>
    </>
)
