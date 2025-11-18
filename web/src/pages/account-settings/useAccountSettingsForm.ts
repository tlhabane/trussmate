import { useForm, useFormElements } from '../../utils';
import { accountSettingsFormConfig } from './account-settings-form-config';
import { AccountInfo } from '../../models';

export const useAccountSettingsForm = () => {
    const form = useForm(accountSettingsFormConfig);
    const getElement = useFormElements<AccountInfo>();
    
    return { ...form, getElement };
};
