import React, { JSX, useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { format } from 'date-fns';
import {
    ContainerSpinner,
    EmptyListContainer,
    getRecordsDisplayedOptions,
    pagingLinkClickHandler,
    PaymentModalForm,
    usePaymentForm,
    PDFViewer,
    recordsPerPageSelectionHandler,
    SearchAndFilter,
    SectionTitleColumn,
    SectionTitleContainer,
    SectionTitleLabel,
    TaskListItem,
} from '../../components';
import {
    formatNumber,
    toggleButtonLoadingState,
    useAxios,
    useHttpRequestConfig,
    useFetchData,
    useJobAxios,
} from '../../utils';
import { usePreviousLocation, usePromiseToast, useSearchHandlers } from '../../hooks';
import { useLayoutContext } from '../../containers';
import { SaleTaskList, TaskStatus } from '../../models';
import type { ButtonClickFn, HTMLElementClickFn, LinkClickFn, Pagination, ReactSelectSingleOption } from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';
import { InputChangeFn } from '../../types';

export default function InboxV1(): JSX.Element {
    document.title = `Inbox :: ${APP_NAME}`;
    const { authorisedUser } = useLayoutContext();
    
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
        url: '/sale/task',
        queryKey: ['saleTasks'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    
    const [loadSaleTaskList, setLoadSaleTaskList] = useState(false);
    const [saleTaskList, setSaleTaskList] = useState<SaleTaskList[]>([]);
    const [saleTaskMap, setSaleTaskMap] = useState<Record<string, SaleTaskList[]>>({});
    const [pagination, setPagination] = useState<Pagination | null>(null);
    const { data, isLoading, isFetching, refetch } = useFetchData(fetchConfig);
    
    const splitKey = (key: string) => {
        const [taskDate, saleNo] = key.split(';');
        return { taskDate, saleNo };
    };
    
    const updateData = useCallback((data?: any) => {
        if (data) {
            const updatedSaleTaskList = (data?.records || []) as SaleTaskList[];
            const updatedPagination = data?.pagination as Pagination;
            const saleNumbers = updatedSaleTaskList.map((task) => `${task.taskDate};${task.saleNo}`).filter((value, index, self) => {
                return self.indexOf(value) === index;
            });
            
            const updatedSaleTaskMap: Record<string, SaleTaskList[]> = {};
            saleNumbers.forEach((saleNumber) => {
                const saleNo = splitKey(saleNumber).saleNo;
                const sortedSaleTasks = updatedSaleTaskList.filter((task) => task.saleNo === saleNo)
                    .sort((a, b) => a.taskNo - b.taskNo);
                
                updatedSaleTaskMap[saleNumber] = sortedSaleTasks.map((task, index) => {
                    const previousTask = sortedSaleTasks[index - 1];
                    if (previousTask) {
                        return { ...task, taskEnabled: previousTask.taskStatus === 'completed' };
                    }
                    return { ...task, taskEnabled: true };
                });
            });
            
            const { userRole } = authorisedUser;
            
            let filteredMapKeys = updatedSaleTaskMap;
            
            if (userRole.toString() === 'production' || userRole.toString() === 'estimator') {
                filteredMapKeys = Object.keys(updatedSaleTaskMap).reduce((acc, key) => {
                    acc[key] = updatedSaleTaskMap[key].filter((t) => t.assignedTo === userRole.toString());
                    return acc;
                }, {} as Record<string, SaleTaskList[]>);
            }
            
            /*sortedMapKeys = Object.keys(updatedSaleTaskMap).sort((a, b) => {
                const saleNoA = splitKey(a).saleNo;
                const saleNoB = splitKey(b).saleNo;
                return +saleNoB - +saleNoA;
            });*/
            const sortedMapKeys = Object.keys(filteredMapKeys).sort((a, b) => {
                const saleNoA = splitKey(a).saleNo;
                const saleNoB = splitKey(b).saleNo;
                return +saleNoB - +saleNoA;
            });
            const sortedSaleTaskMap: Record<string, SaleTaskList[]> = {};
            sortedMapKeys.forEach((key) => {
                // sortedSaleTaskMap[key] = filteredMapKeys[key];
                if (filteredMapKeys[key].length > 0) {
                    sortedSaleTaskMap[key] = filteredMapKeys[key];
                }
            });
            setSaleTaskMap(sortedSaleTaskMap);
            setSaleTaskList(updatedSaleTaskList);
            setPagination(updatedPagination);
            setLoadSaleTaskList(false);
        }
    }, [authorisedUser]);
    
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
        setLoadSaleTaskList(true);
        refetch()
            .then(({ data: updatedData }) => {
                updateData(updatedData);
            })
            .finally(() => {
                setLoadSaleTaskList(false);
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
        pagingLinkClickHandler(event, setFilterParams, setLoadSaleTaskList);
    };
    
    const handleUpdateRecordsPerPage: HTMLElementClickFn<void> = (event) => {
        recordsPerPageSelectionHandler(event, setSaleTaskList, setFilterParams, setLoadSaleTaskList);
    };
    
    const handleRefreshClientList: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        updateRecords();
    };
    
    useEffect(() => {
        const searchTimer = setTimeout(() => {
            setLoadSaleTaskList(true);
        }, 1500);
        
        return () => {
            clearTimeout(searchTimer);
        };
    }, [filterParams.search]);
    
    const navigate = useNavigate();
    const location = usePreviousLocation();
    
    const handleManageSaleTask: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        if (task) {
            navigate(`/sale/task/management/${task}`, { state: { from: location?.pathname } });
        }
    };
    
    const axios = useAxios();
    const getHttpRequestConfig = useHttpRequestConfig();
    const toast = usePromiseToast();
    
    const [previewDocument, setPreviewDocument] = useState(false);
    const initialDocumentPreviewParams = useMemo<Record<string, string>>(() => ({
        documentTitle: '',
        documentUrl: '',
    }), []);
    
    const [documentPreviewParams, setDocumentPreviewParams] = useState(initialDocumentPreviewParams);
    
    const closeDocumentPreview = useCallback(() => {
        setDocumentPreviewParams(initialDocumentPreviewParams);
        setPreviewDocument(false);
        setSaveOnlyHandler(undefined);
    }, [initialDocumentPreviewParams]);
    
    const estimateJobAxios = useJobAxios();
    const fileUploadTask = useRef<SaleTaskList | null>(null);
    const uploadFileInput = useRef<HTMLInputElement | null>(null);
    
    const handleFileUpload: ButtonClickFn<void> | HTMLElementClickFn<void> = (event: any) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        const selectedTask = saleTaskList.find((item) => item.saleTaskId === task);
        if (selectedTask) {
            const { taskAction } = selectedTask;
            fileUploadTask.current = selectedTask;
            
            const fileUploadElement = uploadFileInput.current as HTMLInputElement;
            if (taskAction === 'estimate' && fileUploadElement?.hasAttribute('multiple')) {
                fileUploadElement?.removeAttribute('multiple');
            } else {
                fileUploadElement?.setAttribute('multiple', '');
            }
            fileUploadElement?.click();
        }
    };
    
    const downloadInvoice = useCallback(async (task?: SaleTaskList, loadingMessage = 'Download invoice...') => {
        if (task) {
            try {
                const { saleTaskId, saleId } = task;
                const httpRequestConfig = {
                    ...await getHttpRequestConfig(),
                    url: '/invoice/download',
                    params: { saleTaskId, saleId },
                };
                return toast(axios(httpRequestConfig), loadingMessage);
            } catch (error: any) {
                console.log(error);
            }
        }
    }, [axios, getHttpRequestConfig, toast]);
    
    const sendInvoice = useCallback(async (task?: SaleTaskList) => {
        if (task) {
            try {
                const { saleTaskId, saleId } = task;
                const httpRequestConfig = {
                    ...await getHttpRequestConfig('POST'),
                    url: '/invoice/send',
                    data: { saleTaskId, saleId },
                };
                await toast(axios(httpRequestConfig), 'Sending...');
            } catch (error: any) {
                console.log(error);
            }
        }
    }, [axios, getHttpRequestConfig, toast]);
    
    const downloadQuotation = useCallback(async (task?: SaleTaskList, loadingMessage = 'Download quotation...') => {
        if (task) {
            try {
                const { saleTaskId, saleId } = task;
                const httpRequestConfig = {
                    ...await getHttpRequestConfig(),
                    url: '/sale/quotation/download',
                    params: { saleTaskId, saleId },
                };
                return toast(axios(httpRequestConfig), loadingMessage);
            } catch (error: any) {
                console.log(error);
            }
        }
    }, [axios, getHttpRequestConfig, toast]);
    
    const sendQuotation = useCallback(async (task?: SaleTaskList) => {
        if (task) {
            try {
                const { saleTaskId, saleId } = task;
                const httpRequestConfig = {
                    ...await getHttpRequestConfig('POST'),
                    url: '/sale/quotation/send',
                    data: { saleTaskId, saleId },
                };
                await toast(axios(httpRequestConfig), 'Sending...');
            } catch (error: any) {
                console.log(error);
            }
        }
    }, [axios, getHttpRequestConfig, toast]);
    
    const handlePreviewQuotation: HTMLElementClickFn<void> = async (event) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        const selectedTask = saleTaskList.find((item) => item.saleTaskId === task);
        if (selectedTask) {
            const response = await downloadQuotation(selectedTask, 'Loading preview...');
            if (response) {
                setDocumentPreviewParams({
                    documentTitle: response.filename,
                    documentUrl: response.file,
                });
                setPreviewDocument(true);
            }
        }
    };
    
    const handleDownloadQuotation: HTMLElementClickFn<void> = async (event) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        const selectedTask = saleTaskList.find((item) => item.saleTaskId === task);
        if (selectedTask) {
            const response = await downloadQuotation(selectedTask, 'Downloading...');
            if (response) {
                window.open(response.file, '_blank');
            }
        }
    };
    
    const handlePreviewInvoice: HTMLElementClickFn<void> = async (event) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        const selectedTask = saleTaskList.find((item) => item.saleTaskId === task);
        if (selectedTask) {
            const response = await downloadInvoice(selectedTask, 'Loading preview...');
            if (response) {
                setDocumentPreviewParams({
                    documentTitle: response.filename,
                    documentUrl: response.file,
                });
                setPreviewDocument(true);
            }
        }
    };
    
    const handleDownloadInvoice: HTMLElementClickFn<void> = async (event) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        const selectedTask = saleTaskList.find((item) => item.saleTaskId === task);
        if (selectedTask) {
            try {
                const response = await downloadInvoice(selectedTask, 'Downloading...');
                if (response) {
                    window.open(response.file, '_blank');
                }
            } catch (error: any) {
                console.log(error);
            }
        }
    };
    
    
    const taskFormData = useRef<FormData | null>(null);
    const [saveAndSendHandler, setSaveAndSendHandler] = useState<ButtonClickFn<void> | undefined>(undefined);
    const [saveOnlyHandler, setSaveOnlyHandler] = useState<ButtonClickFn<void> | undefined>(undefined);
    
    const getRequestData = (): FormData | Record<string, any> => {
        if (taskFormData.current) {
            const formData = new FormData();
            (taskFormData.current as FormData).forEach((value, key) => {
                if (value instanceof File) {
                    formData.append(key, value, value.name);
                } else {
                    formData.append(key, value);
                }
            });
            
            return formData;
        }
        
        const { saleTaskId, saleId, taskStatus } = fileUploadTask.current as SaleTaskList;
        return { saleTaskId, saleId, taskStatus };
    };
    
    const handleSaveAndSend: ButtonClickFn<void> = useCallback(async (event) => {
        event.preventDefault();
        if (!taskFormData.current && !fileUploadTask.current) {
            return;
        }
        const button = event.currentTarget;
        const saleTaskId = taskFormData.current ?
            (taskFormData.current as FormData).get('saleTaskId') :
            (fileUploadTask.current as SaleTaskList)?.saleTaskId || '';
        
        const selectedTask = saleTaskList.find((item) => item.saleTaskId === saleTaskId);
        if (!selectedTask) {
            return;
        }
        
        try {
            toggleButtonLoadingState(button);
            const contentType = taskFormData.current ? 'multipart/form-data' : 'application/json';
            const data = getRequestData();
            
            const httpRequestConfig = {
                ...await getHttpRequestConfig('POST', contentType),
                url: '/sale/task/status',
                data,
            };
            await toast(axios(httpRequestConfig), 'Updating task...');
            
            const { taskAction, triggerType } = selectedTask;
            if (triggerType === 'automatic') {
                updateRecords();
                fileUploadTask.current = null;
                taskFormData.current = null;
                setSaveAndSendHandler(undefined);
                setSaveOnlyHandler(undefined);
                toggleButtonLoadingState(button);
                closeDocumentPreview();
                return;
            }
            
            const invoiceAction = taskAction.includes('invoice');
            if (invoiceAction) {
                await sendInvoice(selectedTask);
            } else {
                await sendQuotation(selectedTask);
            }
            updateRecords();
            fileUploadTask.current = null;
            taskFormData.current = null;
            setSaveAndSendHandler(undefined);
            setSaveOnlyHandler(undefined);
            toggleButtonLoadingState(button);
            closeDocumentPreview();
        } catch (error: any) {
            toggleButtonLoadingState(button);
        }
    }, [axios, closeDocumentPreview, getHttpRequestConfig, saleTaskList, sendInvoice, sendQuotation, taskFormData, toast, updateRecords]);
    
    const handleSaveAndUpload: ButtonClickFn<void> = useCallback(async (event) => {
        event.preventDefault();
        if (!taskFormData.current && !fileUploadTask.current) {
            return;
        }
        const button = event.currentTarget;
        try {
            toggleButtonLoadingState(button);
            
            const contentType = taskFormData.current ? 'multipart/form-data' : 'application/json';
            const data = getRequestData();
            
            const httpRequestConfig = {
                ...await getHttpRequestConfig('POST', contentType),
                url: '/sale/task/status',
                data,
            };
            
            await toast(axios(httpRequestConfig), 'Updating task...');
            updateRecords();
            fileUploadTask.current = null;
            taskFormData.current = null;
            setSaveAndSendHandler(undefined);
            setSaveOnlyHandler(undefined);
            toggleButtonLoadingState(button);
            closeDocumentPreview();
        } catch (error: any) {
            toggleButtonLoadingState(button);
        }
    }, [axios, closeDocumentPreview, getHttpRequestConfig, taskFormData, toast, updateRecords]);
    
    const onUploadFile: InputChangeFn<HTMLInputElement> = async (event) => {
        const { files } = event.currentTarget;
        const button = event.currentTarget;
        
        if (files && fileUploadTask.current) {
            toggleButtonLoadingState(button);
            
            try {
                const fileArray = Array.from(files);
                for await (const file of fileArray) {
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
                    
                    const response = await toast(estimateJobAxios(httpRequestConfig), 'Processing estimate...');
                    
                    if (response?.success) {
                        const { saleId, saleTaskId } = fileUploadTask.current as SaleTaskList;
                        const rawEstimateData: Record<string, any> = JSON.parse(response.estimate);
                        const job = JSON.stringify({ ...rawEstimateData, jobNo: rawEstimateData.jobNumber });
                        const data = { saleId, saleTaskId, job, taskStatus: 'completed' };
                        const httpRequestConfig = {
                            ...await getHttpRequestConfig('POST'),
                            url: '/sale/estimate/preview',
                            data,
                        };
                        
                        const previewResponse = await toast(axios(httpRequestConfig), 'Loading preview...');
                        
                        const taskStatusFormData = new FormData();
                        taskStatusFormData.append('files[]', file, file.name);
                        Object.entries(data).forEach(([key, value]) => {
                            taskStatusFormData.append(key, value);
                        });
                        taskFormData.current = taskStatusFormData as FormData;
                        setSaveOnlyHandler(() => handleSaveAndUpload);
                        fileUploadTask.current = null;
                        
                        setDocumentPreviewParams({
                            documentTitle: previewResponse.filename,
                            documentUrl: previewResponse.file,
                        });
                        setPreviewDocument(true);
                        toggleButtonLoadingState(button);
                    }
                }
            } catch (error: any) {
                toggleButtonLoadingState(button);
            }
        }
    };
    
    const handleSendQuotation: ButtonClickFn<void> | HTMLElementClickFn<void> = async (event: any) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        const selectedTask = saleTaskList.find((item) => item.saleTaskId === task);
        if (selectedTask) {
            const { taskStatus } = selectedTask;
            if (taskStatus === 'pending') {
                fileUploadTask.current = { ...selectedTask, taskStatus: 'started' as TaskStatus };
                setSaveAndSendHandler(() => handleSaveAndSend);
                handlePreviewQuotation(event);
                return;
            }
            
            await sendQuotation(selectedTask);
        }
    };
    
    const handleSendInvoice: ButtonClickFn<void> | HTMLElementClickFn<void> = async (event: any) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        const selectedTask = saleTaskList.find((item) => item.saleTaskId === task);
        if (selectedTask) {
            const { taskStatus } = selectedTask;
            if (taskStatus === 'pending') {
                fileUploadTask.current = { ...selectedTask, taskStatus: 'started' as TaskStatus };
                setSaveAndSendHandler(() => handleSaveAndSend);
                handlePreviewInvoice(event);
                return;
            }
            
            await sendInvoice(selectedTask);
        }
    };
    
    const handleUpdateSaleTaskStatus: ButtonClickFn<void> | HTMLElementClickFn<void> = async (event: any) => {
        event.preventDefault();
        const { task, status } = event.currentTarget.dataset;
        const button = event.currentTarget;
        
        if (task && status) {
            try {
                toggleButtonLoadingState(button);
                const httpRequestConfig = {
                    ...await getHttpRequestConfig('POST'),
                    url: '/sale/task/status',
                    data: { saleTaskId: task, taskStatus: status },
                };
                await toast(axios(httpRequestConfig), 'Processing...');
                toggleButtonLoadingState(button);
                updateRecords();
            } catch (error: any) {
                toggleButtonLoadingState(button);
            }
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
    } = usePaymentForm();
    const [loadPaymentForm, setLoadPaymentForm] = useState(false);
    const toggleLoadPaymentForm = () => {
        setLoadPaymentForm((paymentFormLoaded) => !paymentFormLoaded);
    };
    const dismissPaymentForm = () => {
        resetForm();
        toggleLoadPaymentForm();
    };
    
    const handleNewPayment: ButtonClickFn<void> | HTMLElementClickFn<void> = async (event: any) => {
        const { task } = event.currentTarget.dataset;
        const selectedTask = saleTaskList.find((t) => t.saleTaskId === task);
        if (selectedTask) {
            const { saleNo, saleTaskId, balanceDue } = selectedTask;
            setFormData((prevState) => ({
                ...prevState,
                invoiceNo: {
                    ...prevState.invoiceNo,
                    value: saleNo,
                    error: '',
                },
                saleTaskId: {
                    ...prevState.saleTaskId,
                    value: saleTaskId,
                    error: '',
                },
                transactionAmount: {
                    ...prevState.transactionAmount,
                    value: +formatNumber(balanceDue),
                    error: '',
                },
                transactionDesc: {
                    ...prevState.transactionDesc,
                    value: `Payment Invoice #${saleNo}`,
                    error: '',
                },
                transactionDate: {
                    ...prevState.transactionDate,
                    value: format(new Date(), 'yyyy-MM-dd'),
                    error: '',
                },
            }));
            
            toggleLoadPaymentForm();
        }
    };
    
    const handlePaymentFormSubmission = handleSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            const httpRequestConfig = {
                ...await getHttpRequestConfig('POST'),
                url: '/transaction',
                data: { ...validated, transactionType: 'payment' },
            };
            
            await toast(axios(httpRequestConfig), 'Processing payment...');
            button?.classList.remove('loading');
            dismissPaymentForm();
            updateRecords();
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    const {
        handleClearSearch,
        handleSearchValueChange,
        searchInputRef,
    } = useSearchHandlers(setFilterParams, updateRecords);
    
    const RenderInbox = () => {
        if (isLoading && !data) {
            return <ContainerSpinner />;
        }
        
        if (saleTaskList.length > 0) {
            const getPreviousTaskDays = (task: SaleTaskList): number => (
                saleTaskList.filter((t) => t.taskNo < task.taskNo)
                    .reduce((acc, curr) => acc + (curr.taskDays || 0), 0)
            );
            return (
                <div className='d-block mt-3 mb-5'>
                    {Object.entries(saleTaskMap).map(([date, tasks]) => (
                        <div className='d-flex flex-column' key={date}>
                            <SectionTitleContainer>
                                <SectionTitleColumn>
                                    <i className='custom-icon icon right-icon date-time mr-3' />
                                    <SectionTitleLabel>
                                        {format(splitKey(date).taskDate, 'yyyy/MM/dd')}
                                    </SectionTitleLabel>
                                    <span className='v-divider' />
                                    <i className='custom-icon icon right-icon invoice mr-3' />
                                    <SectionTitleLabel>{splitKey(date).saleNo}</SectionTitleLabel>
                                </SectionTitleColumn>
                                <SectionTitleColumn>
                                    <i className='custom-icon icon right-icon activity mr-3' />
                                    <SectionTitleLabel>
                                        {`${tasks.length} Task(s)`}
                                    </SectionTitleLabel>
                                </SectionTitleColumn>
                            </SectionTitleContainer>
                            {tasks.map((task) => (
                                <TaskListItem
                                    key={task.saleTaskId}
                                    task={task}
                                    payInvoice={handleNewPayment}
                                    previousTaskDays={getPreviousTaskDays(task)}
                                    downloadInvoice={handleDownloadInvoice}
                                    previewInvoice={handlePreviewInvoice}
                                    sendInvoice={handleSendInvoice}
                                    downloadQuotation={handleDownloadQuotation}
                                    previewQuotation={handlePreviewQuotation}
                                    sendQuotation={handleSendQuotation}
                                    updateTask={handleManageSaleTask}
                                    updateTaskStatus={handleUpdateSaleTaskStatus}
                                    uploadEstimate={handleFileUpload}
                                />
                            ))}
                        </div>
                    ))}
                
                </div>
            );
        }
        
        const listFiltered = initFilterParams.search.trim().length > 0;
        
        return (
            <div className='d-flex flex-fill flex-column'>
                <div className='row p-5'>
                    <div className='col-md-6 offset-md-3'>
                        <EmptyListContainer>
                            <i className='custom-icon icon activity' style={{ width: 48, height: 48 }} />
                            <p className='hint-text text-center mt-2'>
                                {listFiltered ? (
                                    <>No items matching your criteria available in your inbox</>
                                ) : (
                                    <>
                                        No items currently available in your inbox.
                                        <br /> Once available, all your inbox items will be displayed here.
                                    </>
                                )}
                            </p>
                        </EmptyListContainer>
                    </div>
                </div>
            </div>
        );
    };
    
    
    return (
        <>
            <PaymentModalForm
                openModal={loadPaymentForm}
                toggleModal={dismissPaymentForm}
                getElement={getElement}
                formConfig={formData}
                formInvalid={formInvalid}
                onBlur={onBlur}
                onChange={onChange}
                onSelect={onReactSelectChange}
                onSubmit={handlePaymentFormSubmission}
            />
            <PDFViewer
                documentUrl={documentPreviewParams.documentUrl}
                previewDocument={previewDocument}
                previewTitle={documentPreviewParams.previewTitle}
                documentTitle={documentPreviewParams.documentTitle}
                closeDocumentPreview={closeDocumentPreview}
                saveAndSendHandler={saveAndSendHandler}
                saveOnlyHandler={saveOnlyHandler}
            />
            <div className='d-flex flex-fill flex-column'>
                <SearchAndFilter
                    ref={searchInputRef}
                    disabled={isLoading || isFetching}
                    loading={loadSaleTaskList || isFetching}
                    filterParams={filterParams}
                    pagination={pagination}
                    clearSearchHandler={handleClearSearch}
                    refreshDataHandler={handleRefreshClientList}
                    searchValueChangeHandler={handleSearchValueChange}
                    recordsPerPageOptions={recordsPerPageOptions}
                    resetFilterParamsHandler={handleResetFilterParams}
                    paginationLinkHandler={handlePaginationLinkClick}
                    updateRecordsPerPageHandler={handleUpdateRecordsPerPage}
                />
                <input
                    accept='.pdf'
                    onChange={onUploadFile}
                    ref={uploadFileInput}
                    type='file'
                    style={{ opacity: 0, position: 'absolute' }}
                />
                <RenderInbox />
            </div>
        </>
    );
}
