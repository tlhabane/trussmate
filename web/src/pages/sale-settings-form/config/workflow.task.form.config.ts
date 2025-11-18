import { FormState } from '../../../types';
import { WorkflowTask } from '../../../models';
import { userRoleOptions, workflowTriggerOptions } from '../../../static-data';

export const workflowTaskFormElementsConfig = {
    taskId: {
        error: '',
        value: '',
        label: 'Select Task',
        type: 'select',
        options: [],
        required: true,
    },
    taskNo: {
        error: '',
        value: 0,
        label: 'taskNo',
        type: 'text',
    },
    taskOptional: {
        error: '',
        value: 0,
        label: 'taskOptional',
        type: 'text',
    },
    triggerType: {
        error: '',
        value: '',
        label: 'Start Task',
        type: 'select',
        options: workflowTriggerOptions,
    },
    assignedTo: {
        error: '',
        value: '',
        label: 'Assign task to',
        type: 'select',
        options: userRoleOptions,
        required: true,
    },
    assignmentNote: {
        error: '',
        value: '',
        label: 'Note',
        type: 'textarea',
        style: { height: 80 },
        required: true,
    },
};

export const workflowTaskFormConfig: FormState<WorkflowTask> = { ...workflowTaskFormElementsConfig };


