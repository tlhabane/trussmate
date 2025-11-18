import React, { JSX } from 'react';
import { Form } from '../../../components';
import { ModalContainer, ModalBody, ModalFormFooter, ModalHeader } from '../../../containers';
import { TaskMonitorFormElements } from './task-monitor-form-elements';
import {
    ButtonClickFn,
    FormInput,
    FormState,
    FormSubmitFn,
    InputChangeFn,
    InputFocusFn,
    ReactSelectFn,
} from '../../../types';
import { Task, TaskMonitor } from '../../../models';

type Props = {
    openModal: boolean;
    toggleModal: () => void;
    getElement: (name: any, props: FormInput<TaskMonitor>, handlers?: any) => JSX.Element;
    formConfig: FormState<TaskMonitor>;
    formInvalid: boolean;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect: ReactSelectFn<void>;
    onSubmit: FormSubmitFn<void>;
    tasks: Task[];
};

export const TaskMonitorModalForm: React.FC<Props> = (props) => {
    const {
        formConfig,
        formInvalid,
        getElement,
        openModal,
        onBlur,
        onChange,
        onSelect,
        onSubmit,
        tasks,
        toggleModal,
    } = props;
    
    const onModalCloseButtonClick: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        toggleModal();
    };
    
    const { escalationId, taskId } = formConfig;
    const selectedTask = tasks.find((task) => task.taskId === taskId.value);
    const updateTask = escalationId.value.trim() !== '';
    
    return (
        <ModalContainer show={openModal} closeModal={toggleModal}>
            <ModalHeader onClick={onModalCloseButtonClick}>
                <h5 className='mr-auto'>{`${updateTask ? 'Update' : 'Add'} Task Monitor`}</h5>
            </ModalHeader>
            <ModalBody>
                <Form onSubmit={onSubmit}>
                    <TaskMonitorFormElements
                        getElement={getElement}
                        formConfig={formConfig}
                        onBlur={onBlur}
                        onChange={onChange}
                        onSelect={onSelect}
                        task={selectedTask}
                    />
                    <ModalFormFooter onClick={onModalCloseButtonClick} disabled={formInvalid} />
                </Form>
            </ModalBody>
        </ModalContainer>
    );
};
