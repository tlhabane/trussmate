import { useForm, useFormElements } from '../../../utils';
import { contactFormConfig } from '../config';
import { ContactPerson } from '../../../models';

export const useContactPersonForm = () => {
    const form = useForm(contactFormConfig);
    const getElement = useFormElements<ContactPerson>();
    
    return { ...form, getElement };
}
