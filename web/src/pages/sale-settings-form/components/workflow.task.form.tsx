import React, { JSX } from 'react';
import {
    FormInput,
    FormState,
    InputChangeFn,
    InputFocusFn,
    ReactSelectFn
} from '../../../types';

type Props = {
    getElement: (name: any, props: FormInput<string>, handlers?: any) => JSX.Element;
    formConfig: FormState<any>;
    formInvalid: boolean;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect: ReactSelectFn<void>;
};

export const WorkflowTaskForm: React.FC<Props> = (props) => {
    const { formConfig, getElement, onBlur, onChange, onSelect } = props;
    
    const getElementProps = (name?: any) => {
        const props = formConfig[name];
        if (!props) {
            throw new Error(`Form config does not contain element with name: ${name}`);
        }
        return props;
    };
    
    const TaskSelect = getElement('taskId', getElementProps('taskId'), { onSelect });
    const TaskTriggerSelect = getElement('triggerType', getElementProps('triggerType'), { onSelect });
    const TaskAssignmentSelect = getElement('assignedTo', getElementProps('assignedTo'), { onSelect });
    const AssignmentNoteInput = getElement('assignmentNote', getElementProps('assignmentNote'), { onBlur, onChange });
    
    return (
        <>
            {TaskSelect}
            {TaskTriggerSelect}
            {TaskAssignmentSelect}
            {AssignmentNoteInput}
        </>
    )
};
