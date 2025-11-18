import React, { JSX } from 'react';

type Props = {
    taskName: string,
    taskDescription: string
};

export const TaskNameAndDescription: React.FC<Props> = ({ taskName, taskDescription }): JSX.Element => (
    <div className='title'>
        <div>
            <i className='custom-icon icon activity' />
        </div>
        <div>
            <h2>
                <small className='font-weight-bold text-wrap'>
                    {taskName}
                </small>
                <small className='text-wrap small'>
                    {taskDescription}
                </small>
            </h2>
        </div>
    </div>
);
