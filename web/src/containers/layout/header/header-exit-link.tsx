import React, { JSX } from 'react';
import { Link, To, useNavigate } from 'react-router-dom';
import { LinkClickFn } from '../../../types';

export const HeaderExitLink: React.FC<React.HTMLProps<HTMLAnchorElement>> = ({ href }): JSX.Element => {
    const navigate = useNavigate();
    const handleExit: LinkClickFn = (event) => {
        event.preventDefault();
        const { href } = event.currentTarget;
        const paths = href.split('/');
        const target = paths?.length > 0 ? `/${paths[paths?.length - 1]}` : href;
        navigate((target || -1) as To, { replace: true });
    };
    
    return (
        <Link to={href || ''} onClick={handleExit}>
            <svg
                xmlns='http://www.w3.org/2000/svg'
                width='24'
                height='24'
                viewBox='0 0 24 24'
                fill='none'
                stroke='#353f4d'
                strokeWidth='2'
                strokeLinecap='round'
                strokeLinejoin='round'
                className='feather feather-x'
            >
                <line x1='18' y1='6' x2='6' y2='18' />
                <line x1='6' y1='6' x2='18' y2='18' />
            </svg>
        </Link>
    );
};
