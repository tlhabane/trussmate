import { FormInput, FormState } from '../../types';

export function useValidateFormElement<T extends Record<string, any>>() {
    return (name: keyof T, formConfig: FormState<T>) => {
        const formElementProps = formConfig[name];
        if (!formElementProps) {
            throw new Error(`Form config does not contain and element named: ${name.toString()}`);
        }
        return formElementProps as unknown as FormInput<T>;
    };
}
