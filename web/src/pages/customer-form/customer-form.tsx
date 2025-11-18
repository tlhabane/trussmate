import React, { JSX, useEffect, useState } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { To, useLocation, useNavigate } from 'react-router-dom';
import { ContactInfoForm, CustomerInfoForm } from './components';
import { scrollToElement, useAxios, useHttpRequestConfig } from '../../utils';
import { usePromiseToast, useScreenWidth } from '../../hooks';
import { Form, useContactPersonForm, useCustomerForm } from '../../components';
import { useLayoutChildContext } from '../../containers';
import { ButtonClickFn } from '../../types';
import { APP_NAME } from '../../config';

export default function CustomerForm(): JSX.Element {
    document.title = `Manage Customer :: ${APP_NAME}`;
    
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
    
    useEffect(() => {
        setFormData((prevState) => {
            const { customerName, customerType } = prevState;
            if (customerType.value === 'business') {
                return {
                    ...prevState,
                    customerName: { ...customerName, label: 'Business Name', placeholder: 'Building Contractor' },
                };
            }
            if (customerType.value === 'individual') {
                return {
                    ...prevState,
                    customerName: { ...customerName, label: 'Customer Name', placeholder: 'Sibongile Mazibuko' },
                };
            }
            return prevState;
        });
    }, [formData.customerType.value, setFormData]);
    
    const [activeTab, setActiveTab] = useState('customer-info');
    const onSwitchTab = (selectedTab: string) => {
        setActiveTab(selectedTab);
        scrollToElement();
    };
    
    const axios = useAxios();
    const getHttpRequestConfig = useHttpRequestConfig();
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
        formData: contactPersonData,
        formInvalid: contactPersonFormInvalid,
        getElement: getContactPersonElement,
        handleSubmit: handleContactPersonFormSubmit,
        onBlur: onContactPersonInputBlur,
        onChange: onContactPersonInputChange,
        resetForm: resetContactPersonForm,
        setFormData: setContactPersonFormData,
    } = useContactPersonForm();
    const queryClient = useQueryClient();
    
    const onSubmit = handleSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            // Simulate API call
            // await new Promise((resolve) => setTimeout(resolve, 5000));
            const customerId = (validated?.customerId?.toString() || '').trim();
            const httpRequestConfig = {
                ...await getHttpRequestConfig(customerId === '' ? 'POST' : 'PATCH'),
                url: '/customer',
                data: { ...validated },
            };
            const process = customerId === '' ? 'Adding new customer' : 'Updating customer info';
            const response = await toast(axios(httpRequestConfig), `${process}...`);
            button?.classList.remove('loading');
            
            if (response?.success) {
                setFormData((prevState) => {
                    const updatedCustomerData = { ...prevState };
                    updatedCustomerData['customerId'] = {
                        ...updatedCustomerData['customerId'],
                        value: response?.id || '',
                    };
                    return updatedCustomerData;
                });
                
                setContactPersonFormData((prevState) => {
                    const updatedContactPersonData = { ...prevState };
                    updatedContactPersonData['customerId'] = {
                        ...updatedContactPersonData['customerId'],
                        value: response?.id || '',
                    };
                    return updatedContactPersonData;
                });
                
                const { customerType } = validated;
                if (customerType === 'business') {
                    onSwitchTab('customer-contact-person');
                    return;
                }
                
                // Reset formConfig or navigate as needed
                await queryClient.invalidateQueries({ queryKey: ['addresses', 'contacts', 'customers'] });
                resetForm();
                navigate((previousPath || -1) as To, { replace: true });
                return;
            }
            
            // basicToast(getDefaultError(response?.message), 'error');
        } catch (error: any) {
            // basicToast(getDefaultError(error?.message ), 'error');
            button?.classList.remove('loading');
        }
    });
    
    const onContactPersonFormSubmit = handleContactPersonFormSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            // Simulate API call
            // await new Promise((resolve) => setTimeout(resolve, 5000));
            const contactId = (validated?.contactId?.toString() || '').trim();
            const httpRequestConfig = {
                ...await getHttpRequestConfig(contactId === '' ? 'POST' : 'PATCH'),
                url: '/customer/contact',
                data: { ...validated },
            };
            const process = contactId === '' ? 'Adding new contact' : 'Updating contact info';
            const response = await toast(axios(httpRequestConfig), `${process}...`);
            button?.classList.remove('loading');
            
            if (response?.success) {
                // Reset formConfig or navigate as needed
                await queryClient.invalidateQueries({ queryKey: ['addresses', 'contacts', 'customers'] });
                resetForm();
                resetContactPersonForm();
                button?.classList.remove('loading');
                navigate((previousPath || -1) as To, { replace: true });
                return;
            }
            button?.classList.remove('loading');
            // basicToast(getDefaultError(response?.message), 'error');
        } catch (error: any) {
            // basicToast(getDefaultError(error?.message ), 'error');
            button?.classList.remove('loading');
        }
    });
    
    
    const onSwitchTabHandler: ButtonClickFn = (event) => {
        event.preventDefault();
        const target = event.currentTarget;
        if (target && target.dataset.tab) {
            const tabId = target.dataset.tab;
            if (tabId === 'cancel') {
                navigate((previousPath || -1) as To, { replace: true });
                return;
            }
            onSwitchTab(tabId);
        }
    };
    
    const screenWidth = useScreenWidth();
    const mobilePadding = screenWidth <= 768 ? 'pr-sm-0 pl-sm-0' : '';
    const tabletPadding = screenWidth <= 810 ? 'pr-md-0 pl-md-0' : '';
    
    useEffect(() => {
        scrollToElement();
        return () => {
            setActiveTab('customer-info');
        };
    }, []);
    
    return (
        <div className='tab-content flex-fill'>
            <div className={`tab-pane fade ${activeTab === 'customer-info' ? 'active show' : ''}`} id='customer-info'>
                {activeTab === 'customer-info' && (
                    <Form onSubmit={onSubmit} className='flex-fill'>
                        <CustomerInfoForm
                            getElement={getElement}
                            formConfig={formData}
                            formInvalid={formInvalid}
                            onBlur={onBlur}
                            onChange={onChange}
                            onSelect={onReactSelectChange}
                            mobilePadding={mobilePadding}
                            onSwitchTabHandler={onSwitchTabHandler}
                            tabletPadding={tabletPadding}
                        />
                    </Form>
                )}
            </div>
            <div className={`tab-pane fade ${activeTab === 'customer-contact-person' ? 'active show' : ''}`}
                 id='customer-contact-person'>
                {activeTab === 'customer-contact-person' && (
                    <Form onSubmit={onContactPersonFormSubmit} className='flex-fill'>
                        <ContactInfoForm
                            getElement={getContactPersonElement}
                            formConfig={contactPersonData}
                            formInvalid={contactPersonFormInvalid}
                            onBlur={onContactPersonInputBlur}
                            onChange={onContactPersonInputChange}
                            mobilePadding={mobilePadding}
                            onSwitchTabHandler={onSwitchTabHandler}
                            tabletPadding={tabletPadding}
                        />
                    </Form>
                )}
            </div>
        </div>
    );
}
