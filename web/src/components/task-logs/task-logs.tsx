import React, { JSX } from 'react';
import { v4 as uuidV4 } from 'uuid';
import { EmptyListContainer } from '../empty-notice';
import { Carousel } from '../../containers';
import { TaskLogItem } from './task-log-item';
import { TaskLog } from '../../models';

type Props = {
    logs: TaskLog[];
}

export const TaskLogs: React.FC<Props> = ({ logs }): JSX.Element => {
    if (logs.length === 1) {
        return (
            <div className='row p-5'>
                <div className='col-md-6 offset-md-3'>
                    <EmptyListContainer>
                        <i className='custom-icon icon date-time' style={{ width: 48, height: 48 }} />
                        <p className='hint-text text-center mt-2'>
                            No task history or updates available.
                        </p>
                    </EmptyListContainer>
                </div>
            </div>
        );
    }
    
    const iconSize = { width: 22, height: 22 };
    return (
        <div className='log-container'>
            <div className='log'>
                <div className='font-weight-bold py-3 px-3 d-flex flex-row align-items-center' style={{ marginTop: 3 }}>
                    <i className='custom-icon icon date-time mr-2' style={iconSize} /> Date & Time
                </div>
                <div className='font-weight-bold py-3 px-3 d-flex flex-row align-items-center'>
                    <i className='custom-icon icon user mr-2' style={iconSize} /> Name
                </div>
                <div className='font-weight-bold py-3 px-3 d-flex flex-row align-items-center'>
                    <i className='custom-icon icon tag mr-2' style={iconSize} /> Status
                </div>
                <div className='font-weight-bold py-3 px-3 d-flex flex-row align-items-center'>
                    <i className='custom-icon icon calendar mr-2' style={iconSize} /> Completion Date
                </div>
                <div className='font-weight-bold py-3 px-3 d-flex flex-row align-items-center'>
                    <i className='custom-icon icon activity mr-2' style={iconSize} /> Comments
                </div>
            </div>
            <div className='log'>
                <Carousel baseSlidesPerView={2} spaceBetween={0}>
                    {logs.map((log) => (
                        <TaskLogItem key={uuidV4()} log={log} />
                    ))}
                </Carousel>
            </div>
        </div>
    );
};
