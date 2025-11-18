import { AxiosRequestConfig, Method } from 'axios';

export const getHttpRequestConfig = (method: Method = 'GET', token = '') => {
    const httpRequestConfig: AxiosRequestConfig = {
        method,
        headers: {
            'Content-Type': 'application/json',
        },
    };
    // If a token is provided, add it to the headers
    if (token.trim() !== '') {
        httpRequestConfig.headers = {
            ...httpRequestConfig.headers,
            Authorization: `Bearer ${token.trim()}`,
        };
    }

    return httpRequestConfig;
};
