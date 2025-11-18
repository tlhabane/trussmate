import React, { JSX, useEffect, useMemo, useRef, useState } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { To, useLocation, useNavigate } from 'react-router-dom';
import { SaleFormInputElements } from './sale-form-input-element';
import { useSaleForm } from './useSaleForm';
import { saleFormConfig } from './sale-form-config';
import { capitalizeFirstLetter, useAxios, useFetchData, useHttpRequestConfigWithFiles } from '../../utils';
import { usePromiseToast } from '../../hooks';
import { Button, ContainerSpinner, Form } from '../../components';
import { StickyFooter, useLayoutChildContext } from '../../containers';
import { CustomerAddress, CustomerList, ContactPerson, Sale, WorkflowList } from '../../models';
import { ButtonClickFn, InputChangeFn } from '../../types';
import { APP_NAME } from '../../config';

export default function SaleForm(): JSX.Element {
    document.title = `Manage Sale :: ${APP_NAME}`;
    
    const axios = useAxios();
    const getHttpRequestConfig = useHttpRequestConfigWithFiles<Sale>();
    const navigate = useNavigate();
    const toast = usePromiseToast();
    
    const location = useLocation();
    const [previousPath, setPreviousPath] = useState<To>('');
    const { setPreviousLocation } = useLayoutChildContext();
    useEffect(() => {
        if (location.state?.from) {
            setPreviousLocation(location.state.from);
            setPreviousPath(location.state.from);
        }
    }, [location.state?.from, setPreviousLocation]);
    
    const {
        formData,
        formInvalid,
        getElement,
        handleSubmit,
        onReactSelectChange,
        resetForm,
        setFormData,
    } = useSaleForm(saleFormConfig);
    
    /* Workflow options */
    const workflowFetchConfig = useMemo(() => ({
        url: '/workflow',
        queryKey: ['workflows'],
    }), []);
    const { data: workflowOptionData, isLoading: workflowOptionsLoading } = useFetchData(workflowFetchConfig);
    
    /* Customer options */
    const customerFetchConfig = useMemo(() => ({
        url: '/customer',
        queryKey: ['customers'],
    }), []);
    const { data: customerOptionData, isLoading: customerOptionsLoading } = useFetchData(customerFetchConfig);
    const [customers, setCustomers] = useState<CustomerList[]>([]);
    const [workflows, setWorkflows] = useState<WorkflowList[]>([]);
    
    /* Load and set customer & workflow options */
    useEffect(() => {
        if (customerOptionData && workflowOptionData) {
            const workflowList = (workflowOptionData?.records || []) as WorkflowList[];
            const updatedWorkflowOptions = workflowList.map(({ workflowId, workflowName }) => ({
                label: workflowName,
                value: workflowId,
            }));
            setWorkflows(workflowList);
            
            const customerList = (customerOptionData?.records || []) as CustomerList[];
            const updatedCustomerOptions = customerList.map(({ customerId, customerName }) => ({
                label: customerName,
                value: customerId,
            }));
            
            setCustomers(customerList);
            setFormData((prevState) => ({
                ...prevState,
                workflowId: {
                    ...prevState.workflowId,
                    value: updatedWorkflowOptions[0]?.value || '',
                    options: updatedWorkflowOptions,
                },
                customerId: {
                    ...prevState.customerId,
                    value: updatedCustomerOptions[0]?.value || '',
                    options: updatedCustomerOptions,
                },
                delivery: {
                    ...prevState.delivery,
                    value: workflowList[0]?.delivery || 0,
                },
                labour: {
                    ...prevState.labour,
                    value: workflowList[0]?.labour || 0,
                },
            }));
        }
    }, [customerOptionData, workflowOptionData, setFormData]);
    
    useEffect(() => {
        if (formData.workflowId.value.trim() !== '' && workflows.length > 0) {
            const selectedWorkflow = workflows.find((workflow) => workflow.workflowId === formData.workflowId.value);
            if (selectedWorkflow) {
                const { workflowId, delivery, labour } = selectedWorkflow;
                setFormData((prevState) => ({
                    ...prevState,
                    workflowId: {
                        ...prevState.workflowId,
                        value: workflowId,
                    },
                    delivery: {
                        ...prevState.delivery,
                        value: delivery || 0,
                    },
                    labour: {
                        ...prevState.labour,
                        value: labour || 0,
                    },
                }));
            }
        }
    }, [formData.workflowId.value, workflows]);
    
    const [showContactPersonSelector, setShowContactPersonSelector] = useState(false);
    useEffect(() => {
        if (formData.customerId.value.trim() !== '' && customers.length > 0) {
            const selectedCustomer = customers.find((customer) => customer.customerId === formData.customerId.value);
            if (selectedCustomer) {
                setShowContactPersonSelector(selectedCustomer.customerType === 'business');
            }
        }
    }, [formData.customerId.value, customers]);
    
    /* Customer address options */
    const addressFetchConfig = useMemo(() => ({
        url: '/customer/address',
        params: { customerId: formData.customerId.value },
        queryKey: ['addresses'],
    }), [formData.customerId.value]);
    
    const { data: addressOptionData, isLoading: addressOptionsLoading } = useFetchData(addressFetchConfig);
    useEffect(() => {
        if (addressOptionData) {
            const addressList = (addressOptionData?.records || []) as CustomerAddress[];
            const updatedAddressOptions = addressList.map(({ addressId, fullAddress }) => ({
                label: fullAddress,
                value: addressId,
            }));
            setFormData((prevState) => ({
                ...prevState,
                billingAddressId: {
                    ...prevState.billingAddressId,
                    value: updatedAddressOptions[0]?.value || '',
                    options: updatedAddressOptions,
                },
                deliveryAddressId: {
                    ...prevState.deliveryAddressId,
                    options: updatedAddressOptions,
                    value: '',
                },
            }));
        }
    }, [addressOptionData, setFormData]);
    
    /* Customer contact options */
    const contactFetchConfig = useMemo(() => ({
        url: '/customer/contact',
        params: { customerId: formData.customerId.value },
        queryKey: ['contacts'],
    }), [formData.customerId.value]);
    
    const { data: contactOptionData, isLoading: contactOptionsLoading } = useFetchData(contactFetchConfig);
    useEffect(() => {
        if (contactOptionData) {
            const contactList = (contactOptionData?.records || []) as ContactPerson[];
            const updatedContactOptions = contactList.map(({ contactId, firstName, lastName }) => ({
                label: capitalizeFirstLetter(`${firstName} ${lastName}`),
                value: contactId,
            }));
            setFormData((prevState) => ({
                ...prevState,
                contactId: {
                    ...prevState.contactId,
                    value: updatedContactOptions[0]?.value || '',
                    options: updatedContactOptions,
                },
            }));
        }
    }, [contactOptionData, setFormData]);
    
    /* Installation or delivery: Set delivery address as required */
    useEffect(() => {
        setFormData((prevState) => {
            let label = 'Delivery or installation address';
            if (formData.labour.value === 1 || formData.delivery.value === 1) {
                if (formData.labour.value === 1 && formData.delivery.value === 1) {
                    label = 'Installation address';
                } else if (formData.labour.value === 0 && formData.delivery.value === 1) {
                    label = 'Delivery address';
                } else {
                    label = 'Delivery and installation address';
                }
            }
            return {
                ...prevState,
                deliveryAddressId: {
                    ...prevState.deliveryAddressId,
                    required: formData.labour.value === 1 || formData.delivery.value === 1,
                    label,
                },
            };
        });
    }, [formData.labour.value, formData.delivery.value, setFormData]);
    
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
    
    const onSubmit = handleSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            const saleId = (validated?.saleId?.toString() || '').trim();
            const httpRequestConfig = {
                ...await getHttpRequestConfig(validated, saleId === '' ? 'POST' : 'PATCH', uploadedFiles),
                url: '/sale',
            };
            const process = saleId === '' ? 'Adding new sale' : 'Updating sale';
            const response = await toast(axios(httpRequestConfig), `${process}...`);
            button?.classList.remove('loading');
            
            if (response?.success) {
                resetForm();
                navigate((previousPath || -1) as To, { replace: true });
            }
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    const handleCancel: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        navigate((previousPath || -1) as To, { replace: true });
    };
    
    const queryClient = useQueryClient();
    const [clearingCache, setClearingCache] = useState(false);
    const handleAddCustomer: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        setClearingCache(true);
        queryClient.invalidateQueries({ queryKey: ['addresses', 'contacts', 'customers'] })
            .then(() => {
                setClearingCache(false);
            })
            .finally(() => {
                navigate('/customer/management');
            });
    };
    
    const saveDisabled = formInvalid || addressOptionsLoading || contactOptionsLoading || uploadedFiles.length === 0;
    if (clearingCache || (customerOptionsLoading && !customerOptionData) || (workflowOptionsLoading && !workflowOptionData)) {
        return <ContainerSpinner />;
    }
    
    const { value: workflowId } = formData.workflowId;
    const selectedWorkflow = workflows.find((workflow) => workflow.workflowId === workflowId);
    const deliveryOptionAvailable = selectedWorkflow?.delivery || 0;
    return (
        <div className='tab-content flex-fill'>
            <Form onSubmit={onSubmit}>
                <SaleFormInputElements
                    addCustomer={handleAddCustomer}
                    deliveryAvailable={deliveryOptionAvailable}
                    getElement={getElement}
                    formConfig={formData}
                    onSelect={onReactSelectChange}
                    ref={uploadInput}
                    onUpload={onUpload}
                    uploadedFileCount={uploadedFiles.length}
                    uploadFloorPlans={handleUpload}
                    showContactPerson={showContactPersonSelector}
                />
                <StickyFooter>
                    <div className='row'>
                        <div className='col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1'>
                            <div className='form-group mt-3 mb-3'>
                                <div className='row'>
                                    <div className='col-3 pr-sm-0 pl-sm-0'>
                                        <Button type='button' className='btn-default btn-block' onClick={handleCancel}>
                                            Cancel
                                        </Button>
                                    </div>
                                    <div className='col-8 offset-1 pr-sm-0 pl-sm-0'>
                                        <Button type='submit' className='btn-primary btn-block' disabled={saveDisabled}>
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
    );
}
