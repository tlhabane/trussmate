import React, { JSX } from 'react';
import { Logo } from '../../../components';
import { APP_NAME } from '../../../config';

export const HeaderLogo: React.FC = (): JSX.Element => (
    <div className='d-flex flex-row align-items-center'>
        <div className='d-flex align-items-center'>
            <Logo className='d-flex' style={{ height: 50, width: 50 }} />
        </div>
        <div className='d-flex flex-column pl-2'>
            <span className='font-weight-bold text-nowrap'>Billing Management</span>
            <span className='small'>{APP_NAME}</span>
        </div>
    </div>
);
