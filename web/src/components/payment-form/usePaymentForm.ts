import { useForm, useFormElements } from '../../utils';
import { Transaction } from '../../models';
import { paymentFormConfig } from './payment-form-config';

export const usePaymentForm = (formState = paymentFormConfig) => {
    const form = useForm<Transaction>(formState);
    const getElement = useFormElements<Transaction>();
    
    return { ...form, getElement };
};
