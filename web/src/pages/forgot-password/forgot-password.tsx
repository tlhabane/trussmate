import React, { JSX } from 'react';
import { useNavigate } from 'react-router-dom';
import { APP_NAME } from '../../config';
import { ForgotPasswordForm } from './forgot-password-form';
import { getHttpRequestConfig, useAxios } from '../../utils';
import { usePromiseToast } from '../../hooks';
import { useForgotPasswordForm } from './useForgotPasswordForm';

type Fields = {
    username: string;
};

export default function Login(): JSX.Element {
    document.title = `Forgot Password :: ${APP_NAME}`;

    const {
        formData,
        getElement,
        onChange,
        onBlur,
        handleSubmit,
        formInvalid,
        setErrorsFromAPI,
        resetForm
    } = useForgotPasswordForm();

    const axios = useAxios<Fields>();
    const toast = usePromiseToast();
    const navigate = useNavigate();
    const onSubmit = handleSubmit(async (validated, button) => {
        try {
            const { username } = validated;
            const config = {
                ...getHttpRequestConfig('POST'),
                url: '/forgot-password',
                data: { username },
            };
            button?.classList.add('loading');
            await toast(axios({ ...config }));
            resetForm();

            navigate('/update-password', { replace: true });
            button?.classList.remove('loading');
        } catch (error: any) {
            button?.classList.remove('loading');
            if (error.fields) {
                setErrorsFromAPI(error.fields);
            }
            console.error('Error during formConfig submission:', error);
        }
    });

    return (
        <ForgotPasswordForm
            getElement={getElement}
            formConfig={formData}
            formInvalid={formInvalid}
            onBlur={onBlur}
            onChange={onChange}
            onSubmit={onSubmit}
        />
    );
}
