import React from 'react';
import { FullPageSpinner } from './full-page-spinner';

type Props = React.HTMLProps<HTMLDivElement>;

export const ContainerSpinner: React.FC<Props> = ({ children, className, style }) => (
    <FullPageSpinner style={{ zIndex: 1, minHeight: 0, height: '100%', ...style }} className={className || ''}>
        {children || 'Loading...'}
    </FullPageSpinner>
);
