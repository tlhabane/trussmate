import { FormState } from '../../types';
import { LoginFields } from './login.fields';

export const loginFormConfig: FormState<LoginFields> = {
    username: {
        value: '',
        error: '',
        type: 'text',
        label: 'Email or Tel',
        placeholder: 'you@example.com',
        required: true,
    },
    password: {
        value: '',
        error: '',
        type: 'password',
        label: 'Password',
        placeholder: '********',
        required: true,
    },
};
