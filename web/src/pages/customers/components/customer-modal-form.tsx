import React, { JSX } from 'react';
import { CustomerFormElements, Form } from '../../../components';
import { ModalContainer, ModalBody, ModalFormFooter, ModalHeader } from '../../../containers';
import { Customer } from '../../../models';
import {
    ButtonClickFn,
    FormInput,
    FormState,
    FormSubmitFn,
    InputChangeFn,
    InputFocusFn,
    ReactSelectFn,
} from '../../../types';

type Props = {
    openModal: boolean;
    toggleModal: () => void;
    getElement: (name: any, props: FormInput<Customer>, handlers?: any) => JSX.Element;
    formConfig: FormState<Customer>;
    formInvalid: boolean;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect: ReactSelectFn<void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSubmit: FormSubmitFn<void>;
    mobilePadding: string;
    tabletPadding: string;
};

export const CustomerModalForm: React.FC<Props> = (props): JSX.Element => {
    const {
        formConfig,
        formInvalid,
        getElement,
        onBlur,
        onChange,
        onSelect,
        onSubmit,
        mobilePadding,
        tabletPadding,
        openModal,
        toggleModal,
    } = props;
    
    const dismissModal: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        toggleModal();
    };
    
    return (
        <ModalContainer show={openModal} closeModal={toggleModal}>
            <ModalHeader onClick={dismissModal}>
                <h5 className='mr-auto'>Update Customer</h5>
            </ModalHeader>
            <ModalBody>
                <Form onSubmit={onSubmit}>
                    <CustomerFormElements
                        getElement={getElement}
                        formConfig={formConfig}
                        onBlur={onBlur}
                        onChange={onChange}
                        onSelect={onSelect}
                        mobilePadding={mobilePadding}
                        tabletPadding={tabletPadding}
                        partialForm
                    />
                    <ModalFormFooter onClick={dismissModal} disabled={formInvalid} />
                </Form>
            </ModalBody>
        </ModalContainer>
    );
};
