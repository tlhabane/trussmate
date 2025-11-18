import { loginFormConfig } from './login.form.config';
import { useForm, useFormElements } from '../../utils';
import { LoginFields } from './login.fields';

export const useLoginForm = () => {
    const form = useForm(loginFormConfig);
    const getElement = useFormElements<LoginFields>();
    
    return { ...form, getElement };
};
