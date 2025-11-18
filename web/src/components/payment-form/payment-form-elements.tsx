import React, { JSX } from 'react';
import { useValidateFormElement } from '../../utils';
import { FormInput, FormState, InputChangeFn, InputFocusFn, ReactSelectFn } from '../../types';
import { Transaction } from '../../models';

type Props = {
    getElement: (name: any, props: FormInput<any>, handlers?: any) => JSX.Element;
    formConfig: FormState<Transaction>;
    onBlur?: InputFocusFn<HTMLInputElement, void>;
    onChange: InputChangeFn<HTMLInputElement, void>;
    onSelect: ReactSelectFn<void>;
};

export const PaymentFormElements: React.FC<Props> = (props): JSX.Element => {
    const { formConfig, getElement, onBlur, onChange, onSelect } = props;
    const validateFormElement = useValidateFormElement<Transaction>();
    const getInputComponent = (inputName: string) => {
        const inputProps = validateFormElement(inputName as keyof Transaction, formConfig);
        if (inputProps.type === 'select') {
            return getElement(inputName, inputProps, { onSelect });
        }
        
        return getElement(inputName, inputProps, { onBlur, onChange });
    };
    
    return (
        <>
            {getInputComponent('invoiceNo')}
            <div className='row'>
                <div className='col-md-6'>
                    {getInputComponent('transactionDate')}
                </div>
                <div className='col-md-6'>
                    {getInputComponent('transactionMethod')}
                </div>
            </div>
            {getInputComponent('transactionAmount')}
            {getInputComponent('transactionDesc')}
        </>
    );
};
