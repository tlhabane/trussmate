import { useForm, useFormElements } from '../../../utils';
import { customerFormConfig } from '../config';
import { Customer } from '../../../models';

export const useCustomerForm = () => {
    const form = useForm(customerFormConfig);
    const getElement = useFormElements<Customer>();
    
    return { ...form, getElement };
}
