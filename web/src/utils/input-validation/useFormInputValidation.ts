import { useCallback } from 'react';
import { FormInput } from '../../types';
import { defaultMessages } from './messages';

export const useFormInputValidation = () => {
    /**
     * Email validation
     *
     * @param {string} emailAddress
     * @return {boolean}
     */
    const isEmailAddressValid = (emailAddress: string) => {
        const regExp =
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return regExp.test(String(emailAddress).toLowerCase());
    };

    /**
     * Telephone number validation
     *
     * @param {string} telephoneNum
     * @return {boolean}
     */
    const isTelNumberValid = (telephoneNum: string) => {
        const regExp = /^[+]?(1-|1\s|1|\d{3}-|\d{3}\s|)?((\(\d{3}\))|\d{3})(-|\s)?(\d{3})(-|\s)?(\d{4})$/g;
        return regExp.test(String(telephoneNum).toLowerCase());
    };
    /**
     * Validates the input properties based on type and required fields.
     *
     * @param {Record<string, any>} inputProps
     * @return {updatedInputProps: {[p: string]: any}, isValid: boolean} - Returns true if all validations pass, otherwise false.
     */
    return useCallback(<T>(inputProps: FormInput<T>) => {
        const updatedInputProps = { ...inputProps, error: '' };
        const { value, required, type, validation } = inputProps;
        
        let isValid = true;
        
        if ((value === undefined || value === null || value === '') && required) {
            updatedInputProps.error = defaultMessages.required;
            isValid = false;
        } else if (type === 'email' && !isEmailAddressValid(String(value)) && required) {
            updatedInputProps.error = defaultMessages.invalidEmail;
            isValid = false;
        } else if (type === 'tel' && !isTelNumberValid(String(value)) && required) {
            updatedInputProps.error = defaultMessages.invalidTel;
            isValid = false;
        } else if (validation?.pattern && !validation.pattern.test(String(value)) && required) {
            updatedInputProps.error = validation.errorMessage || 'Invalid input';
            isValid = false;
        } else if (validation?.custom && !validation.custom(value)) {
            updatedInputProps.error = validation.errorMessage || 'Invalid input';
            isValid = false;
        }
        
        return { updatedInputProps, isValid };
    }, []);
};
