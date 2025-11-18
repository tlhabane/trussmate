import React, { JSX } from 'react';
import { HeaderLogo } from './header.logo';
import { HeaderExitLink } from './header-exit-link';

export const HeaderChild: React.FC<React.HTMLProps<HTMLAnchorElement>> = (props): JSX.Element => (
    <header>
        <div className='header-inner'>
            <HeaderLogo />
            <HeaderExitLink {...props} />
        </div>
    </header>
);
