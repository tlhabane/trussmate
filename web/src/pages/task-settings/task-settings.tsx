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
import { TaskListItem, TaskModalForm } from './components';
import { ModalPrompt } from '../../containers';
import { useAxios, useFetchData, useHttpRequestConfig } from '../../utils';
import { usePromiseToast, useSearchHandlers } from '../../hooks';
import { useTaskForm } from './useTaskForm';
import { taskFormConfig } from './taskFormConfig';
import { Task } from '../../models';
import type {
    ButtonClickFn,
    HTMLElementClickFn,
    LinkClickFn,
    Pagination,
    ReactSelectSingleOption,
} from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';

export default function TaskSettings(): JSX.Element {
    document.title = `Task Settings :: ${APP_NAME}`;
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
        url: '/task',
        queryKey: ['tasks'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    
    const [loadTaskList, setLoadTaskList] = useState(false);
    const [taskList, setTaskList] = useState<Task[]>([]);
    const [pagination, setPagination] = useState<Pagination | null>(null);
    const { data, isLoading, isFetching, refetch } = useFetchData(fetchConfig);
    
    const updateData = useCallback((data?: any) => {
        if (data) {
            const updatedTaskList = (data?.records || []) as Task[];
            const updatedPagination = data?.pagination as Pagination;
            setTaskList(updatedTaskList);
            setPagination(updatedPagination);
            setLoadTaskList(false);
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
        setLoadTaskList(true);
        refetch()
            .then(({ data: updatedData }) => {
                updateData(updatedData);
            })
            .finally(() => {
                setLoadTaskList(false);
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
        pagingLinkClickHandler(event, setFilterParams, setLoadTaskList);
    };
    
    const handleUpdateRecordsPerPage: HTMLElementClickFn<void> = (event) => {
        recordsPerPageSelectionHandler(event, setTaskList, setFilterParams, setLoadTaskList);
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
        resetForm,
        setFormData,
    } = useTaskForm();
    
    const [showTaskForm, setShowTaskForm] = useState(false);
    const dismissTaskForm = () => {
        resetForm();
        setShowTaskForm(false);
    };
    
    useEffect(() => {
        // toggle payment amount element: Fixed amount or Percentage(%) of sale
        setFormData((prev) => {
            const paymentType = formData.taskPaymentType.value;
            const value = paymentType === '0' ? 0 : +prev.taskPayment.value;
            const type = paymentType === 'fixed' ? 'text' : 'select';
            const label = paymentType === 'fixed' ? 'Payment Amount (ZAR)' : 'Payment Amount (% of order amount)';
            const required = paymentType === 'fixed' || paymentType === 'percentage';
            return { ...prev, taskPayment: { ...prev.taskPayment, error: '', label, required, type, value } };
        });
    }, [formData.taskPaymentType.value, setFormData]);
    
    const toggleShowTaskForm = () => {
        setShowTaskForm((taskFormShown) => {
            if (taskFormShown) {
                dismissTaskForm();
            }
            return !taskFormShown;
        });
    };
    
    const axios = useAxios();
    const getHttpRequestConfig = useHttpRequestConfig();
    const toast = usePromiseToast();
    
    const onSubmit = handleSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            const taskId = (validated?.taskId?.toString() || '').trim();
            const httpRequestConfig = {
                ...await getHttpRequestConfig(taskId === '' ? 'POST' : 'PATCH'),
                url: '/task',
                data: { ...validated },
            };
            const process = taskId === '' ? 'Adding new task' : 'Updating task info';
            const response = await toast(axios(httpRequestConfig), `${process}...`);
            if (response?.success) {
                updateRecords();
                dismissTaskForm();
            }
            
            button?.classList.remove('loading');
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    const handleAddTask: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        toggleShowTaskForm();
    };
    
    const handleUpdateTask: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        const selectedTask = taskList.find((t) => t.taskId === task);
        if (selectedTask) {
            const updatedFormData = Object.entries(taskFormConfig).reduce((acc: any, [key, props]) => {
                const value = (selectedTask as any)[key];
                acc[key] = { ...props, error: '', value };
                return acc;
            }, {});
            setFormData(updatedFormData);
            toggleShowTaskForm();
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
    
    const handleProceedDeletingTask: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        const httpRequestConfig = {
            ...await getHttpRequestConfig('DELETE'),
            url: '/task',
            data: { taskId: task || '' },
        };
        
        await toast(axios(httpRequestConfig, undefined, event.currentTarget));
        updateRecords();
    };
    
    const handleDeleteTask: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        if (task) {
            setConfirmationButton(
                <Button
                    className='btn-danger btn-block'
                    data-task={task}
                    onClick={handleProceedDeletingTask}
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
    
    const RenderTaskList = () => {
        if (isLoading && !data) {
            return <ContainerSpinner />;
        }
        
        if (taskList.length > 0) {
            return (
                <div className='d-block mt-3 mb-5'>
                    {taskList.map((task) => (
                        <TaskListItem
                            key={task.taskId}
                            task={task}
                            deleteTask={handleDeleteTask}
                            updateTask={handleUpdateTask}
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
                                <>No tasks matching your criteria available</>
                            ) : (
                                <>
                                    No tasks currently available.
                                    <br /> Once available, all your tasks will be displayed here.
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
            <TaskModalForm
                openModal={showTaskForm}
                toggleModal={dismissTaskForm}
                getElement={getElement}
                formConfig={formData}
                formInvalid={formInvalid}
                onBlur={onBlur}
                onChange={onChange}
                onSelect={onReactSelectChange}
                onSubmit={onSubmit}
            />
            <div className='d-flex flex-fill align-items-center justify-content-center'>
                <div className='d-flex flex-fill flex-column pl-lg-3'>
                    <SearchAndFilter
                        ref={searchInputRef}
                        disabled={isLoading || isFetching}
                        loading={isFetching || loadTaskList}
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
                    <RenderTaskList />
                </div>
            </div>
        </>
    );
}
