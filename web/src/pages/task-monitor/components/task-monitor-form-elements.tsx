import React, { JSX } from 'react';
import { ListItemContainer, ListItemHeader } from '../../../components';
import { useValidateFormElement } from '../../../utils';
import { FormInput, FormState, InputChangeFn, InputFocusFn, ReactSelectFn } from '../../../types';
import { Task, TaskMonitor } from '../../../models';

type Props = {
    getElement: (name: any, props: FormInput<TaskMonitor>, handlers?: any) => JSX.Element;
    formConfig: FormState<TaskMonitor>;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect: ReactSelectFn<void>;
    task?: Task;
};

export const TaskMonitorFormElements: React.FC<Props> = (props) => {
    const { formConfig, getElement, onBlur, onChange, onSelect, task } = props;
    const validateFormElement = useValidateFormElement<TaskMonitor>();
    const getInputComponent = (inputName: string) => {
        const inputProps = validateFormElement(inputName as keyof TaskMonitor, formConfig);
        if (inputProps.type === 'select') {
            return getElement(inputName, inputProps, { onSelect });
        }
        
        return getElement(inputName, inputProps, { onBlur, onChange });
    };
    
    return (
        <>
            {getInputComponent('taskId')}
            {task && (
                <>
                    <ListItemContainer>
                        <ListItemHeader>
                            <div className='col-9 title'>
                                <div>
                                    <i className='custom-icon icon activity' />
                                </div>
                                <div>
                                    <h2>
                                        <small className='font-weight-bold text-wrap'>
                                            {task.taskDescription}
                                        </small>
                                    </h2>
                                </div>
                            </div>
                            <div className='col-3 title'>
                                <div>
                                    <i className='custom-icon icon date-time' />
                                </div>
                                <div>
                                    <h2>
                                        <small className='font-weight-bold text-wrap'>
                                            {task.taskDays === 1 ? 'Same day' : `${task.taskDays} day(s)`}
                                        </small>
                                        <small className='text-wrap small'>
                                            ETA
                                        </small>
                                    </h2>
                                </div>
                            </div>
                        </ListItemHeader>
                    </ListItemContainer>
                    <hr className='default' />
                </>
            )}
            {getInputComponent('escalationType')}
            {getInputComponent('escalationDays')}
            {getInputComponent('username')}
        </>
    );
};
