import React, { JSX } from 'react';
import { useValidateFormElement } from '../../../utils';
import { FormInput, FormState, InputChangeFn, InputFocusFn, ReactSelectFn } from '../../../types';
import { BankAccount } from '../../../models';

type Props = {
    getElement: (name: any, props: FormInput<any>, handlers?: any) => JSX.Element;
    formConfig: FormState<BankAccount>;
    onBlur?: InputFocusFn<HTMLInputElement, void>;
    onChange: InputChangeFn<HTMLInputElement, void>;
    onSelect: ReactSelectFn<void>;
};

export const BankAccountFormElements: React.FC<Props> = (props): JSX.Element => {
    const { formConfig, getElement, onBlur, onChange, onSelect } = props;
    const validateFormElement = useValidateFormElement<BankAccount>();
    const getInputComponent = (inputName: string) => {
        const inputProps = validateFormElement(inputName as keyof BankAccount, formConfig);
        if (inputProps.type === 'select') {
            return getElement(inputName, inputProps, { onSelect });
        }
        
        return getElement(inputName, inputProps, { onBlur, onChange });
    };
    
    return (
        <>
            {getInputComponent('bankName')}
            {getInputComponent('bankAccountName')}
            {getInputComponent('bankAccountNo')}
            {getInputComponent('branchCode')}
        </>
    );
};
