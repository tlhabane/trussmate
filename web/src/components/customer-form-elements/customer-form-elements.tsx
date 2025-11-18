import React, { JSX } from 'react';
import { useValidateFormElement } from '../../utils';
import { Customer } from '../../models';
import {
    FormInput,
    FormState,
    InputChangeFn,
    InputFocusFn, ReactSelectFn,
} from '../../types';

type Props = {
    partialForm?: boolean;
    getElement: (name: any, props: FormInput<Customer>, handlers?: any) => JSX.Element;
    formConfig: FormState<Customer>;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect: ReactSelectFn<void>;
    mobilePadding: string;
    tabletPadding: string;
};

export const CustomerFormElements: React.FC<Props> = (props): JSX.Element => {
    const {
        partialForm = false,
        formConfig,
        getElement,
        onBlur,
        onChange,
        onSelect,
        mobilePadding,
        tabletPadding,
    } = props;
    
    const validateFormElement = useValidateFormElement<Customer>();
    const getElementProps = (name: string) => validateFormElement(name as keyof Customer, formConfig);
    
    const CustomerTypeSelect = getElement('customerType', getElementProps('customerType'), { onSelect });
    const CustomerNameInput = getElement('customerName', getElementProps('customerName'), { onBlur, onChange });
    const RegNoInput = getElement('registrationNo', getElementProps('registrationNo'), { onBlur, onChange });
    const VatNoInput = getElement('vatNo', getElementProps('vatNo'), { onBlur, onChange });
    const TelInput = getElement('tel', getElementProps('tel'), { onBlur, onChange });
    const AltTelInput = getElement('altTel', getElementProps('altTel'), { onBlur, onChange });
    const EmailInput = getElement('email', getElementProps('email'), { onBlur, onChange });
    const WebInput = getElement('web', getElementProps('web'), { onBlur, onChange });
    const AddressInput = getElement('address', getElementProps('address'), { onBlur, onChange });
    
    const businessAccount = formConfig.customerType.value === 'business';
    
    return (
        <>
            <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                <div className='d-flex flex-row align-items-center'>
                    <span className='pr-3'>
                        <i className='custom-icon icon tag' />
                    </span>
                    <h6>Customer Type</h6>
                </div>
                <hr className='default' />
                {CustomerTypeSelect}
            </div>
            <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                <div className='d-flex flex-row align-items-center'>
                    <span className='pr-3'>
                        <i className={`custom-icon icon user${businessAccount ? 's' : ''}`} />
                    </span>
                    <h6>{businessAccount ? 'Business' : 'Customer'} Details</h6>
                </div>
                <hr className='default' />
                {CustomerNameInput}
                {businessAccount && (
                    <div className='row'>
                        <div
                            className={`col-lg-6 col-md-6 col-sm-12 pr-0 pl-0 ${tabletPadding} ${mobilePadding}`}>
                            {RegNoInput}
                        </div>
                        <div
                            className={`col-lg-6 col-md-6 col-sm-12 pr-0 pl-0 ${tabletPadding} ${mobilePadding}`}>
                            {VatNoInput}
                        </div>
                    </div>
                )}
                {!businessAccount && VatNoInput}
            </div>
            <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                <div className='d-flex flex-row align-items-center'>
                    <span className='pr-3'>
                        <i className='custom-icon icon phone' />
                    </span>
                    <h6>Contact Details</h6>
                </div>
                <hr className='default' />
                {EmailInput}
                <div className='row'>
                    <div className={`col-lg-6 col-md-6 col-sm-12 pr-0 pl-0 ${tabletPadding} ${mobilePadding}`}>
                        {TelInput}
                    </div>
                    <div className={`col-lg-6 col-md-6 col-sm-12 pr-0 pl-0 ${tabletPadding} ${mobilePadding}`}>
                        {AltTelInput}
                    </div>
                </div>
                {WebInput}
                {!partialForm && AddressInput}
            </div>
        </>
    );
};
