import { useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { useBasicNotification } from './notifications';
import { getHttpRequestConfig, useAxios } from '../utils';
import { AuthorisedUser } from '../models';
import { loadFromLocalStorage, clearLocalStorage } from '../store';

export const useAuthenticatedUser = () => {
    const axios = useAxios();
    const navigate = useNavigate();
    const toast = useBasicNotification();
    
    const backToLogin = useCallback((message?: string) => {
        navigate('/', { replace: true });
        toast(`${message || 'Invalid or expired session, please login again'}`, "error");
        clearLocalStorage();
    }, [navigate, toast]);
    
    return useCallback(async (): Promise<AuthorisedUser | void> => {
        try {
            const authenticatedUser = loadFromLocalStorage() as AuthorisedUser;
            if (!authenticatedUser || authenticatedUser?.token?.trim() === '') {
                backToLogin();
                return;
            }
            const httpRequestConfig = {
                ...getHttpRequestConfig('POST', authenticatedUser.token),
                url: '/session/validate',
            };
    
            await axios(httpRequestConfig);
            return authenticatedUser;
        } catch (error: any) {
            backToLogin(error?.message);
        }
    }, [axios, backToLogin])
};
