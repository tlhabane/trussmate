import React, { JSX } from 'react';
import './styles.scss';

export const StickyFooter: React.FC<React.HTMLProps<HTMLDivElement>> = ({
                                                                            children,
                                                                            className,
                                                                            ...rest
                                                                        }): JSX.Element => (
    <footer {...rest} className={`sticky-footer ${className || ''}`}>
        <div className='footer-inner'>
            {children}
        </div>
    </footer>
);
