import { FormState } from '../../types';
import { FormFields } from './form.fields';

export const formConfig: FormState<FormFields> = {
    username: {
        value: '',
        error: '',
        type: 'text',
        label: 'Email or Tel',
        placeholder: 'you@example.com',
        required: true,
    },
}
