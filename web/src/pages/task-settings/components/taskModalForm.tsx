import React, { JSX } from 'react';
import { Form } from '../../../components';
import { ModalContainer, ModalBody, ModalFormFooter, ModalHeader } from '../../../containers';
import { TaskFormElements } from './taskFormElements';
import { ButtonClickFn, FormInput, FormState, FormSubmitFn, InputChangeFn, InputFocusFn, ReactSelectFn } from '../../../types';
import { Task } from '../../../models';

type Props = {
    openModal: boolean;
    toggleModal: () => void;
    getElement: (name: any, props: FormInput<any>, handlers?: any) => JSX.Element;
    formConfig: FormState<Task>;
    formInvalid: boolean;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect: ReactSelectFn<void>;
    onSubmit: FormSubmitFn<void>;
};

export const TaskModalForm: React.FC<Props> = (props) => {
    const { formConfig, formInvalid, getElement, openModal, onBlur, onChange, onSelect, onSubmit, toggleModal } = props;
    const onModalCloseButtonClick: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        toggleModal();
    };
    
    const updateTask = formConfig.taskId.value.trim() !== '';
    return (
        <ModalContainer show={openModal} closeModal={toggleModal}>
            <ModalHeader onClick={onModalCloseButtonClick}>
                <h5 className="mr-auto">{`${updateTask ? 'Update' : 'Add'} Task`}</h5>
            </ModalHeader>
            <ModalBody>
                <Form onSubmit={onSubmit}>
                    <TaskFormElements
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
    )
}
