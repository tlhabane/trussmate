import React, { JSX } from 'react';
import logo from '../../static/images/tm.png';

export const Logo: React.FC<React.HTMLProps<HTMLImageElement>> = ({ alt, src, style, ...rest }): JSX.Element => {
    return (
        <div className='logo-container'>
            <img {...rest} alt={alt} src={src || logo} style={{ width: 120, height: 120, ...style }} />
        </div>
    );
};
