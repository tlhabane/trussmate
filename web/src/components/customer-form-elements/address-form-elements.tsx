import React, { JSX } from 'react';
import { useValidateFormElement } from '../../utils';
import { Address } from '../../models';
import {
    FormInput,
    FormState,
    InputChangeFn,
    InputFocusFn, ReactSelectFn,
} from '../../types';

type Props = {
    getElement: (name: any, props: FormInput<Address>, handlers?: any) => JSX.Element;
    formConfig: FormState<Address>;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect: ReactSelectFn<void>;
};

export const AddressFormElements: React.FC<Props> = (props): JSX.Element => {
    const {
        formConfig,
        getElement,
        onBlur,
        onChange,
        onSelect,
    } = props;
    
    const validateFormElement = useValidateFormElement<Address>();
    const getElementProps = (name: string) => validateFormElement(name as keyof Address, formConfig);
    
    const AddressTypeSelect = getElement('billingAddress', getElementProps('billingAddress'), { onSelect });
    const AddressInput = getElement('fullAddress', getElementProps('fullAddress'), { onBlur, onChange });
    
    return (
        <>
            <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                <div className='d-flex flex-row align-items-center'>
                    <span className='pr-3'>
                        <i className='custom-icon icon tag' />
                    </span>
                    <h6>Address Type</h6>
                </div>
                <hr className='default' />
                {AddressTypeSelect}
            </div>
            {AddressInput}
        </>
    );
};
