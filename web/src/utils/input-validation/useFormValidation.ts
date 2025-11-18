import { useFormInputValidation } from './useFormInputValidation';
import { FormState } from '../../types';

export const useFormValidation = <T extends Record<string, any>>() => {
    /**
     * Validates the formConfig inputs and updates the formConfig state.
     *
     * @param {Record<string, any>} inputProps
     * @return {updatedInputProps: {[p: string]: any}, isValid: boolean} - Returns true if all validations pass, otherwise false.
     */
    const validateFormInput = useFormInputValidation();
    
    return (formData: FormState<T>) => {
        let isValid = true;
        const updatedFormState = {} as FormState<T>;
        
        for (const key in formData) {
            const { updatedInputProps, isValid: inputValid } = validateFormInput(formData[key]);
            updatedFormState[key] = updatedInputProps;
            if (!inputValid) isValid = false;
        }
        
        return { isValid, updatedFormState };
    };
};
