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
import { BankAccountListItem, BankAccountModalForm } from './components';
import { ModalPrompt } from '../../containers';
import { getDefaultError, useAxios, useFetchData, useHttpRequestConfig } from '../../utils';
import { useBasicNotification, usePromiseToast, useSearchHandlers } from '../../hooks';
import { useBankAccountForm } from './useBankAccountForm';
import { bankAccountFormConfig } from './bank-account-form-config';
import { BankAccount } from '../../models';
import type {
    ButtonClickFn,
    HTMLElementClickFn,
    LinkClickFn,
    Pagination,
    ReactSelectSingleOption,
} from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';
import { bankNames } from '../../static-data';

export default function BankAccountSettings(): JSX.Element {
    document.title = `Bank Accounts :: ${APP_NAME}`;
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
        url: '/settings/bank',
        queryKey: ['bankAccounts'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    
    const [loadBankAccountList, setLoadBankAccountList] = useState(false);
    const [bankAccountList, setBankAccountList] = useState<BankAccount[]>([]);
    const [pagination, setPagination] = useState<Pagination | null>(null);
    const { data, isLoading, isFetching, refetch } = useFetchData(fetchConfig);
    
    const updateData = useCallback((data?: any) => {
        if (data) {
            const updatedBankAccountList = (data?.records || []) as BankAccount[];
            const updatedPagination = data?.pagination as Pagination;
            setBankAccountList(updatedBankAccountList);
            setPagination(updatedPagination);
            setLoadBankAccountList(false);
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
        setLoadBankAccountList(true);
        refetch()
            .then(({ data: updatedData }) => {
                updateData(updatedData);
            })
            .finally(() => {
                setLoadBankAccountList(false);
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
        pagingLinkClickHandler(event, setFilterParams, setLoadBankAccountList);
    };
    
    const handleUpdateRecordsPerPage: HTMLElementClickFn<void> = (event) => {
        recordsPerPageSelectionHandler(event, setBankAccountList, setFilterParams, setLoadBankAccountList);
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
    } = useBankAccountForm();
    
    /* Bank name update = update branch code */
    useEffect(() => {
        const selectedBank = bankNames.find((bank) => bank.bankName === formData.bankName.value);
        if (selectedBank) {
            setFormData((prevState) => ({
                ...prevState,
                branchCode: {
                    ...prevState.branchCode,
                    value: selectedBank.branchCode,
                    error: '',
                },
            }));
        }
    }, [formData.bankName.value, setFormData]);
    
    const [showBankAccountForm, setShowBankAccountForm] = useState(false);
    const dismissBankAccountForm = () => {
        resetForm();
        setShowBankAccountForm(false);
    };
    
    const toggleShowBankAccountForm = () => {
        setShowBankAccountForm((bankAccountFormShown) => {
            if (bankAccountFormShown) {
                dismissBankAccountForm();
            }
            return !bankAccountFormShown;
        });
    };
    
    const axios = useAxios();
    const getHttpRequestConfig = useHttpRequestConfig();
    const toast = usePromiseToast();
    const basicToast = useBasicNotification();
    
    const onSubmit = handleSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            const bankAccountId = (validated?.bankId?.toString() || '').trim();
            const httpRequestConfig = {
                ...await getHttpRequestConfig(bankAccountId === '' ? 'POST' : 'PATCH'),
                url: '/settings/bank',
                data: { ...validated },
            };
            const process = bankAccountId === '' ? 'Adding new bankAccount' : 'Updating bankAccount info';
            const response = await toast(axios(httpRequestConfig), `${process}...`);
            if (response?.message) {
                basicToast(getDefaultError(response?.message), 'error');
            }
            if (response?.success) {
                updateRecords();
                dismissBankAccountForm();
            }
            
            button?.classList.remove('loading');
        } catch (error: any) {
            basicToast(getDefaultError(error?.message), 'error');
            button?.classList.remove('loading');
        }
    });
    
    const handleAddBankAccount: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        toggleShowBankAccountForm();
    };
    
    const handleUpdateBankAccount: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { account } = event.currentTarget.dataset;
        const selectedBankAccount = bankAccountList.find((a) => a.bankId === account);
        if (selectedBankAccount) {
            const updatedFormData = Object.entries(bankAccountFormConfig).reduce((acc: any, [key, props]) => {
                const value = (selectedBankAccount as any)[key];
                acc[key] = { ...props, error: '', value };
                return acc;
            }, {});
            setFormData(updatedFormData);
            toggleShowBankAccountForm();
        }
    };
    
    const [promptUser, setPromptUser] = useState(false);
    const [confirmationButton, setConfirmationButton] = useState(<Button />);
    const bankAccountDeleteMessage = (
        <>
            <h5>Are your sure you want to delete the selected bank account?</h5>
            <p className='small'>The effects of this action cannot be reversed.</p>
        </>
    );
    
    const handleDismissPromptModal = () => {
        setConfirmationButton(<Button />);
        setPromptUser(false);
    };
    
    const handleProceedDeletingBankAccount: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const { account } = event.currentTarget.dataset;
        const httpRequestConfig = {
            ...await getHttpRequestConfig('DELETE'),
            url: '/settings/bank',
            data: { bankId: account || '' },
        };
        
        await toast(axios(httpRequestConfig, undefined, event.currentTarget));
        updateRecords();
    };
    
    const handleDeleteBankAccount: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { bankAccount } = event.currentTarget.dataset;
        if (bankAccount) {
            setConfirmationButton(
                <Button
                    className='btn-danger btn-block'
                    data-bankAccount={bankAccount}
                    onClick={handleProceedDeletingBankAccount}
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
    
    const RenderBankAccountList = () => {
        if (isLoading && !data) {
            return <ContainerSpinner />;
        }
        
        if (bankAccountList.length > 0) {
            return (
                <div className='d-block mt-3 mb-5'>
                    {bankAccountList.map((bankAccount) => (
                        <BankAccountListItem
                            key={bankAccount.bankId}
                            account={bankAccount}
                            deleteBankAccount={handleDeleteBankAccount}
                            updateBankAccount={handleUpdateBankAccount}
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
                        <i className='custom-icon icon money-1' style={{ width: 48, height: 48 }} />
                        <p className='hint-text text-center mt-2'>
                            {listFiltered ? (
                                <>No bank accounts matching your criteria available</>
                            ) : (
                                <>
                                    No bank accounts currently available.
                                    <br /> Once available, all your bank accounts will be displayed here.
                                </>
                            )}
                        </p>
                        <EmptyNoticeButton onClick={handleAddBankAccount}>
                            Add {listFiltered ? 'Missing' : 'New'} Account
                        </EmptyNoticeButton>
                        <EmptyAdditionalNotice>
                            <Button className='btn-link'>
                                Upload {listFiltered ? 'Missing' : 'New'} Accounts
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
                {bankAccountDeleteMessage}
            </ModalPrompt>
            <BankAccountModalForm
                openModal={showBankAccountForm}
                toggleModal={dismissBankAccountForm}
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
                        loading={isFetching || loadBankAccountList}
                        filterParams={filterParams}
                        pagination={pagination}
                        addNewHandler={handleAddBankAccount}
                        clearSearchHandler={handleClearSearch}
                        refreshDataHandler={handleRefreshDataList}
                        searchValueChangeHandler={handleSearchValueChange}
                        recordsPerPageOptions={recordsPerPageOptions}
                        resetFilterParamsHandler={handleResetFilterParams}
                        paginationLinkHandler={handlePaginationLinkClick}
                        updateRecordsPerPageHandler={handleUpdateRecordsPerPage}
                    />
                    <RenderBankAccountList />
                </div>
            </div>
        </>
    );
}
