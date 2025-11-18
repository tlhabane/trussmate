import React, { JSX } from 'react';
import { AddressFormElements, Form } from '../../../components';
import { ModalContainer, ModalBody, ModalFormFooter, ModalHeader } from '../../../containers';
import { Address } from '../../../models';
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
    getElement: (name: any, props: FormInput<Address>, handlers?: any) => JSX.Element;
    formConfig: FormState<Address>;
    formInvalid: boolean;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect: ReactSelectFn<void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSubmit: FormSubmitFn<void>;
};

export const AddressModalForm: React.FC<Props> = (props): JSX.Element => {
    const {
        formConfig,
        formInvalid,
        getElement,
        onBlur,
        onChange,
        onSelect,
        onSubmit,
        openModal,
        toggleModal,
    } = props;
    
    const dismissModal: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        toggleModal();
    };
    
    const newAddress = formConfig.addressId.value.trim() === '';
    
    return (
        <ModalContainer show={openModal} closeModal={toggleModal}>
            <ModalHeader onClick={dismissModal}>
                <h5 className='mr-auto'>{newAddress ? 'Add' : 'Update'} Address</h5>
            </ModalHeader>
            <ModalBody>
                <Form onSubmit={onSubmit}>
                    <AddressFormElements
                        getElement={getElement}
                        formConfig={formConfig}
                        onBlur={onBlur}
                        onChange={onChange}
                        onSelect={onSelect}
                    />
                    <ModalFormFooter onClick={dismissModal} disabled={formInvalid} />
                </Form>
            </ModalBody>
        </ModalContainer>
    );
};
