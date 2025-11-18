import { ReactSelectSingleOption } from '../types';

const defaultReasons = [
    'Incorrect Payment or Charge',
    'Cancelled Order',
    'Excess Payment or Charge',
    'Incomplete Service',
    'Invoice Error (i.e. incorrect invoice)',
    'Loyalty Reward Credit',
    'Refund to Account Credit',
    'Penalty or Fine',
];

export const debitMemoOptions: ReactSelectSingleOption[] = defaultReasons.map((reason) => ({
    label: reason,
    value: reason,
}));
