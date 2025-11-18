import { AxiosRequestConfig, Method } from 'axios';
import { useAuthenticatedUser } from '../../hooks';

export const useHttpRequestConfig = () => {
    const getAuthenticatedUser = useAuthenticatedUser();
    return async (method: Method = 'GET', contentType = 'application/json') => {
        const authenticatedUser = await getAuthenticatedUser();
        const httpRequestConfig: AxiosRequestConfig = {
            method,
            headers: {
                'Content-Type': contentType,
            },
        };
        
        if (authenticatedUser) {
            const { token } = authenticatedUser;
            httpRequestConfig.headers = {
                ...httpRequestConfig.headers,
                Authorization: `Bearer ${token.trim()}`,
            };
        }
        
        return httpRequestConfig;
    };
};
