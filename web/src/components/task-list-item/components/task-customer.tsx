import React, { JSX } from 'react';

type Props = {
    customerName: string
};

export const TaskCustomer: React.FC<Props> = ({ customerName }): JSX.Element => (
    <div className='title'>
        <div>
            <i className='custom-icon icon user' />
        </div>
        <div>
            <h2>
                <small className='font-weight-bold text-wrap'>
                    {customerName}
                </small>
                <small className='text-wrap small'>
                    Customer
                </small>
            </h2>
        </div>
    </div>
);
