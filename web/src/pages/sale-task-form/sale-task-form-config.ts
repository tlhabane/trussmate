import { SaleTaskForm, TaskStatus } from '../../models';
import { FormState } from '../../types';
import {
    calendarDayOptions,
    dailyIntervalOptions,
    paymentTypeOptions,
    statusOptions,
    taskPaymentOptions,
} from '../../static-data';

const taskStatusOptions = statusOptions.filter((option) => (
    option.value.toString().toLowerCase().trim() !== 'completed'
));/*statusOptions.map((option) => {
    const completeOption = option.value.toString().toLowerCase().trim() === 'completed';
    if (completeOption) {
        return { ...option, enabled: false };
    }
    return { ...option, enabled: true };
})*/

export const saleTaskFormConfig: FormState<SaleTaskForm> = {
    saleId: {
        error: '',
        value: '',
        label: 'saleId',
        type: 'text',
    },
    saleTaskId: {
        error: '',
        value: '',
        label: 'saleTaskId',
        type: 'text',
    },
    taskStatus: {
        error: '',
        value: TaskStatus.PENDING,
        label: 'Task Status',
        type: 'select',
        options: taskStatusOptions,
        required: true,
    },
    taskCompletionDate: {
        value: '',
        error: '',
        label: 'Est. completion date',
        type: 'date',
        required: true,
    },
    taskDays: {
        value: 0,
        error: '',
        label: 'Task due (in days)',
        type: 'select',
        options: dailyIntervalOptions,
    },
    taskFrequency: {
        value: 0,
        error: '',
        label: 'Repeat task every (in days)',
        type: 'select',
        options: [{ value: 0, label: 'Don\'t repeat' }, ...calendarDayOptions],
    },
    taskPayment: {
        value: 0,
        error: '',
        label: 'Payment amount',
        type: 'select',
        options: taskPaymentOptions,
    },
    taskPaymentType: {
        value: '0',
        error: '',
        label: 'Payment required',
        type: 'select',
        options: paymentTypeOptions,
    },
    comments: {
        value: '',
        error: '',
        label: 'Reason for changes',
        type: 'textarea',
        style: { minHeight: 80 },
        required: true,
    },
};
