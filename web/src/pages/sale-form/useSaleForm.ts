import { Sale } from '../../models';
import { FormState } from '../../types';
import { useForm, useFormElements } from '../../utils';

export const useSaleForm = (sale: FormState<Sale>) => {
    const form = useForm(sale);
    const getElement = useFormElements<Sale>();
    
    return { ...form, getElement };
};
