import { FormState } from '../../types';
import { BankAccount } from '../../models';
import { bankOptions } from '../../static-data';

export const bankAccountFormConfig: FormState<BankAccount> = {
    bankId: {
        error: '',
        value: '',
        label: 'bankId',
        type: 'text',
    },
    bankName: {
        error: '',
        value: '',
        label: 'Bank Name',
        type: 'select',
        options: bankOptions,
        required: true,
    },
    bankAccountName: {
        error: '',
        value: '',
        label: 'Account name',
        type: 'text',
        required: true,
    },
    bankAccountNo: {
        error: '',
        value: '',
        label: 'Account no',
        type: 'text',
        required: true,
    },
    branchCode: {
        error: '',
        value: '',
        label: 'Branch code',
        type: 'text',
    },
};
