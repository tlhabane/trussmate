import { useForm, useFormElements } from '../../utils';
import { BankAccount } from '../../models';
import { bankAccountFormConfig } from './bank-account-form-config';

export const useBankAccountForm = () => {
    const form = useForm<BankAccount>(bankAccountFormConfig);
    const getElement = useFormElements<BankAccount>();
    
    return { ...form, getElement };
};
