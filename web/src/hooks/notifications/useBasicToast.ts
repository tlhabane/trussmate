import { useCallback } from 'react';
import { toast, ToastType, ToastOptions } from 'react-hot-toast';

export const useBasicNotification = () =>
    useCallback((notificationMessage: string, notificationType: ToastType, options: ToastOptions = {}) => {
        if (notificationMessage && notificationMessage.trim().length > 0) {
            const toastOptions: ToastOptions = {
                duration: 4000,
                position: 'top-center',
                ...options,
            };

            switch (notificationType) {
                case 'error':
                    return toast.error(notificationMessage, toastOptions);
                case 'success':
                    return toast.success(notificationMessage, toastOptions);
                default:
                    return toast(notificationMessage, toastOptions);
            }
        }
        return '';
    }, []);
