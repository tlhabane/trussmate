import { FormState } from '../../../types';
import { Workflow } from '../../../models';
import { yesNoOptions } from '../../../static-data';

export const workflowFormConfig: FormState<Workflow> = {
    workflowId: {
        error: '',
        value: '',
        label: 'workflowId',
        type: 'text',
    },
    workflowName: {
        error: '',
        value: '',
        label: 'Workflow Name',
        type: 'text',
        required: true,
    },
    labour: {
        error: '',
        value: 0,
        label: 'Includes Installation',
        type: 'select',
        options: yesNoOptions,
        placeholder: 'Erect trusses (Yes/no)',
    },
    delivery: {
        error: '',
        value: 0,
        label: 'Includes Delivery',
        type: 'select',
        options: yesNoOptions,
        placeholder: 'Deliver goods to customer',
    },
};
