import React, { JSX } from 'react';
import { FormInput, FormState, InputChangeFn, InputFocusFn } from '../../../types';
import { Workflow } from '../../../models';

type Props = {
    getElement: (name: any, props: FormInput<string>, handlers?: any) => JSX.Element;
    formConfig: FormState<Workflow>;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
};

export const WorkflowForm: React.FC<Props> = ({ getElement, formConfig, onChange, onBlur }) => {
    return getElement('workflowName', formConfig['workflowName'], { onBlur, onChange });
}
