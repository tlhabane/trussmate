import { useCallback, useRef, useState } from 'react';
import {
    FormState,
    FormSubmitFn,
    InputChangeFn,
    InputFocusFn,
    ReactSelectFn,
    ReactSelectSingleOption,
    SignaturePadChangeFn,
} from '../../types';
import { useFormInputValidation, useFormValidation } from '../input-validation';

export function useForm<T extends Record<string, any>>(initialState: FormState<T>) {
    const initialRef = useRef(initialState);
    const [formData, setFormData] = useState<FormState<T>>(initialState);
    
    const [hasChanges, setHasChanges] = useState(false);
    const isFormChanged = useCallback((current: FormState<T>, initial: FormState<T>): boolean => {
        return Object.keys(current).some((key) => {
            const name = key as keyof T;
            return current[name].value !== initial[name].value;
        });
    }, []);
    
    const validateInput = useFormInputValidation();
    const validateForm = useFormValidation<T>();
    
    /**
     * Handles the change event for react-select components, updating the formConfig state with the selected value.
     * @param name - The name of the formConfig field being updated.
     * @param event - The change event from react-select.
     */
    const onReactSelectChange: ReactSelectFn = useCallback((name, event) => {
        const value = Array.isArray(event)
            ? event.map((item: ReactSelectSingleOption) => item?.value)
            : (event as ReactSelectSingleOption).value ?? '';
        
        setFormData((prev) => {
            const updated = {
                ...prev,
                [name]: {
                    ...prev[name],
                    value,
                    error: '',
                },
            };
            setHasChanges(isFormChanged(updated, initialRef.current));
            return updated;
        });
    }, [isFormChanged]);
    
    /**
     * Handles the change event for react-signature components, updating the formConfig state with the selected value.
     * @param name - The name of the formConfig field being updated.
     * @param signature - Base64 encoded data uri (image).
     */
    const onSignature: SignaturePadChangeFn = useCallback((name, signature) => {
        setFormData((prev) => {
            const updated = {
                ...prev,
                [name]: {
                    ...prev[name],
                    value: signature,
                    error: '',
                },
            };
            setHasChanges(isFormChanged(updated, initialRef.current));
            return updated;
        });
    }, [isFormChanged]);
    
    /**
     * Handles the change event for input fields, updating the formConfig state with the new value and clearing any errors.
     * @param event - The change event from the input element.
     */
    const onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement> = useCallback(
        (event) => {
            const { name, value, type } = event.target;
            const key = name as keyof T;
            
            setFormData((prev) => {
                const inputType = prev[key].type || type;
                let newValue: any = value;
                if (inputType === 'checkbox') {
                    newValue = (event.target as HTMLInputElement).checked;
                }
                const updated = {
                    ...prev,
                    [key]: {
                        ...prev[key],
                        value: newValue,
                        error: '',
                    },
                };
                setHasChanges(isFormChanged(updated, initialRef.current));
                return updated;
            });
        },
        [isFormChanged],
    );
    
    /**
     * Handles the blur event for input fields, validating the input and updating the formConfig state.
     * @param event - The focus event from the input element.
     */
    const onBlur: InputFocusFn<HTMLInputElement | HTMLTextAreaElement> = useCallback(
        (event) => {
            const { name } = event.target;
            const key = name as keyof T;
            
            setFormData((prev) => {
                const result = validateInput(prev[key]);
                if (!result.isValid) {
                    return {
                        ...prev,
                        [key]: result.updatedInputProps,
                    };
                }
                return prev;
            });
        },
        [validateInput],
    );
    
    /**
     * Returns validated data from the formConfig state, trimming string values.
     * @param data - The current formConfig state.
     * @returns An object with validated and trimmed values.
     */
    const getValidatedData = useCallback((data: FormState<T>) => {
        return Object.entries(data).reduce((acc, [key, input]) => {
            acc[key as keyof T] =
                typeof input.value === 'string' ? input.value.trim() : input.value;
            return acc;
        }, {} as Record<keyof T, any>);
    }, []);
    
    /**
     * Handles formConfig submission, validates the formConfig data, and calls the provided callback with validated data.
     * @param callback - Function to call with validated formConfig data and the submit button element.
     */
    const handleSubmit = useCallback(
        (callback: (data: Record<keyof T, any>, button?: HTMLButtonElement) => void): FormSubmitFn => (event) => {
            event.preventDefault();
            const formSubmitButton = event.currentTarget.querySelector('button[type="submit"]') as HTMLButtonElement;
            const { isValid, updatedFormState } = validateForm(formData);
            if (!isValid) {
                setFormData(updatedFormState);
                return;
            }
            const validated = getValidatedData(formData);
            callback(validated, formSubmitButton);
            setHasChanges(false);
        },
        [formData, getValidatedData, validateForm],
    );
    
    /**
     * Sets error messages from an API response, updating the formConfig state with the provided errors.
     * @param errors - An object containing error messages for specific fields.
     */
    const setErrorsFromAPI = useCallback((errors: Partial<Record<keyof T, string>>) => {
        setFormData((prev) => {
            const next = { ...prev };
            for (const key in errors) {
                if (key in next) {
                    next[key] = {
                        ...next[key],
                        error: errors[key] || '',
                    };
                }
            }
            return next;
        });
    }, []);
    
    /**
     * Sets an error message for a specific formConfig field, updating the formConfig state.
     * @param name - The name of the formConfig field to update.
     * @param message - The error message to set for the field.
     * @example
     * Set (runtime) custom error messages
     *
     * if (!formData.password.value.includes('@')) {
     *   setError('password', 'Password must include a special character');
     * }
     */
    const setError = useCallback((name: keyof T, message: string) => {
        setFormData((prev) => ({
            ...prev,
            [name]: {
                ...prev[name],
                error: message,
            },
        }));
    }, []);
    
    /**
     * Resets the formConfig to its initial state, clearing all values and error messages.
     * @example
     * Reset formConfig after successful submission
     *
     * const {
     *   setValue,
     *   resetField,
     *   resetForm,
     *   formData,
     *   handleSubmit
     * } = useForm<LoginFields>(initialFormState);
     *
     * const onSubmit = (data) => {
     *   // handle submission logic
     *   resetForm();
     * };
     */
    const resetForm = useCallback(() => {
        setFormData(initialRef.current);
        setHasChanges(false);
    }, []);
    
    /**
     * Sets the value of a specific formConfig field, clearing any existing error messages.
     * @param name - The name of the formConfig field to update.
     * @param value - The new value for the formConfig field.
     * @example
     * Fill username from query param
     *
     * const {
     *   setValue,
     *   resetField,
     *   resetForm,
     *   formData,
     *   handleSubmit
     * } = useForm<LoginFields>(initialFormState);
     *
     * useEffect(() => {
     *   const prefill = new URLSearchParams(location.search).get('username');
     *   if (prefill) {
     *     setValue('username', prefill);
     *   }
     * }, []);
     */
    const setValue = useCallback((name: keyof T, value: any) => {
        setFormData((prev) => {
            const updated = {
                ...prev,
                [name]: {
                    ...prev[name],
                    value,
                    error: '',
                },
            };
            setHasChanges(isFormChanged(updated, initialRef.current));
            return updated;
        });
    }, [isFormChanged]);
    
    /**
     * Resets a specific formConfig field to its initial state, clearing the value and error message.
     * @param name - The name of the formConfig field to reset.
     * @example
     * Reset password if user changes email
     *
     * useEffect(() => {
     *   resetField('password');
     * }, [formData.username.value]);
     */
    const resetField = useCallback((name: keyof T) => {
        setFormData((prev) => {
            const updated = {
                ...prev,
                [name]: {
                    ...prev[name],
                    value: initialRef.current[name].value,
                    error: '',
                },
            };
            setHasChanges(isFormChanged(updated, initialRef.current));
            return updated;
        });
    }, [isFormChanged]);
    
    
    const formInvalid = Object.values(formData).some(
        (field) => field.error || (typeof field.value === 'string' && field.value.trim() === '' && field.required),
    );
    
    return {
        formData,
        hasChanges,
        setFormData,
        onChange,
        onReactSelectChange,
        onSignature,
        onBlur,
        handleSubmit,
        formInvalid,
        getValidatedData,
        setError,
        setErrorsFromAPI,
        setValue,
        resetField,
        resetForm,
    };
}
