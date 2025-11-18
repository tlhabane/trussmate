import React, { JSX } from 'react';
import { ContactFormElements, Form } from '../../../components';
import { ModalContainer, ModalBody, ModalFormFooter, ModalHeader } from '../../../containers';
import { CustomerContactPerson } from '../../../models';
import {
    ButtonClickFn,
    FormInput,
    FormState,
    FormSubmitFn,
    InputChangeFn,
    InputFocusFn,
} from '../../../types';

type Props = {
    openModal: boolean;
    toggleModal: () => void;
    getElement: (name: any, props: FormInput<CustomerContactPerson>, handlers?: any) => JSX.Element;
    formConfig: FormState<CustomerContactPerson>;
    formInvalid: boolean;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSubmit: FormSubmitFn<void>;
    mobilePadding: string;
    tabletPadding: string;
};

export const ContactModalForm: React.FC<Props> = (props): JSX.Element => {
    const {
        formConfig,
        formInvalid,
        getElement,
        onBlur,
        onChange,
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
    
    const newContact = formConfig.contactId.value.trim() === '';
    
    return (
        <ModalContainer show={openModal} closeModal={toggleModal}>
            <ModalHeader onClick={dismissModal}>
                <h5 className='mr-auto'>{newContact ? 'Add' : 'Update'} Contact Person</h5>
            </ModalHeader>
            <ModalBody>
                <Form onSubmit={onSubmit}>
                    <ContactFormElements
                        getElement={getElement}
                        formConfig={formConfig}
                        onBlur={onBlur}
                        onChange={onChange}
                        mobilePadding={mobilePadding}
                        tabletPadding={tabletPadding}
                    />
                    <ModalFormFooter onClick={dismissModal} disabled={formInvalid} />
                </Form>
            </ModalBody>
        </ModalContainer>
    );
};
