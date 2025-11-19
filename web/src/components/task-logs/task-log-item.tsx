import React, { JSX } from 'react';
import { format } from 'date-fns';
import { capitalizeFirstLetter, chunkArray } from '../../utils';
import { TaskLog } from '../../models';

const formatDate = (date: string, time = false) => {
    const taskDate = new Date(date);
    try {
        if (time) {
            return `${format(taskDate, 'yyyy/MM/dd')} @ ${format(taskDate, 'H:I')}`;
        }
        return format(taskDate, 'yyyy/MM/dd');
    } catch (e) {
        const [datePart, timePart] = date.split('T');
        const formattedDate = datePart.split('-').join('/');
        const formattedTime = timePart.split(':').slice(0, 1).join(':');
        return time ? `${formattedDate} @ ${formattedTime}` : `${formattedDate}`;
    }
};

// const iconSize = { width: 24, height: 24 };

export const TaskLogItem: React.FC<{ log: TaskLog }> = ({ log }): JSX.Element => (
    <div className='log'>
        {/*<span className='py-2 px-2 d-flex flex-row align-items-center'>
            <i className='custom-icon icon calendar mr-2' style={iconSize} />
            <span className='py-2 px-2 d-flex flex-column'>
                <span className='font-weight-bold'>Date</span>
                {formatDate(log.logDate)}
            </span>
        </span>*/}
        <div className='py-3 px-3'>{formatDate(log.logDate, true)}</div>
        <div className='py-3 px-3'>{`${log.firstName} ${log.lastName}`}</div>
        <div className='py-3 px-3'>{capitalizeFirstLetter(log.taskStatus.split('_').join(' '))}</div>
        <div className='py-3 px-3'>{`${formatDate(log.taskCompletionDate)}`}</div>
        <div className='py-3 px-3'>
            {chunkArray(log.comments.split(' '), 10).map((arr) => (
                <span key={arr.join(' ')} className='d-block'>
                    {arr.join(' ')}
                </span>
            ))}
        </div>
    </div>
);
