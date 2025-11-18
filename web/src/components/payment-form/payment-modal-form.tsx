import React, { JSX } from 'react';
import { Form } from '../form';
import { ModalContainer, ModalBody, ModalFormFooter, ModalHeader } from '../../containers';
import { PaymentFormElements } from './payment-form-elements';
import {
    ButtonClickFn,
    FormInput,
    FormState,
    FormSubmitFn,
    InputChangeFn,
    InputFocusFn,
    ReactSelectFn,
} from '../../types';
import { Transaction } from '../../models';

type Props = {
    openModal: boolean;
    toggleModal: () => void;
    getElement: (name: any, props: FormInput<any>, handlers?: any) => JSX.Element;
    formConfig: FormState<Transaction>;
    formInvalid: boolean;
    onBlur?: InputFocusFn<HTMLInputElement, void>;
    onChange: InputChangeFn<HTMLInputElement, void>;
    onSelect: ReactSelectFn<void>;
    onSubmit: FormSubmitFn<void>;
}

export const PaymentModalForm: React.FC<Props> = (props): JSX.Element => {
    const { formConfig, formInvalid, getElement, openModal, onBlur, onChange, onSelect, onSubmit, toggleModal } = props;
    const onModalCloseButtonClick: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        toggleModal();
    };
    
    return (
        <ModalContainer show={openModal} closeModal={toggleModal}>
            <ModalHeader onClick={onModalCloseButtonClick}>
                <h5 className='mr-auto'>Payment</h5>
            </ModalHeader>
            <ModalBody>
                <Form onSubmit={onSubmit}>
                    <PaymentFormElements
                        getElement={getElement}
                        formConfig={formConfig}
                        onBlur={onBlur}
                        onChange={onChange}
                        onSelect={onSelect}
                    />
                    <ModalFormFooter onClick={onModalCloseButtonClick} disabled={formInvalid} />
                </Form>
            </ModalBody>
        </ModalContainer>
    );
};
