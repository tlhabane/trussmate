import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import { JOB_BASE_URL } from '../../config';

export const useJobAxios = () => {
    return async (requestConfig: AxiosRequestConfig, actionButton?: HTMLButtonElement) => {
        try {
            actionButton?.classList.add('loading');
            const response = await axios({ ...requestConfig, baseURL: JOB_BASE_URL });
            actionButton?.classList.remove('loading');
            return (response as AxiosResponse).data;
        } catch (error: any) {
            if (axios.isAxiosError(error)) {
                const { response } = error;
                if (response && response.data) {
                    throw new Error(
                        response.data?.message || 'An error occurred while processing your request, please try again.',
                    );
                }
            }
            
            throw new Error(error?.message || 'An error occurred while processing your request, please try again.');
        }
    };
};
