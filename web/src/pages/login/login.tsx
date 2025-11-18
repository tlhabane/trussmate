import React, { JSX } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthorisedUser } from '../../models';
import { APP_NAME } from '../../config';
import { LoginForm } from './login-form';
import { useLoginForm } from './useLoginForm';
import { getHttpRequestConfig, useAxios } from '../../utils';
import { saveToLocalStorage } from '../../store';
import { usePromiseToast } from '../../hooks';

type LoginFields = {
    username: string;
    password: string;
};

export default function Login(): JSX.Element {
    document.title = `Login :: ${APP_NAME}`;
    const {
        formData,
        onChange,
        onBlur,
        handleSubmit,
        formInvalid,
        setFormData,
        resetForm,
        getElement,
    } = useLoginForm();
    
    const axios = useAxios<LoginFields>();
    const toast = usePromiseToast();
    const navigate = useNavigate();
    const onSubmit = handleSubmit(async (validated, button) => {
        try {
            const { username, password } = validated;
            
            const config = {
                ...getHttpRequestConfig('POST'),
                url: '/login',
                auth: { username, password },
            };
            button?.classList.add('loading');
            const response = await toast(axios({ ...config }, setFormData));
            if ((response as AuthorisedUser)?.token) {
                saveToLocalStorage(response as AuthorisedUser);
                resetForm();
                // Redirect to dashboard after successful login
                navigate('/home', { replace: true });
            }
            button?.classList.remove('loading');
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    });
    
    return (
        <LoginForm
            getElement={getElement}
            formConfig={formData}
            formInvalid={formInvalid}
            onChange={onChange}
            onBlur={onBlur}
            onSubmit={onSubmit}
        />
    );
}
