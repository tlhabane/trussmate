import { TaskMonitor } from '../../models';
import { FormState } from '../../types';
import {
    dailyIntervalOptions,
    escalationTypeOptions,
} from '../../static-data';

export const taskMonitorFormConfig: FormState<TaskMonitor> = {
    taskId: {
        value: '',
        error: '',
        label: 'Task',
        type: 'select',
        placeholder: 'Select task to monitor',
        options: [],
        required: true,
    },
    escalationId: {
        value: '',
        error: '',
        label: 'escalationId',
        type: 'text',
    },
    escalationTaskId: {
        value: '',
        error: '',
        label: 'taskId',
        type: 'text',
    },
    escalationDays: {
        value: 0,
        error: '',
        label: 'Send Notification (in days)',
        type: 'select',
        options: dailyIntervalOptions,
        required: true,
    },
    escalationType: {
        value: 'progress',
        error: '',
        label: 'Notification Type',
        type: 'select',
        options: escalationTypeOptions,
        required: true,
    },
    username: {
        value: '',
        error: '',
        label: 'Notify',
        type: 'select',
        placeholder: 'Select notification recipient',
        options: [],
        required: true,
    },
};
