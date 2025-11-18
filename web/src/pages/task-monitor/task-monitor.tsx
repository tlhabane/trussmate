import React, { JSX, useCallback, useEffect, useMemo, useState } from 'react';
import {
    Button,
    ContainerSpinner,
    getRecordsDisplayedOptions,
    EmptyAdditionalNotice,
    EmptyListContainer,
    EmptyNoticeButton,
    pagingLinkClickHandler,
    recordsPerPageSelectionHandler,
    SearchAndFilter,
} from '../../components';
import { TaskMonitorListItem, TaskMonitorModalForm } from './components';
import { ModalPrompt } from '../../containers';
import { useAxios, useFetchData, useHttpRequestConfig } from '../../utils';
import { usePromiseToast, useSearchHandlers } from '../../hooks';
import { useTaskMonitorForm } from './useTaskMonitorForm';
import { taskMonitorFormConfig } from './task-monitor-form-config';
import { Task, TaskMonitorList, User } from '../../models';
import type {
    ButtonClickFn,
    HTMLElementClickFn,
    LinkClickFn,
    Pagination,
    ReactSelectSingleOption,
} from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';

export default function TaskMonitor(): JSX.Element {
    document.title = `Monitoring & Escalations :: ${APP_NAME}`;
    const initFilterParams = useMemo<Record<string, any>>(
        () => ({
            search: '',
            page: 1,
            recordsPerPage: 10,
        }),
        [],
    );
    const [filterParams, setFilterParams] = useState(initFilterParams);
    
    const fetchConfig = useMemo(() => ({
        url: '/task/monitor',
        queryKey: ['taskMonitor'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    
    const [loadTaskMonitorList, setLoadTaskMonitorList] = useState(false);
    const [taskMonitorList, setTaskMonitorList] = useState<TaskMonitorList[]>([]);
    const [pagination, setPagination] = useState<Pagination | null>(null);
    const { data, isLoading, isFetching, refetch } = useFetchData(fetchConfig);
    
    const updateData = useCallback((data?: any) => {
        if (data) {
            const updatedTaskMonitorList = (data?.records || []) as TaskMonitorList[];
            const updatedPagination = data?.pagination as Pagination;
            setTaskMonitorList(updatedTaskMonitorList);
            setPagination(updatedPagination);
            setLoadTaskMonitorList(false);
        }
    }, []);
    
    useEffect(() => {
        updateData(data);
    }, [data, updateData]);
    
    const [recordsPerPageOptions, setRecordsPerPageOptions] = useState<ReactSelectSingleOption[]>([]);
    useEffect(() => {
        if (pagination) {
            const updatedRecordsPerPageOptions = getRecordsDisplayedOptions(pagination.totalRecords);
            setRecordsPerPageOptions(updatedRecordsPerPageOptions);
        }
    }, [pagination]);
    
    const updateRecords = useCallback(() => {
        setLoadTaskMonitorList(true);
        refetch()
            .then(({ data: updatedData }) => {
                updateData(updatedData);
            })
            .finally(() => {
                setLoadTaskMonitorList(false);
            });
    }, [refetch, updateData]);
    
    const resetFilterParams = () => {
        setFilterParams(initFilterParams);
        updateRecords();
    };
    const handleResetFilterParams: LinkClickFn<void> = (event) => {
        event.preventDefault();
        resetFilterParams();
    };
    
    const handlePaginationLinkClick: LinkClickFn<void> = (event) => {
        pagingLinkClickHandler(event, setFilterParams, setLoadTaskMonitorList);
    };
    
    const handleUpdateRecordsPerPage: HTMLElementClickFn<void> = (event) => {
        recordsPerPageSelectionHandler(event, setTaskMonitorList, setFilterParams, setLoadTaskMonitorList);
    };
    
    const handleRefreshDataList: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        updateRecords();
    };
    
    const {
        formData,
        formInvalid,
        getElement,
        handleSubmit,
        onBlur,
        onChange,
        onReactSelectChange,
        setFormData,
    } = useTaskMonitorForm();
    
    const fetchTaskConfig = useMemo(() => ({
        url: '/task',
        queryKey: ['tasks'],
    }), []);
    
    const { data: taskOptionData, isLoading: taskOptionsLoading } = useFetchData(fetchTaskConfig);
    const [taskOptions, setTaskOptions] = useState<ReactSelectSingleOption[]>([]);
    const [taskList, setTaskList] = useState<Task[]>([]);
    
    const fetchUserConfig = useMemo(() => ({
        url: '/user',
        queryKey: ['team'],
    }), []);
    const { data: userOptionData, isLoading: userOptionsLoading } = useFetchData(fetchUserConfig);
    const [userOptions, setUserOptions] = useState<ReactSelectSingleOption[]>([]);
    
    useEffect(() => {
        if (taskOptionData && userOptionData) {
            const updatedTaskList = (taskOptionData?.records || []) as Task[];
            const updatedTaskOptions = updatedTaskList.map(({ taskId, taskName }) => ({
                label: taskName,
                value: taskId,
                enabled: true,
            }));
            setTaskOptions(updatedTaskOptions);
            setTaskList(updatedTaskList);
            
            const userList = (userOptionData?.records || []) as User[];
            const updatedUserOptions = userList.map(({ username, userStatus, firstName, lastName }) => ({
                label: `${firstName} ${lastName}`,
                value: username,
                enabled: userStatus === 'active',
            }));
            setUserOptions(updatedUserOptions);
            
            setFormData((prevState) => ({
                ...prevState,
                taskId: {
                    ...prevState.taskId,
                    options: updatedTaskOptions,
                },
                username: {
                    ...prevState.username,
                    options: updatedUserOptions,
                },
            }));
        }
    }, [setFormData, taskOptionData, userOptionData]);
    
    const [showTaskMonitorForm, setShowTaskMonitorForm] = useState(false);
    const dismissTaskMonitorForm = useCallback(() => {
        setFormData({
            ...taskMonitorFormConfig,
            taskId: {
                ...taskMonitorFormConfig.taskId,
                options: taskOptions,
            },
            username: {
                ...taskMonitorFormConfig.username,
                options: userOptions,
            },
        });
        setShowTaskMonitorForm(false);
    }, [setFormData, taskOptions, userOptions]);
    
    const toggleShowTaskMonitorForm = () => {
        setShowTaskMonitorForm((taskMonitorFormShown) => {
            if (taskMonitorFormShown) {
                dismissTaskMonitorForm();
            }
            return !taskMonitorFormShown;
        });
    };
    
    const axios = useAxios();
    const getHttpRequestConfig = useHttpRequestConfig();
    const toast = usePromiseToast();
    
    const onSubmit = handleSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            const escalationId = (validated?.escalationId?.toString() || '').trim();
            const httpRequestConfig = {
                ...await getHttpRequestConfig(escalationId === '' ? 'POST' : 'PATCH'),
                url: '/task/monitor',
                data: { ...validated },
            };
            const process = escalationId === '' ? 'Adding new task' : 'Updating task info';
            const response = await toast(axios(httpRequestConfig), `${process}...`);
            if (response?.success) {
                updateRecords();
                dismissTaskMonitorForm();
            }
            
            button?.classList.remove('loading');
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    const handleAddTask: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        toggleShowTaskMonitorForm();
    };
    
    const handleUpdateTaskMonitor: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { escalation } = event.currentTarget.dataset;
        const selectedTask = taskMonitorList.find((t) => t.escalationId === escalation);
        if (selectedTask) {
            /*const updatedFormData = Object.entries(taskMonitorFormConfig).reduce((acc: any, [key, props]) => {
                const value = (selectedTask as any)[key];
                acc[key] = { ...props, error: '', value };
                return acc;
            }, {});*/
            setFormData((prevState) => (
                Object.entries(prevState).reduce((acc: any, [key, props]) => {
                    const value = (selectedTask as any)[key];
                    acc[key] = { ...props, error: '', value };
                    return acc;
                }, {})
            ));
            toggleShowTaskMonitorForm();
        }
    };
    
    const [promptUser, setPromptUser] = useState(false);
    const [confirmationButton, setConfirmationButton] = useState(<Button />);
    const taskDeleteMessage = (
        <>
            <h5>Are your sure you want to delete the selected task?</h5>
            <p className='small'>The effects of this action cannot be reversed.</p>
        </>
    );
    
    const handleDismissPromptModal = () => {
        setConfirmationButton(<Button />);
        setPromptUser(false);
    };
    
    const handleProceedDeletingTaskMonitor: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const button = event.currentTarget;
        const { escalation } = button.dataset;
        const httpRequestConfig = {
            ...await getHttpRequestConfig('DELETE'),
            url: '/task/monitor',
            data: { escalationId: escalation || '' },
        };
        
        await toast(axios(httpRequestConfig, undefined, button));
        updateRecords();
        handleDismissPromptModal();
    };
    
    const handleDeleteTaskMonitor: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { escalation } = event.currentTarget.dataset;
        if (escalation) {
            setConfirmationButton(
                <Button
                    className='btn-danger btn-block'
                    data-escalation={escalation}
                    onClick={handleProceedDeletingTaskMonitor}
                >
                    <i className='custom-icon icon left-icon trash' />
                    Delete
                </Button>,
            );
            setPromptUser(true);
        }
    };
    
    const {
        handleClearSearch,
        handleSearchValueChange,
        searchInputRef,
    } = useSearchHandlers(setFilterParams, updateRecords);
    
    const RenderTaskMonitorList = () => {
        if ((isLoading && !data) || taskOptionsLoading || userOptionsLoading) {
            return <ContainerSpinner />;
        }
        
        if (taskMonitorList.length > 0) {
            return (
                <div className='d-block mt-3 mb-5'>
                    {taskMonitorList.map((task) => (
                        <TaskMonitorListItem
                            key={task.escalationId}
                            data={task}
                            deleteTask={handleDeleteTaskMonitor}
                            updateTask={handleUpdateTaskMonitor}
                        />
                    ))}
                </div>
            );
        }
        
        const listFiltered = filterParams.search.trim().length > 0;
        
        return (
            <div className='row p-5'>
                <div className='col-md-6 offset-md-3'>
                    <EmptyListContainer>
                        <i className='custom-icon icon activity' style={{ width: 48, height: 48 }} />
                        <p className='hint-text text-center mt-2'>
                            {listFiltered ? (
                                <>No monitoring & escalation tasks matching your criteria available</>
                            ) : (
                                <>
                                    No monitoring & escalation tasks currently available.
                                    <br /> Once available, all your monitoring & escalation tasks will be displayed
                                    here.
                                </>
                            )}
                        </p>
                        <EmptyNoticeButton onClick={handleAddTask}>
                            Add {listFiltered ? 'Missing' : 'New'} task
                        </EmptyNoticeButton>
                        <EmptyAdditionalNotice>
                            <Button className='btn-link'>
                                Upload {listFiltered ? 'Missing' : 'New'} tasks
                            </Button>
                        </EmptyAdditionalNotice>
                    </EmptyListContainer>
                </div>
            </div>
        );
    };
    
    return (
        <>
            <ModalPrompt
                openModalPrompt={promptUser}
                dismissModalPrompt={handleDismissPromptModal}
                promptConfirmationButton={confirmationButton}
            >
                {taskDeleteMessage}
            </ModalPrompt>
            <TaskMonitorModalForm
                openModal={showTaskMonitorForm}
                toggleModal={dismissTaskMonitorForm}
                getElement={getElement}
                formConfig={formData}
                formInvalid={formInvalid}
                onBlur={onBlur}
                onChange={onChange}
                onSelect={onReactSelectChange}
                onSubmit={onSubmit}
                tasks={taskList}
            />
            <div className='d-flex flex-fill align-items-center justify-content-center'>
                <div className='d-flex flex-fill flex-column pl-lg-3'>
                    <SearchAndFilter
                        ref={searchInputRef}
                        disabled={isLoading || isFetching}
                        loading={isFetching || loadTaskMonitorList}
                        filterParams={filterParams}
                        pagination={pagination}
                        addNewHandler={handleAddTask}
                        clearSearchHandler={handleClearSearch}
                        refreshDataHandler={handleRefreshDataList}
                        searchValueChangeHandler={handleSearchValueChange}
                        recordsPerPageOptions={recordsPerPageOptions}
                        resetFilterParamsHandler={handleResetFilterParams}
                        paginationLinkHandler={handlePaginationLinkClick}
                        updateRecordsPerPageHandler={handleUpdateRecordsPerPage}
                    />
                    <RenderTaskMonitorList />
                </div>
            </div>
        </>
    );
};
