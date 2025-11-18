import { Task } from '../../models';
import { FormState } from '../../types';
import {
    calendarDayOptions,
    dailyIntervalOptions,
    paymentTypeOptions,
    taskActionOptions,
    taskPaymentOptions,
    yesNoOptions,
} from '../../static-data';

export const taskFormConfig: FormState<Task> = {
    taskId: {
        value: '',
        error: '',
        label: 'taskId',
        type: 'text',
    },
    taskName: {
        value: '',
        error: '',
        label: 'Task Name',
        required: true,
        type: 'text',
    },
    taskDescription: {
        value: '',
        error: '',
        label: 'Description',
        type: 'textarea',
        style: { minHeight: 50 },
    },
    taskPayment: {
        value: 0,
        error: '',
        label: 'Payment Amount',
        type: 'select',
        options: taskPaymentOptions,
    },
    taskDocument: {
        value: 0,
        error: '',
        label: 'Document(s) Required',
        type: 'select',
        options: yesNoOptions,
    },
    taskDays: {
        value: 0,
        error: '',
        label: 'Task Due (in days)',
        type: 'select',
        options: dailyIntervalOptions,
    },
    taskPaymentType: {
        value: '0',
        error: '',
        label: 'Payment Required',
        type: 'select',
        options: paymentTypeOptions,
    },
    taskFrequency: {
        value: 0,
        error: '',
        label: 'Repeat Every',
        type: 'select',
        options: [{ value: 0, label: 'Don\'t repeat' }, ...calendarDayOptions],
    },
    taskAction: {
        value: '',
        error: '',
        label: 'Action',
        type: 'select',
        options: taskActionOptions,
    },
};
