import React, { JSX, useEffect } from 'react';
import { Button } from '../../../components';
import { WorkflowTaskForm } from './workflow.task.form';
import { useWorkflowTaskForm } from '../hooks';
import { WorkflowTask } from '../../../models';
import { ButtonClickFn, InputChangeFn, FormState, ReactSelectFn, ReactSelectSingleOption } from '../../../types';

type Props = {
    task: FormState<WorkflowTask>;
    taskErrors?: Record<keyof WorkflowTask, string>;
    deleteTask: ButtonClickFn<void>;
    syncTaskHandler?: (updatedTask: FormState<WorkflowTask>) => void;
};
export const TaskForm: React.FC<Props> = ({
                                              deleteTask,
                                              syncTaskHandler,
                                              task,
                                              taskErrors,
                                          }): JSX.Element => {
    const {
        formInvalid,
        getElement,
        onChange,
        onReactSelectChange,
        setErrorsFromAPI,
    } = useWorkflowTaskForm(task);
    
    
    useEffect(() => {
        if (taskErrors) {
            setErrorsFromAPI(taskErrors);
        }
    }, [setErrorsFromAPI, taskErrors]);
    
    const updateTask = (name: string, value: any) => {
        if (syncTaskHandler) {
            const updatedTask = { ...task, [name]: { ...task[name as keyof WorkflowTask], value, error: '' } };
            syncTaskHandler(updatedTask);
        }
    };
    
    const onTextChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement> = (event) => {
        const { name, value } = event.target;
        updateTask(name, value);
        onChange(event);
    };
    
    const onSelectChange: ReactSelectFn<void> = (name, event) => {
        const value = Array.isArray(event)
            ? event.map((item: ReactSelectSingleOption) => item?.value)
            : (event as ReactSelectSingleOption).value ?? '';
        
        updateTask(name, value);
        onReactSelectChange(name, value);
    };
    
    return (
        <div className='d-flex flex-column' key={task.taskNo.value}>
            <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                <div className='d-flex flex-row align-items-center justify-content-between'>
                    <span className='font-weight-bold'>
                        Task #{task.taskNo.value}
                    </span>
                    <Button
                        disabled={task.taskNo.value === 1}
                        style={{ height: 44, width: 72 }}
                        data-task={task.taskNo.value}
                        className='btn-sm btn-danger'
                        onClick={deleteTask}
                    >
                        <i className='custom-icon icon icon-only trash' />
                    </Button>
                </div>
            </div>
            <WorkflowTaskForm
                getElement={getElement}
                formConfig={task}
                formInvalid={formInvalid}
                onChange={onTextChange}
                onSelect={onSelectChange}
            />
        </div>
    );
};
