import React, { JSX } from 'react';
import { useValidateFormElement } from '../../../utils';
import { FormInput, FormState, InputChangeFn, InputFocusFn, ReactSelectFn } from '../../../types';
import { Task } from '../../../models';

type Props = {
    getElement: (name: any, props: FormInput<any>, handlers?: any) => JSX.Element;
    formConfig: FormState<Task>;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect: ReactSelectFn<void>;
};

export const TaskFormElements: React.FC<Props> = (props) => {
    const { formConfig, getElement, onBlur, onChange, onSelect } = props;
    const validateFormElement = useValidateFormElement<Task>();
    const getInputComponent = (inputName: string) => {
        const inputProps = validateFormElement(inputName as keyof Task, formConfig);
        if (inputProps.type === 'select') {
            return getElement(inputName, inputProps, { onSelect });
        }
        
        return getElement(inputName, inputProps, { onBlur, onChange });
    };
    
    return (
        <>
            {getInputComponent('taskName')}
            {getInputComponent('taskDescription')}
            {getInputComponent('taskDocument')}
            {getInputComponent('taskPaymentType')}
            {(formConfig.taskPaymentType.value === 'fixed' || formConfig.taskPaymentType.value === 'percentage') && (
                getInputComponent('taskPayment')
            )}
            {getInputComponent('taskDays')}
            {getInputComponent('taskAction')}
            {getInputComponent('taskFrequency')}
        </>
    );
};
