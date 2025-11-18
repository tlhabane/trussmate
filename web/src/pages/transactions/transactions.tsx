import React, { JSX, useCallback, useEffect, useMemo, useState } from 'react';
import { addDays, format } from 'date-fns';
import {
    ContainerSpinner,
    EmptyListContainer,
    getRecordsDisplayedOptions,
    pagingLinkClickHandler,
    recordsPerPageSelectionHandler,
    PDFViewer,
    PaymentModalForm,
    usePaymentForm,
    SelectInput,
    SearchAndFilter,
    SectionTitleContainer,
    SectionTitleColumn,
    SectionTitleLabel,
    Button,
    DataListFilterModal,
    useDataListFilterFunctions,
} from '../../components';
import { ModalPrompt } from '../../containers';
import { TransactionListItem } from './transaction-list-item';
import { capitalizeFirstLetter, formatPrice, useAxios, useHttpRequestConfig, useFetchData } from '../../utils';
import { useExpandListItem, useSearchHandlers, usePromiseToast } from '../../hooks';
import { TransactionList } from '../../models';
import type {
    ButtonClickFn,
    HTMLElementClickFn,
    LinkClickFn,
    Pagination,
    ReactSelectSingleOption,
    ReactSelectFn,
} from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';
import { debitMemoOptions } from '../../static-data';

