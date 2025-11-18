import React, { JSX } from 'react';
import { FormInput, FormState, InputChangeFn, InputFocusFn, ReactSelectFn } from '../../../types';
import { Workflow } from '../../../models';

type Props = {
    getElement: (name: any, props: FormInput<any>, handlers?: any) => JSX.Element;
    formConfig: FormState<Workflow>;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect?: ReactSelectFn<void>;
};

export const WorkflowForm: React.FC<Props> = ({ getElement, formConfig, onChange, onBlur, onSelect }) => {
    const LabourOptionSelector = getElement('labour', formConfig['labour'], { onSelect });
    const DeliveryOptionSelector = getElement('delivery', formConfig['delivery'], { onSelect });
    const WorkflowNameField = getElement('workflowName', formConfig['workflowName'], { onBlur, onChange });
    // return getElement('workflowName', formConfig['workflowName'], { onBlur, onChange });
    return (
        <>
            {WorkflowNameField}
            {DeliveryOptionSelector}
            {LabourOptionSelector}
        </>
    );
};
