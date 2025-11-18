import { useCallback } from 'react';
import { toast } from 'react-hot-toast';

type Request = Promise<Record<string, any>>;

export const usePromiseToast = () =>
    useCallback(
        (request: Request, loading = 'Processing...') =>
            toast.promise(request, {
                loading,
                success: (response) => response?.success,
                error: (error) => error.message.toString(),
            }),
        [],
    );
