import { useForm, useFormElements } from '../../utils';
import { SaleTaskForm } from '../../models';
import { saleTaskFormConfig } from './sale-task-form-config';

export const useSaleTaskForm = () => {
    const form = useForm(saleTaskFormConfig);
    const getElement = useFormElements<SaleTaskForm>();
    
    return { ...form, getElement };
};
