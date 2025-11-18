import { useForm, useFormElements } from '../../utils';
import { FormFields } from './form.fields';
import { formConfig } from './form.config';

export const useForgotPasswordForm = () => {
    const form = useForm(formConfig);
    const getElement = useFormElements<FormFields>();
    
    return { ...form, getElement };
};
