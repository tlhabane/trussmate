import React, { JSX } from 'react';
import { useValidateFormElement } from '../../utils';
import { CustomerContactPerson } from '../../models';
import {
    FormInput,
    FormState,
    InputChangeFn,
    InputFocusFn,
} from '../../types';

type Props = {
    getElement: (name: any, props: FormInput<CustomerContactPerson>, handlers?: any) => JSX.Element;
    formConfig: FormState<CustomerContactPerson>;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    mobilePadding: string;
    tabletPadding: string;
};

export const ContactFormElements: React.FC<Props> = (props): JSX.Element => {
    const {
        formConfig,
        getElement,
        onBlur,
        onChange,
        mobilePadding,
        tabletPadding,
    } = props;
    
    const validateFormElement = useValidateFormElement<CustomerContactPerson>();
    const getElementProps = (name: string) => validateFormElement(name as keyof CustomerContactPerson, formConfig);
    
    const LastNameInput = getElement('lastName', getElementProps('lastName'), { onBlur, onChange });
    const FirstNameInput = getElement('firstName', getElementProps('firstName'), { onBlur, onChange });
    const JobTitleInput = getElement('jobTitle', getElementProps('jobTitle'), { onBlur, onChange });
    const TelInput = getElement('tel', getElementProps('tel'), { onBlur, onChange });
    const AltTelInput = getElement('altTel', getElementProps('altTel'), { onBlur, onChange });
    const EmailInput = getElement('email', getElementProps('email'), { onBlur, onChange });
    
    return (
        <>
            <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                <div className='d-flex flex-row align-items-center'>
                    <span className='pr-3'>
                        <i className='custom-icon icon user' />
                    </span>
                    <h6>Contact Person</h6>
                </div>
                <hr className='default' />
                <div className='row'>
                    <div className={`col-lg-6 col-md-6 col-sm-12 pr-0 pl-0 ${tabletPadding} ${mobilePadding}`}>
                        {FirstNameInput}
                    </div>
                    <div className={`col-lg-6 col-md-6 col-sm-12 pr-0 pl-0 ${tabletPadding} ${mobilePadding}`}>
                        {LastNameInput}
                    </div>
                </div>
                {JobTitleInput}
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
            </div>
        </>
    );
};
