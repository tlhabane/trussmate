import { ReactSelectSingleOption } from '../types';

const defaultReasons = [
    'Incorrect Payment or Charge',
    'Cancelled Order',
    'Customer Deposit',
    'Discount',
    'Excess Payment or Charge',
    'Incomplete Service',
    'Invoice Error (i.e. incorrect invoice)',
    'Loyalty Reward Credit',
    'Referral Credit',
    'Refund to Account Credit',
];

export const creditMemoOptions: ReactSelectSingleOption[] = defaultReasons.map((reason) => ({
    label: reason,
    value: reason,
}));
