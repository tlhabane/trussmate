import React, { JSX, useEffect, useMemo, useRef, useState } from 'react';
import { useAccountSettingsForm } from './useAccountSettingsForm';
import { useAxios, useFetchData, useHttpRequestConfigWithFiles } from '../../utils';
import { usePromiseToast, useScreenWidth } from '../../hooks';
import { Button, ContainerSpinner, Form } from '../../components';
import { AccountInfo } from '../../models';
import { ButtonClickFn, InputChangeFn, FormState } from '../../types';
import { APP_NAME } from '../../config';

let initLoad = true;
export default function AccountSettings(): JSX.Element {
    document.title = `Account Settings :: ${APP_NAME}`;
    const { formData, formInvalid, getElement, handleSubmit, onBlur, onChange, setFormData } = useAccountSettingsForm();
    
    const fetchConfig = useMemo(() => ({
        url: '/account/info',
        queryKey: ['accountInfo'],
    }), []);
    
    const { data, isLoading } = useFetchData(fetchConfig);
    useEffect(() => {
        if (data && initLoad) {
            initLoad = false;
            const updatedAccounts = (data?.records || []) as AccountInfo[];
            if (updatedAccounts.length > 0) {
                const updatedFormData = Object.entries(updatedAccounts[0]).reduce((acc, [key, value]) => {
                    const dataIndex = key as keyof AccountInfo;
                    acc[dataIndex] = { ...formData[dataIndex] };
                    if (formData[dataIndex]) {
                        acc[dataIndex].value = value;
                    }
                    return acc;
                }, {} as FormState<AccountInfo>);
                setFormData(updatedFormData);
            }
        }
    }, [data, formData, setFormData]);
    
    const uploadLogoInput = useRef<HTMLInputElement | null>(null);
    const handleLogoUpload: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        uploadLogoInput.current?.click();
    };
    
    const [uploadedLogoFiles, setUploadedLogoFiles] = useState<File[]>([]);
    const onLogoUpload: InputChangeFn<HTMLInputElement> = async (event) => {
        const { files } = event.currentTarget;
        if (files) {
            const fileArray = Array.from(files);
            setUploadedLogoFiles(fileArray);
        }
    };
    
    const axios = useAxios();
    const getHttpRequestConfig = useHttpRequestConfigWithFiles<AccountInfo>();
    const toast = usePromiseToast();
    
    const onSubmit = handleSubmit(async (validated, button) => {
        try {
            button?.classList.add('loading');
            const httpRequestConfig = {
                ...await getHttpRequestConfig(validated, 'POST', uploadedLogoFiles),
                url: '/account/info',
            };
            
            await toast(axios(httpRequestConfig), 'Updating account info...');
            button?.classList.remove('loading');
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    const screenWidth = useScreenWidth();
    const mobilePadding = screenWidth <= 768 ? 'pr-sm-0 pl-sm-0' : '';
    const tabletPadding = screenWidth <= 810 ? 'pr-md-0 pl-md-0' : '';
    
    if (isLoading && !data) {
        return <ContainerSpinner />;
    }
    
    const RegisteredNameInput = getElement('registeredName', formData['registeredName'], { onBlur, onChange });
    const TradingNameInput = getElement('tradingName', formData['tradingName'], { onBlur, onChange });
    const RegNoInput = getElement('registrationNo', formData['registrationNo'], { onBlur, onChange });
    const VatNoInput = getElement('vatNo', formData['vatNo'], { onBlur, onChange });
    const TelInput = getElement('tel', formData['tel'], { onBlur, onChange });
    const AltTelInput = getElement('altTel', formData['altTel'], { onBlur, onChange });
    const EmailInput = getElement('email', formData['email'], { onBlur, onChange });
    const WebInput = getElement('web', formData['web'], { onBlur, onChange });
    const AddressInput = getElement('address', formData['address'], { onBlur, onChange });
    
    const updatedLogo = uploadedLogoFiles.length > 0 ? URL.createObjectURL(uploadedLogoFiles[0]) : '';
    const currentLogo = formData.logo.value;
    const logoAvailable = updatedLogo.trim() !== '' || currentLogo.trim() !== '';
    
    return (
        <div className='flex-fill'>
            <Form onSubmit={onSubmit}>
                <div className='row mb-5 pb-5'>
                    <div className='col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-sm-10 offset-sm-1 mb-5'>
                        <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                            <div className='row'>
                                <div className='col-9 d-flex flex-row align-items-center'>
                                    <span className='pr-3'>
                                        <i className='custom-icon icon tag' />
                                    </span>
                                    <h6>Business Details</h6>
                                </div>
                                <div className='col-3'>
                                    <input
                                        accept='image/*'
                                        onChange={onLogoUpload}
                                        ref={uploadLogoInput}
                                        type='file'
                                        style={{ opacity: 0, position: 'absolute' }}
                                    />
                                    <Button
                                        className='btn-success btn-block'
                                        onClick={handleLogoUpload}>
                                        <i className='custom-icon icon position-relative upload mx-2' />
                                        Logo
                                    </Button>
                                </div>
                            </div>
                            <hr className='default' />
                            {logoAvailable && (
                                <div className='row'>
                                    <div className='col-12 d-flex align-items-center justify-content-center'>
                                        <div
                                            style={{ width: 300, height: 300 }}
                                            className='d-flex align-items-center justify-content-center'>
                                            <img
                                                src={updatedLogo || currentLogo}
                                                style={{ maxWidth: 300 }}
                                                alt={formData.registeredName.value || formData.tradingName.value}
                                            />
                                        </div>
                                    </div>
                                </div>
                            )}
                            {RegisteredNameInput}
                            {TradingNameInput}
                            <div className='row'>
                                <div
                                    className={`col-lg-6 col-md-6 col-sm-12 pr-0 pl-0 ${tabletPadding} ${mobilePadding}`}>
                                    {RegNoInput}
                                </div>
                                <div
                                    className={`col-lg-6 col-md-6 col-sm-12 pr-0 pl-0 ${tabletPadding} ${mobilePadding}`}>
                                    {VatNoInput}
                                </div>
                            </div>
                        </div>
                        <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                            <div className='d-flex flex-row align-items-center'>
                                <span className='pr-3'>
                                    <i className='custom-icon icon phone' />
                                </span>
                                <h6>Contact Details</h6>
                            </div>
                            <hr className='default' />
                            {EmailInput}
                            <div className='row'>
                                <div
                                    className={`col-lg-6 col-md-6 col-sm-12 pr-0 pl-0 ${tabletPadding} ${mobilePadding}`}>
                                    {TelInput}
                                </div>
                                <div
                                    className={`col-lg-6 col-md-6 col-sm-12 pr-0 pl-0 ${tabletPadding} ${mobilePadding}`}>
                                    {AltTelInput}
                                </div>
                            </div>
                            {WebInput}
                            {AddressInput}
                        </div>
                        
                        <div className='row'>
                            <div className='col-12 pr-sm-0 pl-sm-0'>
                                <Button type='submit' className='btn-primary btn-block' disabled={formInvalid}>
                                    <i className='custom-icon icon left-icon save' />
                                    Save
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </Form>
        </div>
    );
}
