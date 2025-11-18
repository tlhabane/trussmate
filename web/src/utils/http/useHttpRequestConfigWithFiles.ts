import { Method } from 'axios';
import { useHttpRequestConfig } from './useHttpRequestConfig';

export const useHttpRequestConfigWithFiles = <T extends Record<string, any>>() => {
    const getHttpRequestConfig = useHttpRequestConfig();
    
    return async (data: T, method: Method = 'GET', files: File[] = []) => {
        const httpRequestConfig = files.length > 0
            ? await getHttpRequestConfig(method, 'multipart/form-data')
            : await getHttpRequestConfig(method);
        
        httpRequestConfig['data'] = data;
        
        if (files.length > 0) {
            const formData = new FormData();
            files.forEach(file => {
                formData.append('files[]', file);
            });
            
            Object.entries(data).forEach(([key, value]) => {
                if (value !== undefined && value !== null) {
                    const formDataValue = typeof value === 'object' || Array.isArray(value)
                        ? JSON.stringify(value)
                        : value.toString();
                    formData.append(key, formDataValue);
                }
            });
            
            httpRequestConfig['data'] = formData;
            console.log({ ...httpRequestConfig });
        }
        
        return httpRequestConfig;
    };
};
