import React, { JSX } from 'react';
import { Button, ListItemContainer, ListItemHeader } from '../../../components';
import { capitalizeFirstLetter } from '../../../utils';
import { taskActionOptions } from '../../../static-data';
import { ButtonClickFn } from '../../../types';
import { Task } from '../../../models';

type Props = {
    task: Task,
    deleteTask: ButtonClickFn<void>;
    updateTask: ButtonClickFn<void>;
};

export const TaskListItem: React.FC<Props> = ({ task, deleteTask, updateTask }): JSX.Element => (
    <ListItemContainer className='striped'>
        <ListItemHeader>
            <div className='col-6 title'>
                <div>
                    <i className='custom-icon icon tag' />
                </div>
                <div>
                    <h2>
                        <small className='font-weight-bold text-wrap'>
                            {capitalizeFirstLetter(`${task.taskName}`)}
                        </small>
                        <small className='text-wrap small'>
                            {task.taskDescription}
                        </small>
                    </h2>
                </div>
            </div>
            <div className='col-6 title'>
                <div>
                    <i className='custom-icon icon activity' />
                </div>
                <div>
                    <h2>
                        <small className='font-weight-bold text-wrap'>
                            {capitalizeFirstLetter(`${taskActionOptions.find((i) => i.value === task.taskAction)?.label || 'None'}`)}
                        </small>
                        <small className='text-wrap small'>
                            Action
                        </small>
                    </h2>
                </div>
            </div>
            <div className='col-4 action-col'>
                <Button
                    className='btn-link no-text tooltip-top'
                    data-tooltip='Update'
                    data-task={task.taskId}
                    onClick={updateTask}
                >
                    <i className='custom-icon icon icon-only edit' />
                </Button>
                <div className='v-divider' />
                <Button
                    className='btn-link delete-btn no-text tooltip-top'
                    data-tooltip='Delete'
                    data-task={task.taskId}
                    onClick={deleteTask}
                >
                    <i className='custom-icon icon icon-only trash' />
                </Button>
            </div>
        </ListItemHeader>
    </ListItemContainer>
);