export default function Transactions(): JSX.Element {
    document.title = `Transactions :: ${APP_NAME}`;
    const initFilterParams = useMemo<Record<string, any>>(
        () => ({
            search: '',
            page: 1,
            recordsPerPage: 10,
            customerId: '',
            startDate: format(addDays(new Date(), -30), 'yyyy-MM-dd'),
            endDate: format(new Date(), 'yyyy-MM-dd'),
        }),
        [],
    );
    
    const [filterParams, setFilterParams] = useState(initFilterParams);
    
    const fetchConfig = useMemo(() => ({
        url: '/transaction',
        queryKey: ['transactions'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    
    const [loadTransactionList, setLoadTransactionList] = useState(false);
    
    interface TransactionListWithDate extends TransactionList {
        formattedDate: string;
    }
    
    const [transactionList, setTransactionList] = useState<TransactionList[]>([]);
    const [transactionMap, setTransactionMap] = useState<Record<string, TransactionListWithDate[]>>({});
    const [pagination, setPagination] = useState<Pagination | null>(null);
    const { data, isLoading, isFetching, refetch } = useFetchData(fetchConfig);
    
    const updateData = useCallback((data?: any) => {
        if (data) {
            const updatedTransactionList = (data?.records || []) as TransactionList[];
            const updatedPagination = data?.pagination as Pagination;
            
            const updatedTransactionsWithDate = updatedTransactionList.map((transaction) => ({
                ...transaction,
                formattedDate: format(new Date(transaction.transactionDate), 'yyyy/MM/dd'),
            }));
            const transactionDates = (
                updatedTransactionsWithDate.map((transaction) => transaction.formattedDate)
                    .filter((value, index, self) => {
                        return self.indexOf(value) === index;
                    })
            );
            const updatedTransactionMap: Record<string, TransactionListWithDate[]> = {};
            transactionDates.forEach((date) => {
                updatedTransactionMap[date] = updatedTransactionsWithDate.filter((transaction) => transaction.formattedDate === date);
            });
            setTransactionMap(updatedTransactionMap);
            setTransactionList(updatedTransactionList);
            setPagination(updatedPagination);
            setLoadTransactionList(false);
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
        setLoadTransactionList(true);
        refetch()
            .then(({ data: updatedData }) => {
                updateData(updatedData);
            })
            .finally(() => {
                setLoadTransactionList(false);
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
        pagingLinkClickHandler(event, setFilterParams, setLoadTransactionList);
    };
    
    const handleUpdateRecordsPerPage: HTMLElementClickFn<void> = (event) => {
        recordsPerPageSelectionHandler(event, setTransactionList, setFilterParams, setLoadTransactionList);
    };
    
    const handleRefreshClientList: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        updateRecords();
    };
    
    const axios = useAxios();
    const getHttpRequestConfig = useHttpRequestConfig();
    const toast = usePromiseToast();
    
    const [previewDocument, setPreviewDocument] = useState(false);
    const initialDocumentPreviewParams: Record<string, string> = {
        documentTitle: '',
        documentUrl: '',
    };
    const [documentPreviewParams, setDocumentPreviewParams] = useState(initialDocumentPreviewParams);
    const closeDocumentPreview = () => {
        setDocumentPreviewParams(initialDocumentPreviewParams);
        setPreviewDocument(false);
    };
    
    const handleDownloadOrPreview = (action: string, response: Record<string, any>) => {
        if (action === 'preview') {
            setDocumentPreviewParams({
                documentTitle: response.filename,
                documentUrl: response.file,
            });
            setPreviewDocument(true);
        } else if (action === 'download') {
            window.open(response.file, '_blank');
        }
    };
    
    const handleInvoice: HTMLElementClickFn<void> = async (event) => {
        event.preventDefault();
        const { transaction, action, url } = event.currentTarget.dataset;
        const button = event.currentTarget.tagName === 'BUTTON' ? event.target : undefined;
        const selectedTransaction = transactionList.find((item) => item.transactionId === transaction);
        if (selectedTransaction && action && url) {
            try {
                (button as HTMLButtonElement)?.classList.add('loading');
                const { saleTaskId, saleId } = selectedTransaction;
                const processing = action === 'preview'
                    ? 'Loading preview...' :
                    `${capitalizeFirstLetter(action)}ing invoice...`;
                const requestMethod = action === 'send' ? 'POST' : 'GET';
                const httpRequestConfig = {
                    ...await getHttpRequestConfig(requestMethod),
                    url: `${url}`,
                    params: { saleTaskId, saleId },
                    data: {},
                };
                if (requestMethod === 'POST') {
                    httpRequestConfig.data = { saleTaskId, saleId };
                }
                const response = await toast(axios(httpRequestConfig), processing);
                handleDownloadOrPreview(action, response);
                (button as HTMLButtonElement)?.classList.remove('loading');
            } catch (error: any) {
                (button as HTMLButtonElement)?.classList.remove('loading');
                console.log(error);
            }
        }
    };
    
    const handleTransaction: HTMLElementClickFn<void> = async (event) => {
        event.preventDefault();
        const { transaction, action, url } = event.currentTarget.dataset;
        const button = event.currentTarget.tagName === 'BUTTON' ? event.target : undefined;
        if (transaction && action && url) {
            try {
                (button as HTMLButtonElement)?.classList.add('loading');
                const processing = action === 'preview'
                    ? 'Loading preview...' :
                    `${capitalizeFirstLetter(action)}ing receipt...`;
                const requestMethod = action === 'send' ? 'POST' : 'GET';
                const httpRequestConfig = {
                    ...await getHttpRequestConfig(requestMethod),
                    url: `${url}`,
                    params: { transactionId: transaction },
                    data: {},
                };
                if (requestMethod === 'POST') {
                    httpRequestConfig.data = { transactionId: transaction };
                }
                
                const response = await toast(axios(httpRequestConfig), processing);
                handleDownloadOrPreview(action, response);
                (button as HTMLButtonElement)?.classList.remove('loading');
            } catch (error: any) {
                (button as HTMLButtonElement)?.classList.remove('loading');
                console.log(error);
            }
        }
    };
    
    const handleDownloadStatement: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const button = event.currentTarget;
        try {
            button?.classList.add('loading');
            const { customerId, search, startDate, endDate } = filterParams;
            const httpRequestConfig = {
                ...await getHttpRequestConfig('GET'),
                url: '/transaction/statement',
                params: { customerId, search, startDate, endDate },
            };
            const response = await toast(axios(httpRequestConfig), 'Preparing statement...');
            handleDownloadOrPreview('download', response);
            button?.classList.remove('loading');
        } catch (error: any) {
            button?.classList.remove('loading');
            console.log(error);
        }
    };
    
    const [promptUser, setPromptUser] = useState(false);
    const [cancelTransactionData, setCancelTransactionData] = useState({
        transactionId: '',
        transactionDesc: debitMemoOptions[0].value,
    });
    
    const handleDismissPromptModal = useCallback(() => {
        setCancelTransactionData({
            transactionId: '',
            transactionDesc: debitMemoOptions[0].value,
        });
        setPromptUser(false);
    }, []);
    
    const onSelectDebitReason: ReactSelectFn<void> = (name, option) => {
        setCancelTransactionData((prevState) => ({
            ...prevState, [name]: (option as ReactSelectSingleOption)?.value || '',
        }));
    };
    
    const handleCancelTransaction: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const button = event.currentTarget;
        const { transaction } = button.dataset;
        if (transaction) {
            setCancelTransactionData((prevState) => ({
                ...prevState, transactionId: transaction,
            }));
            setPromptUser(true);
        }
    };
    const handleProceedTransactionCancellation: ButtonClickFn<void> = useCallback(async (event) => {
        const button = event.currentTarget;
        try {
            button.classList.add('loading');
            const httpRequestConfig = {
                ...await getHttpRequestConfig('DELETE'),
                url: '/transaction',
                data: { ...cancelTransactionData },
            };
            await toast(axios(httpRequestConfig), 'Cancelling...');
            updateRecords();
            button.classList.remove('loading');
            handleDismissPromptModal();
        } catch (error: any) {
            button.classList.remove('loading');
        }
    }, [axios, cancelTransactionData, getHttpRequestConfig, handleDismissPromptModal, toast, updateRecords]);
    
    const confirmationButton = (
        <Button
            className='btn-danger btn-block'
            disabled={cancelTransactionData.transactionDesc === ''}
            onClick={handleProceedTransactionCancellation}
        >
            <i className='custom-icon icon left-icon return' />
            Cancel Transaction
        </Button>
    );
    
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
    
    const handleNewPayment: HTMLElementClickFn<void> = async (event: any) => {
        const { transaction } = event.currentTarget.dataset;
        const selectedTransaction = transactionList.find((item) => item.transactionId === transaction);
        if (selectedTransaction) {
            const { invoiceNo, saleTaskId, invoiceBalance } = selectedTransaction;
            if (invoiceBalance > 0.01) {
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
                        value: invoiceBalance,
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
    const { expandListItem, listItemViewState } = useExpandListItem();
    
    const RenderTransactions = () => {
        if (isLoading && !data) {
            return <ContainerSpinner />;
        }
        
        if (transactionList.length > 0) {
            return (
                <div className='d-block mt-3 mb-5'>
                    {Object.entries(transactionMap).map(([date, transactions]) => (
                        <div className='d-flex flex-column' key={date}>
                            <SectionTitleContainer>
                                <SectionTitleColumn>
                                    <i className='custom-icon icon right-icon date-time mr-3' />
                                    <SectionTitleLabel>{date}</SectionTitleLabel>
                                </SectionTitleColumn>
                                <SectionTitleColumn>
                                    <i className='custom-icon icon right-icon money-1 mr-3' />
                                    <SectionTitleLabel>
                                        {formatPrice(transactions.reduce((total, transaction) => total + transaction.transactionAmount, 0), '')}
                                    </SectionTitleLabel>
                                    <span className='v-divider' />
                                    <SectionTitleLabel>
                                        {`${transactions.length} Transaction(s)`}
                                    </SectionTitleLabel>
                                </SectionTitleColumn>
                            </SectionTitleContainer>
                            {transactions.map((transaction) => (
                                <TransactionListItem
                                    key={transaction.transactionId}
                                    transaction={transaction}
                                    viewTransactionHandler={expandListItem}
                                    viewTransactionDetail={listItemViewState[transaction.transactionId] || false}
                                    payInvoice={handleNewPayment}
                                    cancelTransactionHandler={handleCancelTransaction}
                                    invoiceActionHandler={handleInvoice}
                                    transactionActionHandler={handleTransaction}
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
                        <i className='custom-icon icon money-1' style={{ width: 48, height: 48 }} />
                        <p className='hint-text text-center mt-2'>
                            {listFiltered ? (
                                <>No transactions matching your criteria available</>
                            ) : (
                                <>
                                    No transactions currently available.
                                    <br /> Once available, all your transactions will be displayed here.
                                </>
                            )}
                        </p>
                    </EmptyListContainer>
                </div>
            </div>
        );
    };
    
    const selectedReason = (
        debitMemoOptions.find((item) => item.value === cancelTransactionData.transactionDesc)
    );
    return (
        <>
            <DataListFilterModal
                filterParams={filterParams}
                filterInputChangeFn={handlerFilterInputChange}
                filterParamSelectFn={handleSelectFilterOption}
                handleClearListFilter={handleClearFilterParams}
                handleToggleModal={toggleFilterModal}
                modalOpened={filterModalOpen}
            />
            <ModalPrompt
                openModalPrompt={promptUser}
                dismissModalPrompt={handleDismissPromptModal}
                promptConfirmationButton={confirmationButton}
            >
                <h5>Are your sure you want to delete the selected transaction?</h5>
                <p className='small'>The effects of this action cannot be reversed.</p>
                <SelectInput
                    label='Reason for cancellation'
                    defaultValue={selectedReason}
                    onChange={(option) => {
                        onSelectDebitReason('transactionDesc', option as ReactSelectSingleOption);
                    }}
                    options={debitMemoOptions}
                    required
                />
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
            />
            <div className='d-flex flex-fill flex-column'>
                <SearchAndFilter
                    ref={searchInputRef}
                    disabled={isLoading || isFetching}
                    loading={loadTransactionList || isFetching}
                    filterParams={filterParams}
                    pagination={pagination}
                    clearSearchHandler={handleClearSearch}
                    downloadHandler={handleDownloadStatement}
                    refreshDataHandler={handleRefreshClientList}
                    searchValueChangeHandler={handleSearchValueChange}
                    recordsPerPageOptions={recordsPerPageOptions}
                    resetFilterParamsHandler={handleResetFilterParams}
                    paginationLinkHandler={handlePaginationLinkClick}
                    toggleFilterOptionsHandler={handleToggleFilterModal}
                    filterOptionsVisible={filterModalOpen}
                    updateRecordsPerPageHandler={handleUpdateRecordsPerPage}
                />
                <RenderTransactions />
            </div>
        </>
    );
};
