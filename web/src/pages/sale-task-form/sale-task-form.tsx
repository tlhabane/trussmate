import React, { JSX, useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { To, useLocation, useNavigate, useParams } from 'react-router-dom';
import { differenceInDays, format, isPast } from 'date-fns';
import { useSaleTaskForm } from './useSaleTaskForm';
import {
    scrollToElement,
    useAxios,
    useFetchData,
    useHttpRequestConfigWithFiles,
    useJobAxios,
    useValidateFormElement,
} from '../../utils';
import { usePromiseToast } from '../../hooks';
import { Button, ContainerSpinner, DocumentListItem, Form, JobView } from '../../components';
import { StickyFooter, useLayoutChildContext } from '../../containers';
import { Job, SaleTaskList, SaleTaskForm } from '../../models';
import { ButtonClickFn, InputChangeFn } from '../../types';
import { saleTaskFormConfig } from './sale-task-form-config';
import { APP_NAME } from '../../config';

export default function SaleTaskFormComponent(): JSX.Element {
    document.title = `Update Task :: ${APP_NAME}`;
    
    const location = useLocation();
    const [previousPath, setPreviousPath] = useState<To>('');
    const { setPreviousLocation } = useLayoutChildContext();
    
    useEffect(() => {
        if (location.state?.from) {
            setPreviousLocation(location.state.from);
            setPreviousPath(location.state.from);
        }
    }, [location.state?.from, setPreviousLocation]);
    
    const uploadInput = useRef<HTMLInputElement | null>(null);
    const handleUpload: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        uploadInput.current?.click();
    };
    
    const [uploadedFiles, setUploadedFiles] = useState<File[]>([]);
    const onUpload: InputChangeFn<HTMLInputElement> = async (event) => {
        const { files } = event.currentTarget;
        if (files) {
            const fileArray = Array.from(files);
            setUploadedFiles(fileArray);
        }
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
    } = useSaleTaskForm();
    
    const axios = useAxios();
    const jobAxios = useJobAxios();
    const getHttpRequestConfig = useHttpRequestConfigWithFiles<SaleTaskForm>();
    const navigate = useNavigate();
    const toast = usePromiseToast();
    const queryClient = useQueryClient();
    
    const [jobEstimate, setJobEstimate] = useState<Job | null>(null);
    const [validatedData, setValidatedData] = useState<Record<string, any>>({});
    
    const [activeTab, setActiveTab] = useState('task-info');
    const switchTab = (selectedTab: string) => {
        setActiveTab(selectedTab);
        scrollToElement();
    };
    
    const handleSubmitTask = async (data: SaleTaskForm, button?: HTMLButtonElement) => {
        try {
            const httpRequestConfig = {
                ...await getHttpRequestConfig(data, 'POST', uploadedFiles),
                url: '/sale/task',
            };
            
            const response = await toast(axios(httpRequestConfig), 'Updating task...');
            button?.classList.remove('loading');
            
            if (response?.success) {
                await queryClient.invalidateQueries({ queryKey: ['sales', 'saleTasks'] });
                resetForm();
                navigate((previousPath || -1) as To, { replace: true });
            }
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    };
    
    const onSubmit = handleSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            
            const estimatedDocumentsRequired = validated.taskStatus.value === 'completed' && uploadedFiles.length === 0;
            const estimateTask = saleTask?.taskAction === 'estimate' && estimatedDocumentsRequired;
            
            if (estimateTask) {
                for await (const file of uploadedFiles) {
                    const formData = new FormData();
                    formData.append('quotation', file, file.name);
                    const httpRequestConfig = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                        method: 'POST',
                        url: '/upload',
                        data: formData,
                    };
                    
                    const response = await toast(jobAxios(httpRequestConfig), 'Processing estimate...');
                    button?.classList.remove('loading');
                    
                    if (response?.success) {
                        setValidatedData(validated);
                        const rawEstimate: Record<string, any> = JSON.parse(response.estimate);
                        setJobEstimate({ ...rawEstimate, jobNo: rawEstimate.jobNumber } as Job);
                        setActiveTab('job-info');
                    }
                }
                
                return;
            }
            await handleSubmitTask(validated, button);
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    const submitTask: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        
        const button = event.currentTarget;
        button.classList.add('loading');
        const data = { ...validatedData, job: { ...jobEstimate } } as SaleTaskForm;
        await handleSubmitTask(data, button);
    };
    
    const [saleTask, setSaleTask] = useState<SaleTaskList | null>(null);
    const [loadTask, setLoadTask] = useState(true);
    
    useEffect(() => {
        return () => {
            queryClient.invalidateQueries({ queryKey: ['saleTask'] })
                .finally(() => {
                    setSaleTask(null);
                    setLoadTask(true);
                });
        };
    }, [queryClient]);
    
    const { saleTaskId } = useParams();
    const taskFetchConfig = useMemo(() => ({
        url: '/sale/task',
        queryKey: ['saleTask', [saleTaskId]],
        params: { saleTaskId },
    }), [saleTaskId]);
    
    const { data } = useFetchData(taskFetchConfig);
    
    useEffect(() => {
        if (data) {
            const updatedSaleTasks = (data?.records || []) as SaleTaskList[];
            if (updatedSaleTasks.length > 0) {
                const selectedSaleTask = updatedSaleTasks[0];
                const updatedFormData = Object.entries(saleTaskFormConfig).reduce((acc: any, [key, props]) => {
                    let value = (selectedSaleTask as any)[key];
                    if (key === 'taskCompletionDate') {
                        value = format(new Date(value), 'yyyy-MM-dd');
                    }
                    acc[key] = { ...props, error: '', value };
                    return acc;
                }, {});
                
                setFormData(updatedFormData);
                setSaleTask(selectedSaleTask);
            }
            
            setLoadTask(false);
        }
    }, [data, setFormData]);
    
    const [showTaskDetail, setShowTaskDetail] = useState(false);
    const toggleShowTaskDetail: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        setShowTaskDetail((taskDetailShown) => !taskDetailShown);
    };
    
    useEffect(() => {
        scrollToElement();
        return () => {
            setActiveTab('task-info');
        };
    }, []);
    
    const onTaskCompletionDateChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void> = useCallback((event) => {
        const { value } = event.currentTarget;
        
        setFormData((prevState) => {
            const updatedFormData = { ...prevState };
            console.log('Current', updatedFormData);
            const updatedTaskCompletionDate = new Date(value);
            
            const currentTaskCompletionDate = new Date(prevState.taskCompletionDate.value);
            const days = differenceInDays(updatedTaskCompletionDate, currentTaskCompletionDate);
            
            const dateHasPassed = isPast(updatedTaskCompletionDate);
            
            updatedFormData['taskCompletionDate'] = {
                ...updatedFormData['taskCompletionDate'],
                value: format(updatedTaskCompletionDate, 'yyyy-MM-dd'),
                error: dateHasPassed ? 'Task completion date provided is in the past' : '',
            };
            
            const updatedTaskDays = prevState.taskDays.value + days;
            updatedFormData['taskDays'] = {
                ...updatedFormData['taskDays'],
                value: updatedTaskDays,
                error: '',
            };
            
            return updatedFormData;
        });
    }, [setFormData]);
    
    const validateFormElement = useValidateFormElement<SaleTaskForm>();
    const getSaleTaskFormInputComponent = (inputName: string) => {
        const inputElementIndex = inputName as keyof SaleTaskForm;
        const inputProps = validateFormElement(inputElementIndex, formData);
        if (inputProps.type === 'select') {
            return getElement(inputElementIndex, inputProps, { onSelect: onReactSelectChange });
        }
        if (inputElementIndex === 'taskCompletionDate') {
            return getElement(inputElementIndex, inputProps, { onBlur, onChange: onTaskCompletionDateChange });
        }
        return getElement(inputElementIndex, inputProps, { onBlur, onChange });
    };
    
    const { taskPaymentType } = formData;
    
    useEffect(() => {
        // toggle payment amount element: Fixed amount or Percentage(%) of sale
        setFormData((prev) => {
            const currentPaymentType = taskPaymentType.value;
            const value = currentPaymentType === '0' ? 0 : +prev.taskPayment.value;
            const type = currentPaymentType === 'fixed' ? 'text' : 'select';
            const label = currentPaymentType === 'fixed' ? 'Payment amount (ZAR)' : 'Payment amount (% of order amount)';
            const required = currentPaymentType === 'fixed' || currentPaymentType === 'percentage';
            return { ...prev, taskPayment: { ...prev.taskPayment, error: '', label, required, type, value } };
        });
    }, [taskPaymentType.value, setFormData]);
    
    const showTaskPaymentElement = taskPaymentType.value === 'fixed' || taskPaymentType.value === 'percentage';
    
    if (loadTask || !saleTaskId || !saleTask) {
        return <ContainerSpinner />;
    }
    
    const handleSwitchTab: ButtonClickFn = (event) => {
        event.preventDefault();
        const target = event.currentTarget;
        if (target && target.dataset.tab) {
            const tabId = target.dataset.tab;
            if (tabId === 'cancel') {
                navigate((previousPath || -1) as To, { replace: true });
                return;
            }
            switchTab(tabId);
        }
    };
    
    const { documents, taskAction, taskName, taskDescription, taskDocument } = saleTask;
    
    const documentsRequired = formData.taskStatus.value === 'completed' && !!taskDocument && uploadedFiles.length === 0;
    const disableSave = formInvalid || documentsRequired || formData.comments.value?.trim() === '';
    const estimate = taskAction === 'estimate' && documentsRequired;
    
    return (
        <div className='tab-content flex-fill'>
            <div className={`tab-pane fade ${activeTab === 'task-info' ? 'active show' : ''}`} id='task-info'>
                {activeTab === 'task-info' && (
                    <Form onSubmit={onSubmit}>
                        <div className='row mb-5 pb-5'>
                            <div className='col-lg-6 offset-lg-3 col-md-10 offset-md-1 col-sm-10 offset-sm-1 mb-5'>
                                <div className='d-flex flex-column pt-2 pb-2'>
                                    <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                                        <div className='row'>
                                            <div className='col-10 d-flex flex-row align-items-center'>
                                                <span className='pr-3'>
                                                    <i className='custom-icon icon activity' />
                                                </span>
                                                <div className=''>
                                                    <h6 className='mt-1 mb-0'>{taskName}</h6>
                                                    <p className='mb-0'>{taskDescription}</p>
                                                </div>
                                            </div>
                                            <div className='col-2'>
                                                <Button
                                                    onClick={toggleShowTaskDetail}
                                                    className='btn-default btn-block tooltip-left'
                                                    data-tooltip={`${showTaskDetail ? 'Close' : 'More'}`}
                                                    disabled={(documents || []).length === 0}
                                                >
                                                    <i className={`custom-icon icon icon-only ${showTaskDetail ? 'close' : 'chevron-down'}`} />
                                                </Button>
                                            </div>
                                        </div>
                                        {showTaskDetail && (
                                            <>
                                                <hr className='default' />
                                                {(documents || []).map((doc, index) => (
                                                    <DocumentListItem
                                                        key={doc.docId}
                                                        className={`px-2 ${index === (documents || []).length - 1 ? 'mb-0' : ''}`}
                                                        doc={doc}
                                                    />
                                                ))}
                                            </>
                                        )}
                                    </div>
                                    
                                    <div className='row mb-2'>
                                        <div className='col-12'>
                                            <input
                                                accept='.pdf'
                                                onChange={onUpload}
                                                ref={uploadInput}
                                                type='file'
                                                style={{ opacity: 0, position: 'absolute' }}
                                                multiple={!estimate}
                                            />
                                            <Button
                                                className='btn-success btn-block count-badge badge-simple'
                                                data-badge={uploadedFiles.length}
                                                onClick={handleUpload}>
                                                <i className='custom-icon icon left-icon upload' />
                                                Upload Task Document(s)
                                            </Button>
                                        </div>
                                    </div>
                                    <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                                        <div className='d-flex flex-row align-items-center'>
                                            <span className='pr-3'>
                                                <i className='custom-icon icon calendar' />
                                            </span>
                                            <h6>Status & Completion Date</h6>
                                        </div>
                                        <hr className='default' />
                                        {getSaleTaskFormInputComponent('taskStatus')}
                                        {getSaleTaskFormInputComponent('taskCompletionDate')}
                                    </div>
                                    {taskAction === 'penalty' && (
                                        <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                                            <div className='d-flex flex-row align-items-center'>
                                            <span className='pr-3'>
                                                <i className='custom-icon icon money-1' />
                                            </span>
                                                <h6>Payment Info</h6>
                                            </div>
                                            <hr className='default' />
                                            {getSaleTaskFormInputComponent('taskPaymentType')}
                                            {showTaskPaymentElement && (
                                                getSaleTaskFormInputComponent('taskPayment')
                                            )}
                                        </div>
                                    )}
                                    {getSaleTaskFormInputComponent('taskFrequency')}
                                    {getSaleTaskFormInputComponent('comments')}
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
                                                    data-tab='cancel'
                                                    onClick={handleSwitchTab}
                                                >
                                                    Cancel
                                                </Button>
                                            </div>
                                            <div className='col-8 offset-1 pr-sm-0 pl-sm-0'>
                                                <Button
                                                    type='submit'
                                                    className={`btn-${estimate ? 'primary' : 'success'} btn-block`}
                                                    disabled={disableSave}
                                                >
                                                    <i className={`custom-icon icon right-icon ${estimate ? 'chevrons-right' : 'save'}`} />
                                                    {estimate ? 'Next' : 'Save'}
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </StickyFooter>
                    </Form>
                )}
            </div>
            <div className={`tab-pane fade ${activeTab === 'job-info' ? 'active show' : ''}`} id='job-info'>
                {jobEstimate && activeTab === 'job-info' && (
                    <>
                        <div className='row mb-5 pb-5'>
                            <div className='col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-sm-10 offset-sm-1 mb-5'>
                                <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                                    <div className='row'>
                                        <div className='col-10 d-flex flex-row align-items-center'>
                                                <span className='pr-3'>
                                                    <i className='custom-icon icon activity' />
                                                </span>
                                            <div className=''>
                                                <h6 className='mt-1 mb-0'>{taskName}</h6>
                                                <p className='mb-0'>{taskDescription}</p>
                                            </div>
                                        </div>
                                        <div className='col-2'>
                                            <Button
                                                onClick={toggleShowTaskDetail}
                                                className='btn-default btn-block tooltip-left'
                                                data-tooltip={`${showTaskDetail ? 'Close' : 'More'}`}
                                                disabled={(documents || []).length === 0}
                                            >
                                                <i className={`custom-icon icon icon-only ${showTaskDetail ? 'close' : 'chevron-down'}`} />
                                            </Button>
                                        </div>
                                    </div>
                                    {showTaskDetail && (
                                        <>
                                            <hr className='default' />
                                            {(documents || []).map((doc, index) => (
                                                <DocumentListItem
                                                    key={doc.docId}
                                                    className={`px-2 ${index === (documents || []).length - 1 ? 'mb-0' : ''}`}
                                                    doc={doc}
                                                />
                                            ))}
                                        </>
                                    )}
                                    <hr className='default' />
                                    <div className='d-flex align-items-center justify-content-center'>
                                        <JobView job={jobEstimate} />
                                    </div>
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
                                                    data-tab='task-info'
                                                    onClick={handleSwitchTab}
                                                >
                                                    Back
                                                </Button>
                                            </div>
                                            <div className='col-8 offset-1 pr-sm-0 pl-sm-0'>
                                                <Button
                                                    type='submit'
                                                    className='btn-success btn-block'
                                                    onClick={submitTask}
                                                >
                                                    <i className={`custom-icon icon right-icon save`} />
                                                    Save
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </StickyFooter>
                    </>
                )}
            </div>
        </div>
    );
};
