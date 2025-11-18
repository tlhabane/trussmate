import React, { JSX, useCallback, useEffect, useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';
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
    useAddressForm,
    useContactPersonForm,
    useCustomerForm,
    addressFormConfig,
    contactFormConfig,
    customerFormConfig,
} from '../../components';
import { AddressModalForm, CustomerListItem, ContactModalForm, CustomerModalForm } from './components';
import { ModalPrompt, useLayoutContext } from '../../containers';
import { getHttpRequestConfig, useFetchData, useAxios } from '../../utils';
import {
    useExpandListItem,
    useScreenWidth,
    useSearchHandlers,
    usePromiseToast,
    usePreviousLocation,
} from '../../hooks';
import { CustomerList } from '../../models';
import type {
    ButtonClickFn,
    HTMLElementClickFn,
    LinkClickFn,
    Pagination,
    ReactSelectSingleOption,
} from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';

export default function Customer(): JSX.Element {
    document.title = `Customers :: ${APP_NAME}`;
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
        url: '/customer',
        queryKey: ['customers'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    
    const [loadCustomerList, setLoadCustomerList] = useState(false);
    const [customerList, setCustomerList] = useState<CustomerList[]>([]);
    const [pagination, setPagination] = useState<Pagination | null>(null);
    const { data, isLoading, isFetching, refetch } = useFetchData(fetchConfig);
    
    const updateData = useCallback((data?: any) => {
        if (data) {
            const updatedCustomerList = (data?.records || []) as CustomerList[];
            const updatedPagination = data?.pagination as Pagination;
            setCustomerList(updatedCustomerList);
            setPagination(updatedPagination);
            setLoadCustomerList(false);
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
        setLoadCustomerList(true);
        refetch()
            .then(({ data: updatedData }) => {
                updateData(updatedData);
            })
            .finally(() => {
                setLoadCustomerList(false);
            });
    }, [refetch, updateData]);
    
    /*const { setRefreshDataHandler } = useLayoutContext();
    useEffect(() => {
        setRefreshDataHandler(() => updateRecords);
        return () => {
            setRefreshDataHandler(null);
        }
    }, [setRefreshDataHandler, updateRecords])*/
    
    const resetFilterParams = () => {
        setFilterParams(initFilterParams);
        updateRecords();
    };
    const handleResetFilterParams: LinkClickFn<void> = (event) => {
        event.preventDefault();
        resetFilterParams();
    };
    
    const handlePaginationLinkClick: LinkClickFn<void> = (event) => {
        pagingLinkClickHandler(event, setFilterParams, setLoadCustomerList);
    };
    
    const handleUpdateRecordsPerPage: HTMLElementClickFn<void> = (event) => {
        recordsPerPageSelectionHandler(event, setCustomerList, setFilterParams, setLoadCustomerList);
    };
    
    const handleRefreshClientList: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        updateRecords();
    };
    /*
    const handleClearFilterParams: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        resetFilterParams();
    };
    */
    const navigate = useNavigate();
    const location = usePreviousLocation();
    
    const [promptUser, setPromptUser] = useState(false);
    const [confirmationButton, setConfirmationButton] = useState(<Button />);
    const [deleteMessage, setDeleteMessage] = useState('');
    
    const handleDismissPromptModal = () => {
        setConfirmationButton(<Button />);
        setDeleteMessage('');
        setPromptUser(false);
    };
    
    const axios = useAxios();
    const toast = usePromiseToast();
    const handleProceedDeletingCustomer: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const { customer } = event.currentTarget.dataset;
        const httpRequestConfig = {
            ...getHttpRequestConfig('DELETE'),
            url: '/customer',
            data: { customerId: customer || '' },
        };
        
        await toast(axios(httpRequestConfig, undefined, event.currentTarget));
        handleDismissPromptModal();
        updateRecords();
    };
    
    const handleDeleteCustomer: HTMLElementClickFn<void> = (event) => {
        event.preventDefault();
        const { customer } = event.currentTarget.dataset;
        if (customer) {
            setConfirmationButton(
                <Button
                    className='btn-danger btn-block'
                    data-customer={customer}
                    onClick={handleProceedDeletingCustomer}
                >
                    <i className='custom-icon icon left-icon trash' />
                    Delete
                </Button>,
            );
            setDeleteMessage('Are your sure you want to delete the selected customer?');
            setPromptUser(true);
        }
    };
    
    const { setAddNewHandler } = useLayoutContext();
    const addNewCustomer: ButtonClickFn<void> = useCallback((event) => {
        event?.preventDefault();
        navigate('/customer/management', { state: { from: location?.pathname } });
    }, [location, navigate]);
    
    useEffect(() => {
        setAddNewHandler(() => addNewCustomer);
        return () => {
            setAddNewHandler(null);
        };
    }, [addNewCustomer, setAddNewHandler]);
    
    const {
        handleClearSearch,
        handleSearchValueChange,
        searchInputRef,
    } = useSearchHandlers(setFilterParams, updateRecords);
    
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
    } = useCustomerForm();
    
    const [openCustomerModalForm, setOpenCustomerModalForm] = useState(false);
    const toggleCustomerModalForm = () => {
        resetForm();
        setOpenCustomerModalForm((modalOpen) => !modalOpen);
    };
    
    const handleUpdateCustomer: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { customer } = event.currentTarget.dataset;
        const selectedCustomer = customerList.find((item) => item.customerId === customer);
        if (selectedCustomer) {
            const updatedCustomerFormData = Object.entries(customerFormConfig).reduce((acc: any, [key, props]) => {
                const value = (selectedCustomer as any)[key];
                acc[key] = { ...props, error: '', value };
                return acc;
            }, {});
            setFormData(updatedCustomerFormData);
            setOpenCustomerModalForm(true);
        }
    };
    
    const onCustomerFormSubmit = handleSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            const httpRequestConfig = {
                ...await getHttpRequestConfig('PATCH'),
                url: '/customer',
                data: { ...validated },
            };
            await toast(axios(httpRequestConfig), 'Updating customer info...');
            button?.classList.remove('loading');
            toggleCustomerModalForm();
            updateRecords();
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    const {
        formData: contactPersonData,
        formInvalid: contactPersonFormInvalid,
        getElement: getContactPersonElement,
        handleSubmit: handleContactPersonFormSubmit,
        onBlur: onContactPersonInputBlur,
        onChange: onContactPersonInputChange,
        resetForm: resetContactPersonForm,
        setFormData: setContactPersonFormData,
    } = useContactPersonForm();
    
    const [openContactModalForm, setOpenContactModalForm] = useState(false);
    const toggleContactModalForm = () => {
        resetContactPersonForm();
        setOpenContactModalForm((modalOpen) => !modalOpen);
    };
    
    const handleAddContactPerson: HTMLElementClickFn<void> = (event) => {
        event.preventDefault();
        const { customer } = event.currentTarget.dataset;
        if (customer) {
            setContactPersonFormData((prevState) => ({
                ...prevState,
                customerId: {
                    ...prevState.customerId,
                    value: customer,
                },
            }));
            setOpenContactModalForm(true);
        }
    };
    
    const handleUpdateContactPerson: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { customer, contact } = event.currentTarget.dataset;
        const selectedCustomer = customerList.find((item) => item.customerId === customer);
        if (selectedCustomer) {
            const { contacts } = selectedCustomer;
            const selectedContact = contacts.find((item) => item.contactId === contact);
            if (selectedContact) {
                const updatedContactFormData = Object.entries(contactFormConfig).reduce((acc: any, [key, props]) => {
                    const value = (selectedContact as any)[key];
                    acc[key] = { ...props, error: '', value };
                    return acc;
                }, {});
                setContactPersonFormData(updatedContactFormData);
                setOpenContactModalForm(true);
            }
        }
    };
    
    const handleProceedDeletingContact: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const { contact } = event.currentTarget.dataset;
        const httpRequestConfig = {
            ...getHttpRequestConfig('DELETE'),
            url: '/customer/contact',
            data: { contactId: contact || '' },
        };
        
        await toast(axios(httpRequestConfig, undefined, event.currentTarget));
        handleDismissPromptModal();
        updateRecords();
    };
    
    const handleDeleteContact: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { contact } = event.currentTarget.dataset;
        if (contact) {
            setConfirmationButton(
                <Button
                    className='btn-danger btn-block'
                    data-contact={contact}
                    onClick={handleProceedDeletingContact}
                >
                    <i className='custom-icon icon left-icon trash' />
                    Delete
                </Button>,
            );
            setDeleteMessage('Are your sure you want to delete the selected contact person?');
            setPromptUser(true);
        }
    };
    
    const onContactPersonFormSubmit = handleContactPersonFormSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            const contactId = (validated?.contactId?.toString() || '').trim();
            const httpRequestConfig = {
                ...await getHttpRequestConfig(contactId === '' ? 'POST' : 'PATCH'),
                url: '/customer/contact',
                data: { ...validated },
            };
            const process = contactId === '' ? 'Adding new contact' : 'Updating contact info';
            await toast(axios(httpRequestConfig), `${process}...`);
            button?.classList.remove('loading');
            toggleContactModalForm();
            updateRecords();
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    const {
        formData: addressData,
        formInvalid: addressFormInvalid,
        getElement: getAddressElement,
        handleSubmit: handleAddressFormSubmit,
        onBlur: onAddressInputBlur,
        onChange: onAddressInputChange,
        onReactSelectChange: onAddressSelect,
        resetForm: resetAddressForm,
        setFormData: setAddressFormData,
    } = useAddressForm();
    
    const [openAddressModalForm, setOpenAddressModalForm] = useState(false);
    const toggleAddressModalForm = () => {
        resetAddressForm();
        setOpenAddressModalForm((modalOpen) => !modalOpen);
    };
    
    const handleAddAddress: HTMLElementClickFn<void> = (event) => {
        event.preventDefault();
        const { customer } = event.currentTarget.dataset;
        if (customer) {
            setAddressFormData((prevState) => ({
                ...prevState,
                customerId: {
                    ...prevState.customerId,
                    value: customer,
                },
            }));
            setOpenAddressModalForm(true);
        }
    };
    
    const handleUpdateAddress: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { customer, address } = event.currentTarget.dataset;
        const selectedCustomer = customerList.find((item) => item.customerId === customer);
        if (selectedCustomer) {
            const { addresses } = selectedCustomer;
            const selectedAddress = addresses.find((item) => item.addressId === address);
            if (selectedAddress) {
                const updatedAddressFormData = Object.entries(addressFormConfig).reduce((acc: any, [key, props]) => {
                    const value = (selectedAddress as any)[key];
                    acc[key] = { ...props, error: '', value };
                    return acc;
                }, {});
                setAddressFormData(updatedAddressFormData);
                setOpenAddressModalForm(true);
            }
        }
    };
    
    const handleProceedDeletingAddress: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const { address } = event.currentTarget.dataset;
        const httpRequestConfig = {
            ...getHttpRequestConfig('DELETE'),
            url: '/customer/address',
            data: { addressId: address || '' },
        };
        
        await toast(axios(httpRequestConfig, undefined, event.currentTarget));
        handleDismissPromptModal();
        updateRecords();
    };
    
    const handleDeleteAddress: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { address } = event.currentTarget.dataset;
        if (address) {
            setConfirmationButton(
                <Button
                    className='btn-danger btn-block'
                    data-address={address}
                    onClick={handleProceedDeletingAddress}
                >
                    <i className='custom-icon icon left-icon trash' />
                    Delete
                </Button>,
            );
            setDeleteMessage('Are your sure you want to delete the selected address?');
            setPromptUser(true);
        }
    };
    
    const onAddressFormSubmit = handleAddressFormSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            const contactId = (validated?.addressId?.toString() || '').trim();
            const httpRequestConfig = {
                ...await getHttpRequestConfig(contactId === '' ? 'POST' : 'PATCH'),
                url: '/customer/address',
                data: { ...validated },
            };
            const process = contactId === '' ? 'Adding new address' : 'Updating address';
            await toast(axios(httpRequestConfig), `${process}...`);
            button?.classList.remove('loading');
            toggleAddressModalForm();
            updateRecords();
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    const screenWidth = useScreenWidth();
    const mobilePadding = screenWidth <= 768 ? 'pr-sm-0 pl-sm-0' : '';
    const tabletPadding = screenWidth <= 810 ? 'pr-md-0 pl-md-0' : '';
    
    const { expandListItem, listItemViewState } = useExpandListItem();
    
    const RenderCustomers = () => {
        if (isLoading && !data) {
            return <ContainerSpinner />;
        }
        
        if (customerList.length > 0) {
            return (
                <div className='d-block mt-3 mb-5'>
                    {customerList.map((customer) => (
                        <CustomerListItem
                            key={customer.customerId}
                            customer={customer}
                            addAddress={handleAddAddress}
                            deleteAddress={handleDeleteAddress}
                            updateAddress={handleUpdateAddress}
                            addContact={handleAddContactPerson}
                            deleteContact={handleDeleteContact}
                            updateContact={handleUpdateContactPerson}
                            deleteCustomer={handleDeleteCustomer}
                            updateCustomer={handleUpdateCustomer}
                            toggleListItem={expandListItem}
                            viewCustomerDetail={listItemViewState[customer.customerId] || false}
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
                        <i className='custom-icon icon users' style={{ width: 48, height: 48 }} />
                        <p className='hint-text text-center mt-2'>
                            {listFiltered ? (
                                <>No customers matching your criteria available</>
                            ) : (
                                <>
                                    No customers currently available.
                                    <br /> Once available, all your customers will be displayed here.
                                </>
                            )}
                        </p>
                        <EmptyNoticeButton onClick={addNewCustomer}>
                            Add {listFiltered ? 'Missing' : 'New'} Customer
                        </EmptyNoticeButton>
                        <EmptyAdditionalNotice>
                            <Button className='btn-link'>
                                Upload {listFiltered ? 'Missing' : 'New'} Customers
                            </Button>
                        </EmptyAdditionalNotice>
                    </EmptyListContainer>
                </div>
            </div>
        );
    };
    
    return (
        <>
            <AddressModalForm
                getElement={getAddressElement}
                formConfig={addressData}
                formInvalid={addressFormInvalid}
                onBlur={onAddressInputBlur}
                onChange={onAddressInputChange}
                onSelect={onAddressSelect}
                openModal={openAddressModalForm}
                toggleModal={toggleAddressModalForm}
                onSubmit={onAddressFormSubmit}
            />
            <ContactModalForm
                getElement={getContactPersonElement}
                formConfig={contactPersonData}
                formInvalid={contactPersonFormInvalid}
                onBlur={onContactPersonInputBlur}
                onChange={onContactPersonInputChange}
                mobilePadding={mobilePadding}
                tabletPadding={tabletPadding}
                openModal={openContactModalForm}
                toggleModal={toggleContactModalForm}
                onSubmit={onContactPersonFormSubmit}
            />
            <CustomerModalForm
                getElement={getElement}
                formConfig={formData}
                formInvalid={formInvalid}
                onBlur={onBlur}
                onChange={onChange}
                onSelect={onReactSelectChange}
                mobilePadding={mobilePadding}
                tabletPadding={tabletPadding}
                openModal={openCustomerModalForm}
                toggleModal={toggleCustomerModalForm}
                onSubmit={onCustomerFormSubmit}
            />
            <ModalPrompt
                openModalPrompt={promptUser}
                dismissModalPrompt={handleDismissPromptModal}
                promptConfirmationButton={confirmationButton}
            >
                <h5>{deleteMessage}</h5>
                <p className='small'>The effects of this action cannot be reversed.</p>
            </ModalPrompt>
            <div className='d-flex flex-fill flex-column'>
                <SearchAndFilter
                    ref={searchInputRef}
                    disabled={isLoading || isFetching}
                    loading={loadCustomerList || isFetching}
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
                <RenderCustomers />
            </div>
        </>
    );
}
