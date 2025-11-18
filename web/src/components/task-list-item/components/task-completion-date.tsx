import React, { JSX } from 'react';
import { differenceInDays, format } from 'date-fns';
import { TaskStatus } from '../../../models';

type Props = {
    taskCompletionDate: string;
    taskStatus: TaskStatus;
};

export const TaskCompletionDate: React.FC<Props> = ({ taskCompletionDate, taskStatus }): JSX.Element => {
    const completionDate = new Date(taskCompletionDate);
    const formattedTaskCompletionDate = format(completionDate, 'yyyy/MM/dd');
    
    let taskDaysOverdue = 0;
    if (taskStatus !== 'completed' && taskStatus !== 'cancelled') {
        taskDaysOverdue = differenceInDays(new Date(), completionDate);
    }
    
    return (
        <div className='title'>
            <div>
                <i className={`custom-icon icon ${taskDaysOverdue > 0 ? 'date-time' : 'calendar'}`} />
            </div>
            <div>
                <h2>
                    <small className='font-weight-bold text-wrap'>
                        {taskDaysOverdue > 0 ? `${taskDaysOverdue} day(s) overdue` : formattedTaskCompletionDate}
                    </small>
                    <small className='text-wrap small'>
                        {taskDaysOverdue > 0 ? `Due date: ${formattedTaskCompletionDate}` : 'Est. Completion Date'}
                    </small>
                </h2>
            </div>
        </div>
    );
};
