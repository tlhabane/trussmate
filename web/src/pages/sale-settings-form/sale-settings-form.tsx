import React, { JSX, useCallback, useEffect, useMemo, useState } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { To, useNavigate, useParams } from 'react-router-dom';
import { Button, ContainerSpinner, Form } from '../../components';
import { StickyFooter, useLayoutChildContext } from '../../containers';
import { WorkflowForm, TaskForm } from './components';
import { useWorkflowForm } from './hooks';
import { workflowTaskFormConfig } from './config';
import {
    scrollToElement,
    useAxios,
    useFetchData,
    useHttpRequestConfig,
    useFormValidation,
} from '../../utils';
import { usePromiseToast } from '../../hooks';
import { WorkflowTask, Task, WorkflowList } from '../../models';
import { ButtonClickFn, FormState, ReactSelectSingleOption } from '../../types';
import { APP_NAME } from '../../config';

export default function SaleSettingsForm(): JSX.Element {
    document.title = `Manage Sale Process :: ${APP_NAME}`;
    
    useEffect(() => {
        scrollToElement();
        return () => {
        };
    }, []);
    
    const navigate = useNavigate();
    
    const [exitPath] = useState('/settings/sale/process');
    const { setExitLocation } = useLayoutChildContext();
    useEffect(() => {
        setExitLocation(exitPath);
    }, [exitPath, setExitLocation]);
    
    const handleCancel: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        navigate((exitPath || -1) as To, { replace: true });
    };
    
    const fetchConfig = useMemo(() => ({
        url: '/task',
        queryKey: ['tasks'],
    }), []);
    
    const { data: taskOptionData, isLoading: taskOptionsLoading } = useFetchData(fetchConfig);
    const [taskOptions, setTaskOptions] = useState<ReactSelectSingleOption[]>([]);
    type TaskError = Record<keyof WorkflowTask, string>;
    const [taskErrors, setTaskErrors] = useState<TaskError[]>([]);
    const [tasks, setTasks] = useState<FormState<WorkflowTask>[]>([]);
    const [taskOptionsLoaded, setTaskOptionsLoaded] = useState(false);
    
    useEffect(() => {
        if (taskOptionData) {
            const taskList = (taskOptionData?.records || []) as Task[];
            const updatedTaskOptions = taskList.map(({ taskId, taskName }) => ({
                label: taskName,
                value: taskId,
                enabled: true,
            }));
            setTaskOptions(updatedTaskOptions);
            const task = {
                ...workflowTaskFormConfig,
                taskId: { ...workflowTaskFormConfig.taskId, options: updatedTaskOptions },
                taskNo: { ...workflowTaskFormConfig.taskNo, value: 1 },
            };
            setTasks([{ ...task }]);
            
            const taskError = Object.keys(task).reduce((acc, key) => {
                acc[key as keyof WorkflowTask] = '';
                return acc;
            }, {} as TaskError);
            setTaskErrors([{ ...taskError }]);
            setTaskOptionsLoaded(true);
        }
    }, [taskOptionData]);
    
    const syncTasks = (updatedTask: FormState<WorkflowTask>) => {
        const { taskNo } = updatedTask;
        setTasks((prevState) => {
            const updatedTasks = [...prevState];
            if (updatedTasks[taskNo.value - 1]) {
                updatedTasks[taskNo.value - 1] = {
                    ...updatedTasks[taskNo.value - 1],
                    ...updatedTask,
                };
                return updatedTasks;
            }
            return prevState;
        });
    };
    
    const handleAddTask: ButtonClickFn<void> = useCallback((event) => {
        event.preventDefault();
        setTasks(prevState => [
            ...prevState,
            {
                ...workflowTaskFormConfig,
                taskId: { ...workflowTaskFormConfig.taskId, options: taskOptions },
                taskNo: { ...workflowTaskFormConfig.taskNo, value: prevState.length + 1 },
            },
        ]);
    }, [taskOptions]);
    
    const handleDeleteTask: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        if (task) {
            setTasks((prevState) => {
                const updatedTasks = prevState.filter((_, index) => index !== (+task - 1));
                return [...updatedTasks].map((task, index) => ({
                    ...task, taskNo: { ...task.taskNo, value: index + 1 },
                }));
            });
            setTaskErrors((prevState) => [
                ...prevState.filter((_, index) => index !== (+task - 1)),
            ]);
        }
    };
    
    const {
        formData,
        formInvalid,
        getElement,
        handleSubmit,
        onBlur,
        onChange,
        setFormData,
    } = useWorkflowForm();
    
    const axios = useAxios();
    const getHttpRequestConfig = useHttpRequestConfig();
    const toast = usePromiseToast();
    const validateForm = useFormValidation<WorkflowTask>();
    const queryClient = useQueryClient();
    
    const onSubmit = handleSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            /* Validate tasks */
            const validation: boolean[] = [];
            const validatedForm = tasks.map((task) => {
                const { updatedFormState, isValid } = validateForm(task);
                validation.push(isValid);
                return updatedFormState;
            });
            
            if (validation.some(field => !field)) {
                const updatedTaskErrors = validatedForm.map((task) => {
                    return Object.entries(task).reduce((acc, [key, props]) => {
                        acc[key as keyof WorkflowTask] = props?.error || '';
                        return acc;
                    }, {} as TaskError);
                });
                setTaskErrors(updatedTaskErrors);
                button?.classList.remove('loading');
                return;
            }
            // Simulate API call
            // await new Promise((resolve) => setTimeout(resolve, 5000));
            const taskData = validatedForm.map((task) => {
                return Object.entries(task).reduce((acc: Record<string, any>, [key, input]) => {
                    acc[key] = typeof input.value === 'string' ? input.value.trim() : input.value;
                    return acc;
                }, {});
            });
            
            const workflowId = (validated?.workflowId?.toString() || '').trim();
            const httpRequestConfig = {
                ...await getHttpRequestConfig(workflowId === '' ? 'POST' : 'PATCH'),
                url: '/workflow',
                data: { ...validated, tasks: [...taskData] },
            };
            const process = workflowId === '' ? 'Adding new process' : 'Updating process';
            const response = await toast(axios(httpRequestConfig), `${process}...`);
            
            if (response?.success) {
                await queryClient.invalidateQueries({ queryKey: ['workflows'] });
                button?.classList.remove('loading');
                navigate((exitPath || -1) as To, { replace: true });
                return;
            }
            button?.classList.remove('loading');
            // Reset formConfig or navigate as needed
            // resetForm();
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    const { workflowId } = useParams();
    const workflowFetchConfig = useMemo(() => ({
        url: '/workflow',
        queryKey: ['workflows'],
        params: { workflowId },
    }), [workflowId]);
    const { data: workflowData, isLoading: workflowLoading } = useFetchData(workflowFetchConfig);
    
    useEffect(() => {
        if (workflowId && workflowData && taskOptionsLoaded) {
            const workflows = (workflowData?.records || []) as WorkflowList[];
            for (const workflow of workflows) {
                const { workflowName, workflowId: id, tasks } = workflow;
                setFormData((prevState) => ({
                    ...prevState,
                    workflowId: { ...prevState.workflowId, value: id || '' },
                    workflowName: { ...prevState.workflowName, value: workflowName || '' },
                }));
                const updatedTasks: FormState<WorkflowTask>[] = tasks.map((task, index) => ({
                    ...workflowTaskFormConfig,
                    taskId: {
                        ...workflowTaskFormConfig.taskId,
                        value: task.taskId,
                        error: '',
                        options: taskOptions,
                    },
                    taskNo: {
                        ...workflowTaskFormConfig.taskNo,
                        value: task.taskNo || index + 1,
                        error: '',
                    },
                    taskOptional: {
                        ...workflowTaskFormConfig.taskOptional,
                        value: task.taskOptional || 0,
                        error: '',
                    },
                    triggerType: {
                        ...workflowTaskFormConfig.triggerType,
                        value: task.triggerType || 'automatic',
                        error: '',
                    },
                    assignedTo: {
                        ...workflowTaskFormConfig.assignedTo,
                        value: task.assignedTo || '',
                        error: '',
                    },
                    assignmentNote: {
                        ...workflowTaskFormConfig.assignmentNote,
                        value: task.assignmentNote || '',
                        error: '',
                    },
                }));
                const sortedTasks = updatedTasks.sort((a, b) => a.taskNo.value - b.taskNo.value);
                setTasks(sortedTasks);
            }
            setTaskOptionsLoaded(false);
        }
    }, [setFormData, taskOptions, taskOptionsLoaded, workflowData, workflowId]);
    
    if ((taskOptionsLoading && !taskOptionData) || (workflowId && workflowLoading && !workflowData && taskOptionsLoaded)) {
        return <ContainerSpinner />;
    }
    
    return (
        <div className='flex-fill'>
            <div className='row mb-5 pb-5'>
                <div className='col-lg-6 offset-lg-3 col-md-10 offset-md-1 col-sm-10 offset-sm-1 mb-5'>
                    <Form onSubmit={onSubmit}>
                        <WorkflowForm
                            getElement={getElement}
                            formConfig={formData}
                            onBlur={onBlur}
                            onChange={onChange}
                        />
                        <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                            <div className='d-flex flex-row align-items-center'>
                                <span className='pr-3'>
                                    <i className='custom-icon icon activity' />
                                </span>
                                <h6>Tasks</h6>
                            </div>
                            <hr className='default' />
                            
                            {tasks.map((task, index) => (
                                <TaskForm
                                    key={task.taskNo.value}
                                    task={task}
                                    deleteTask={handleDeleteTask}
                                    syncTaskHandler={syncTasks}
                                    taskErrors={taskErrors[index]}
                                />
                            ))}
                            <hr className='default' />
                            <div className='row'>
                                
                                <div className='col-12 pr-sm-0 pl-sm-0'>
                                    <Button
                                        type='button'
                                        className='btn-default btn-block'
                                        onClick={handleAddTask}
                                    >
                                        <i className='custom-icon icon left-icon plus-circle' />
                                        Add Another Task
                                    </Button>
                                </div>
                            </div>
                        </div>
                        <StickyFooter>
                            <div className='row'>
                                <div className='col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1'>
                                    <div className='form-group mt-3 mb-3'>
                                        <div className='row'>
                                            <div className='col-3 pr-sm-0 pl-sm-0'>
                                                <Button
                                                    type='button'
                                                    className='btn-default btn-block'
                                                    onClick={handleCancel}
                                                >
                                                    Cancel
                                                </Button>
                                            </div>
                                            <div className='col-8 offset-1 pr-sm-0 pl-sm-0'>
                                                <Button
                                                    type='submit'
                                                    className='btn-success btn-block'
                                                    disabled={formInvalid}
                                                >
                                                    <i className='custom-icon icon left-icon save' />
                                                    Save
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </StickyFooter>
                    </Form>
                </div>
            </div>
        </div>
    );
}
