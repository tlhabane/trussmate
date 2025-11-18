import { FormState } from '../../../types';
import { Workflow } from '../../../models';

export const workflowFormConfig: FormState<Workflow> = {
    workflowId: {
        error:'',
        value: '',
        label: 'workflowId',
        type: 'text'
    },
    workflowName: {
        error:'',
        value: '',
        label: 'Workflow Name',
        type: 'text',
        required: true
    },
};
