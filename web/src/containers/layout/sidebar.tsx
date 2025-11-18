import React, { JSX } from 'react';

export const Sidebar: React.FC<React.HTMLProps<HTMLDivElement>> = ({ children }): JSX.Element => (
    <div className='sidebar'>
        <div className='sidebar-inner'>{children}</div>
    </div>
);
