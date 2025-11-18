import React, { JSX } from 'react';
import { capitalizeFirstLetter } from '../../../utils';

type Props = {
    assignedTo: string;
};

export const TaskTeam: React.FC<Props> = ({ assignedTo }): JSX.Element => (
    <div className='title'>
        <div>
            <i className='custom-icon icon user' />
        </div>
        <div>
            <h2>
                <small className='font-weight-bold text-wrap'>
                    {capitalizeFirstLetter(assignedTo.split('_').join(' '))}
                </small>
                <small className='text-wrap small'>
                    Team
                </small>
            </h2>
        </div>
    </div>
);
