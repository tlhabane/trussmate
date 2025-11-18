import { useCallback } from 'react';
import axios, { AxiosRequestConfig, AxiosError, AxiosResponse } from 'axios';
import { SetState, FormState } from '../../types';
import { BASE_URL } from '../../config';

type SetFormState<T extends Record<string, any>> = SetState<FormState<T>>;

export const useAxios = <T extends Record<string, any>>() => {
    const getErrorMessage = useCallback((errorMessage: string) => {
        const errorMessages = ['data validation error', 'request aborted'];
        if (
            errorMessages.filter(
                (msg) => errorMessage.toLowerCase().search(msg) !== -1,
            ).length > 0
        ) {
            return '';
        }
        
        return errorMessage;
    }, []);
    
    /**
     * Handles errors from Axios requests.
     * @param error - The error object from Axios or a generic Error.
     * @param setFormState
     * @returns A formatted error message.
     */
    const handleError = useCallback(
        (error: AxiosError | Error | any, setFormState?: SetFormState<T>) => {
            if (axios.isAxiosError(error)) {
                const { response } = error;
                if (response && response.data) {
                    const {
                        error: httpErrorMessage,
                        message,
                        fields: validationErrors,
                    } = response.data;
                    if (
                        validationErrors &&
                        Object.entries(validationErrors).length > 0 &&
                        setFormState
                    ) {
                        setFormState((prevState) => {
                            const updatedState = { ...prevState };
                            for (const [key, value] of Object.entries(
                                validationErrors as { [key: string]: string },
                            )) {
                                const tKey = key as keyof T;
                                if (key in updatedState) {
                                    updatedState[tKey] = {
                                        ...updatedState[tKey],
                                        error: value,
                                    };
                                }
                            }
                            return updatedState;
                        });
                    }
                    
                    const possibleErrorMessage = (httpErrorMessage as string) || message;
                    if (possibleErrorMessage) {
                        return getErrorMessage(possibleErrorMessage);
                    }
                }
            }
            return getErrorMessage(error.message);
        },
        [getErrorMessage],
    );
    
    return useCallback(
        async (
            requestConfig: AxiosRequestConfig,
            setFormState?: SetFormState<T>,
            actionButton?: HTMLButtonElement,
        ) => {
            try {
                actionButton?.classList.add('loading');
                // console.log({ ...requestConfig, baseURL: BASE_URL });
                const response = await axios({ ...requestConfig, baseURL: BASE_URL });
                actionButton?.classList.remove('loading');
                return (response as AxiosResponse).data;
            } catch (error: AxiosError | Error | any) {
                actionButton?.classList.remove('loading');
                const errorMessage = handleError(error, setFormState);
                throw new Error(errorMessage);
            }
        },
        [handleError],
    );
};
