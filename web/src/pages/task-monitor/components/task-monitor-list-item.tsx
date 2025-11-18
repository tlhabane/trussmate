import React, { JSX } from 'react';
import { Button, ListItemContainer, ListItemHeader } from '../../../components';
import { capitalizeFirstLetter } from '../../../utils';
import { TaskMonitorList } from '../../../models';
import { ButtonClickFn } from '../../../types';
import { escalationTypeOptions } from '../../../static-data';

type Props = {
    data: TaskMonitorList;
    deleteTask: ButtonClickFn<void>;
    updateTask: ButtonClickFn<void>;
}
export const TaskMonitorListItem: React.FC<Props> = ({ data, deleteTask, updateTask }): JSX.Element => {
    const notificationType = capitalizeFirstLetter(
        `${escalationTypeOptions.find((i) => i.value === data.escalationType)?.label || 'None'}`,
    );
    const notificationSuffix = data.escalationType === 'overdue'
        ? `For ${data.escalationDays} or more`
        : `Follow up within ${data.escalationDays}`;
    
    return (
        <ListItemContainer className='striped'>
            <ListItemHeader>
                <div className='col-4 title'>
                    <div>
                        <i className='custom-icon icon activity' />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>
                                {capitalizeFirstLetter(`${data.taskName}`)}
                            </small>
                            <small className='text-wrap small'>
                                {data.taskDescription}
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-2 title'>
                    <div>
                        <i className='custom-icon icon date-time' />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>
                                {data.taskDays === 1 ? 'Same day' : `${data.taskDays} day(s)`}
                            </small>
                            <small className='text-wrap small'>
                                ETA
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-3 title'>
                    <div>
                        <i className='custom-icon icon notification' />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>
                                {notificationType}
                            </small>
                            <small className='text-wrap small'>
                                {`${notificationSuffix}  day(s)`}
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-3 title'>
                    <div>
                        <i className='custom-icon icon user' />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>
                                {capitalizeFirstLetter(`${data.firstName} ${data.lastName}`)}
                            </small>
                            <small className='text-wrap small'>
                                Handler
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-4 action-col'>
                    <Button
                        className='btn-link no-text tooltip-top'
                        data-tooltip='Update'
                        data-escalation={data.escalationId}
                        onClick={updateTask}
                    >
                        <i className='custom-icon icon icon-only edit' />
                    </Button>
                    <div className='v-divider' />
                    <Button
                        className='btn-link delete-btn no-text tooltip-top'
                        data-tooltip='Delete'
                        data-escalation={data.escalationId}
                        onClick={deleteTask}
                    >
                        <i className='custom-icon icon icon-only trash' />
                    </Button>
                </div>
            </ListItemHeader>
        </ListItemContainer>
    );
};
