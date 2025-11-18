import { ReactSelectSingleOption } from '../types';

export const taskActionOptions: ReactSelectSingleOption[] = [
    {
        label: 'None',
        value: '0',
    },
    {
        label: 'Upload Estimate',
        value: 'estimate',
    },
    {
        label: 'Send invoice',
        value: 'invoice',
    },
    {
        label: 'Send proforma invoice',
        value: 'proforma_invoice',
    },
    {
        label: 'Send quotation',
        value: 'quotation',
    },
    {
        label: 'Create a task',
        value: 'task',
    },
    {
        label: 'Charge penalty fee',
        value: 'penalty',
    },
];
