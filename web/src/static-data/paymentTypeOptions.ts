import { ReactSelectSingleOption } from '../types';

export const paymentTypeOptions: ReactSelectSingleOption[] = [
    {
        label: 'None',
        value: '0',
    },
    {
        label: 'Fixed Amount',
        value: 'fixed',
    },
    {
        label: 'Percentage (%)',
        value: 'percentage',
    },
];
