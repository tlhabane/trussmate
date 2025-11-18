import React, { JSX, useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { addDays, format } from 'date-fns';
import { useNavigate } from 'react-router-dom';
import {
    Button,
    ContainerSpinner,
    EmptyAdditionalNotice,
    EmptyListContainer,
    EmptyNoticeButton,
    getRecordsDisplayedOptions,
    PaymentModalForm,
    usePaymentForm,
    PDFViewer,
    pagingLinkClickHandler,
    recordsPerPageSelectionHandler,
    SearchAndFilter,
    SectionTitleContainer,
    SectionTitleColumn,
    SectionTitleLabel,
    SelectStatus,
    TaskListItem,
    TaskNameAndDescription,
    TaskCompletionDate,
    TaskTeam,
    DataListFilterModal,
    useDataListFilterFunctions,
} from '../../components';
import { ModalPrompt, useLayoutContext } from '../../containers';
import { SaleListItem } from './components';
import {
    formatNumber,
    formatPrice,
    formatSales,
    sortDates,
    toggleButtonLoadingState,
    useAxios,
    useHttpRequestConfig,
    useFetchData,
    useJobAxios,
} from '../../utils';
import {
    useSearchHandlers,
    useExpandListItem,
    usePromiseToast,
    usePreviousLocation,
} from '../../hooks';
import { SaleList, SaleTaskList, TaskStatus } from '../../models';
import type {
    ButtonClickFn,
    HTMLElementClickFn,
    LinkClickFn,
    Pagination,
    ReactSelectSingleOption,
} from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';
import { InputChangeFn } from '../../types';

export default function Sales(): JSX.Element {
    document.title = `Sales :: ${APP_NAME}`;
    
    const initFilterParams = useMemo<Record<string, any>>(
        () => ({
            customerId: '',
            search: '',
            saleStatus: '',
            page: 1,
            recordsPerPage: 10,
            startDate: format(addDays(new Date(), -90), 'yyyy-MM-dd'),
            endDate: format(new Date(), 'yyyy-MM-dd'),
        }),
        [],
    );
    
    const [filterParams, setFilterParams] = useState(initFilterParams);
    
    const fetchConfig = useMemo(() => ({
        url: '/sale',
        queryKey: ['sales'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    
    const [loadSaleList, setLoadSaleList] = useState(false);
    const [saleList, setSaleList] = useState<SaleList[]>([]);
    const [saleMap, setSaleMap] = useState<Record<string, SaleList[]>>({});
    const [pagination, setPagination] = useState<Pagination | null>(null);
    const { data, isLoading, isFetching, refetch } = useFetchData(fetchConfig);
    
    const { authorisedUser } = useLayoutContext();
    const updateData = useCallback((data?: any) => {
        if (data) {
            const updatedPagination = data?.pagination as Pagination;
            const updatedSaleList = formatSales((data?.records || []) as SaleList[], authorisedUser.userRole);
            
            const sortedSaleDates = sortDates(updatedSaleList.map(({ saleDate }) => saleDate));
            const formattedSaleDates = sortedSaleDates
                .map((date) => format(date, 'yyyy/MM/dd'))
                .filter((value, index, self) => {
                    return self.indexOf(value) === index;
                });
            
            const updatedSaleMap = formattedSaleDates.reduce((acc, date) => {
                acc[date] = updatedSaleList.filter((sale) => (
                    format(new Date(sale.saleDate), 'yyyy/MM/dd') === date
                ));
                return acc;
            }, {} as Record<string, SaleList[]>);
            
            setSaleMap(updatedSaleMap);
            setSaleList(updatedSaleList);
            setPagination(updatedPagination);
            setLoadSaleList(false);
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
        setLoadSaleList(true);
        refetch()
            .then(({ data: updatedData }) => {
                updateData(updatedData);
            })
            .finally(() => {
                setLoadSaleList(false);
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
    
    const {
        filterModalOpen,
        handleClearFilterParams,
        handlerFilterInputChange,
        handleSelectFilterOption,
        handleToggleFilterModal,
        toggleFilterModal,
    } = useDataListFilterFunctions(setFilterParams, resetFilterParams);
    
    const handlePaginationLinkClick: LinkClickFn<void> = (event) => {
        pagingLinkClickHandler(event, setFilterParams, setLoadSaleList);
    };
    
    const handleUpdateRecordsPerPage: HTMLElementClickFn<void> = (event) => {
        recordsPerPageSelectionHandler(event, setSaleList, setFilterParams, setLoadSaleList);
    };
    
    const handleRefreshClientList: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        updateRecords();
    };
    
    
    const navigate = useNavigate();
    const location = usePreviousLocation();
    
    const handleUpdateSale: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { sale } = event.currentTarget.dataset;
        if (sale) {
            navigate(`/sale/management/${sale}`, { state: { from: location?.pathname } });
        }
    };
    
    const handleUpdateSaleStatus: ButtonClickFn<void> = (event) => {
        event.preventDefault();
    };
    
    const [promptUser, setPromptUser] = useState(false);
    const [confirmationButton, setConfirmationButton] = useState(<Button />);
    const saleDeleteMessage = (
        <>
            <h5>Are your sure you want to delete the selected sale?</h5>
            <p className='small'>The effects of this action cannot be reversed.</p>
        </>
    );
    
    const handleDismissPromptModal = () => {
        setConfirmationButton(<Button />);
        setPromptUser(false);
    };
    
    const axios = useAxios();
    const toast = usePromiseToast();
    const getHttpRequestConfig = useHttpRequestConfig();
    
    const handleProceedDeletingSale: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const button = event.currentTarget;
        try {
            toggleButtonLoadingState(button);
            const { sale } = event.currentTarget.dataset;
            const httpRequestConfig = {
                ...getHttpRequestConfig('DELETE'),
                url: '/sale',
                data: { saleId: sale || '' },
            };
            
            await toast(axios(httpRequestConfig, undefined, event.currentTarget));
            toggleButtonLoadingState(button);
            updateRecords();
        } catch (error: any) {
            toggleButtonLoadingState(button);
        }
    };
    
    const handleDeleteSale: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { sale } = event.currentTarget.dataset;
        if (sale) {
            setConfirmationButton(
                <Button
                    className='btn-danger btn-block'
                    data-sale={sale}
                    onClick={handleProceedDeletingSale}
                >
                    <i className='custom-icon icon left-icon trash' />
                    Delete
                </Button>,
            );
            setPromptUser(true);
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
                updateRecords();
                toggleButtonLoadingState(button);
            } catch (error: any) {
                toggleButtonLoadingState(button);
            }
        }
    };
    
    const handleManageSaleTask: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        if (task) {
            navigate(`/sale/task/management/${task}`, { state: { from: location?.pathname } });
        }
    };
    
    const estimateJobAxios = useJobAxios();
    const taskFormData = useRef<FormData | null>(null);
    const fileUploadTask = useRef<SaleTaskList | null>(null);
    
    const [saveAndSendHandler, setSaveAndSendHandler] = useState<ButtonClickFn<void> | undefined>(undefined);
    const [saveOnlyHandler, setSaveOnlyHandler] = useState<ButtonClickFn<void> | undefined>(undefined);
    
    const [previewDocument, setPreviewDocument] = useState(false);
    const initialDocumentPreviewParams = useMemo<Record<string, string>>(() => ({
        documentTitle: '',
        documentUrl: '',
    }), []);
    
    const [documentPreviewParams, setDocumentPreviewParams] = useState(initialDocumentPreviewParams);
    
    const closeDocumentPreview = useCallback(() => {
        setDocumentPreviewParams(initialDocumentPreviewParams);
        setPreviewDocument(false);
    }, [initialDocumentPreviewParams]);
    
    
    const uploadFileInput = useRef<HTMLInputElement | null>(null);
    const handleFileUpload: ButtonClickFn<void> | HTMLElementClickFn<void> = (event: any) => {
        event.preventDefault();
        const { task } = event.currentTarget.dataset;
        const selectedTask = saleList.map(s => s.tasks).flat().find((item) => item.saleTaskId === task);
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
        const selectedTask = saleList.map(s => s.tasks).flat().find((item) => item.saleTaskId === task);
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
        const selectedTask = saleList.map(s => s.tasks).flat().find((item) => item.saleTaskId === task);
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
        const selectedTask = saleList.map(s => s.tasks).flat().find((item) => item.saleTaskId === task);
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
        const selectedTask = saleList.map(s => s.tasks).flat().find((item) => item.saleTaskId === task);
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
        
        const selectedTask = saleList.map(s => s.tasks).flat().find((item) => item.saleTaskId === saleTaskId);
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
    }, [axios, closeDocumentPreview, getHttpRequestConfig, saleList, sendInvoice, sendQuotation, taskFormData, toast, updateRecords]);
    
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
    }, [axios, getHttpRequestConfig, closeDocumentPreview, taskFormData, toast, updateRecords]);
    
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
        const selectedTask = saleList.map(s => s.tasks).flat().find((item) => item.saleTaskId === task);
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
        const selectedTask = saleList.map(s => s.tasks).flat().find((item) => item.saleTaskId === task);
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
        const selectedTask = saleList.map(s => s.tasks).flat().find((t) => t.saleTaskId === task);
        if (selectedTask) {
            const { invoiceNo, saleTaskId, balanceDue } = selectedTask;
            setFormData((prevState) => ({
                ...prevState,
                invoiceNo: {
                    ...prevState.invoiceNo,
                    value: invoiceNo,
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
                    value: `Payment Invoice #${invoiceNo}`,
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
    
    const { setAddNewHandler } = useLayoutContext();
    const addNewSale: ButtonClickFn<void> = useCallback((event) => {
        event?.preventDefault();
        navigate('/sale/management', { state: { from: location?.pathname } });
    }, [navigate, location]);
    
    useEffect(() => {
        setAddNewHandler(() => addNewSale);
        return () => {
            setAddNewHandler(null);
        };
    }, [addNewSale, setAddNewHandler, navigate]);
    
    const {
        handleClearSearch,
        handleSearchValueChange,
        searchInputRef,
    } = useSearchHandlers(setFilterParams, updateRecords);
    const { expandListItem, listItemViewState } = useExpandListItem();
    
    const RenderSales = () => {
        if (isLoading && !data) {
            return <ContainerSpinner />;
        }
        
        if (saleList.length > 0) {
            const getPreviousTaskDays = (task: SaleTaskList): number => (
                saleList.map(s => s.tasks).flat().filter((t) => t.taskNo < task.taskNo)
                    .reduce((acc, curr) => acc + (curr.taskDays || 0), 0)
            );
            return (
                <div className='d-block mt-3 mb-5'>
                    {Object.entries(saleMap).map(([date, sales]) => (
                        <div className='d-flex flex-column' key={date}>
                            <SectionTitleContainer>
                                <SectionTitleColumn>
                                    <i className='custom-icon icon right-icon date-time mr-3' />
                                    <SectionTitleLabel>{date}</SectionTitleLabel>
                                </SectionTitleColumn>
                                <SectionTitleColumn>
                                    <i className='custom-icon icon right-icon money-1 mr-3' />
                                    <SectionTitleLabel>
                                        {formatPrice(sales.reduce((total, sale) => total + sale.saleTotal, 0), '')}
                                    </SectionTitleLabel>
                                    <span className='v-divider' />
                                    <SectionTitleLabel>
                                        {`${sales.length} Sale(s)`}
                                    </SectionTitleLabel>
                                </SectionTitleColumn>
                            </SectionTitleContainer>
                            {sales.map((sale) => (
                                <SaleListItem
                                    key={sale.saleId}
                                    sale={sale}
                                    approveSale={handleUpdateSaleStatus}
                                    deleteSale={handleDeleteSale}
                                    toggleListItem={expandListItem}
                                    updateSale={handleUpdateSale}
                                    viewSaleDetail={listItemViewState[sale.saleId] || false}
                                    saleTaskList={
                                        <>
                                            {sale.tasks.map((task) => (
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
                                                >
                                                    <div className='col-4'>
                                                        <TaskNameAndDescription
                                                            taskName={task.taskName}
                                                            taskDescription={task.taskDescription}
                                                        />
                                                    </div>
                                                    <div className='col-3'>
                                                        <TaskTeam assignedTo={task.assignedTo} />
                                                    </div>
                                                    <div className='col-3'>
                                                        <TaskCompletionDate
                                                            taskCompletionDate={task.taskCompletionDate}
                                                            taskStatus={task.taskStatus}
                                                        />
                                                    </div>
                                                </TaskListItem>
                                            ))}
                                        </>
                                    }
                                />
                            ))}
                        </div>
                    ))}
                
                </div>
            );
        }
        
        const listFiltered = filterParams.search.trim().length > 0;
        
        return (
            <div className='row p-5'>
                <div className='col-md-6 offset-md-3'>
                    <EmptyListContainer>
                        <i className='custom-icon icon users' style={{ width: 48, height: 48 }} />
                        <p className='hint-text text-center mt-2'>
                            {listFiltered ? (
                                <>No sales matching your criteria available</>
                            ) : (
                                <>
                                    No sales currently available.
                                    <br /> Once available, all your sales will be displayed here.
                                </>
                            )}
                        </p>
                        <EmptyNoticeButton onClick={addNewSale}>
                            Add {listFiltered ? 'Missing' : 'New'} Sale
                        </EmptyNoticeButton>
                        <EmptyAdditionalNotice>
                            <Button className='btn-link'>
                                Upload {listFiltered ? 'Missing' : 'New'} Sales
                            </Button>
                        </EmptyAdditionalNotice>
                    </EmptyListContainer>
                </div>
            </div>
        );
    };
    
    return (
        <>
            <DataListFilterModal
                filterParams={filterParams}
                filterInputChangeFn={handlerFilterInputChange}
                filterParamSelectFn={handleSelectFilterOption}
                handleClearListFilter={handleClearFilterParams}
                handleToggleModal={toggleFilterModal}
                modalOpened={filterModalOpen}
            >
                <SelectStatus
                    datalistFilter={true}
                    selectedOption={filterParams.saleStatus}
                    onChange={(option) => {
                        handleSelectFilterOption('saleStatus', option as ReactSelectSingleOption);
                    }}
                />
            </DataListFilterModal>
            <ModalPrompt
                openModalPrompt={promptUser}
                dismissModalPrompt={handleDismissPromptModal}
                promptConfirmationButton={confirmationButton}
            >
                {saleDeleteMessage}
            </ModalPrompt>
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
                    loading={loadSaleList || isFetching}
                    filterParams={filterParams}
                    pagination={pagination}
                    clearSearchHandler={handleClearSearch}
                    refreshDataHandler={handleRefreshClientList}
                    searchValueChangeHandler={handleSearchValueChange}
                    recordsPerPageOptions={recordsPerPageOptions}
                    resetFilterParamsHandler={handleResetFilterParams}
                    paginationLinkHandler={handlePaginationLinkClick}
                    toggleFilterOptionsHandler={handleToggleFilterModal}
                    filterOptionsVisible={filterModalOpen}
                    updateRecordsPerPageHandler={handleUpdateRecordsPerPage}
                />
                <input
                    accept='.pdf'
                    onChange={onUploadFile}
                    ref={uploadFileInput}
                    type='file'
                    style={{ opacity: 0, position: 'absolute' }}
                />
                <RenderSales />
            </div>
        </>
    );
}
