import { useForm, useFormElements } from '../../../utils';
import { addressFormConfig } from '../config';
import { Address } from '../../../models';

export const useAddressForm = () => {
    const form = useForm(addressFormConfig);
    const getElement = useFormElements<Address>();
    
    return { ...form, getElement };
};
