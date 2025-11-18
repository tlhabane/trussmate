import { FormState } from '../../types';
import { Transaction } from '../../models';
import { transactionMethodOptions } from '../../static-data';

export const paymentFormConfig: FormState<Transaction> = {
    invoiceNo: {
        error: '',
        value: '',
        label: 'Invoice No.',
        type: 'text',
    },
    saleTaskId: {
        error: '',
        value: '',
        label: 'saleTaskId',
        type: 'text',
    },
    transactionId: {
        error: '',
        value: '',
        label: 'TransactionId',
        type: 'text',
    },
    transactionType: {
        error: '',
        value: 'payment',
        label: 'Transaction Type',
        type: 'select',
        options: [
            { label: 'Credit Memo', value: 'credit_memo' },
            { label: 'Debit Memo', value: 'debit_memo' },
            { label: 'Payment', value: 'payment' },
            { label: 'Refund', value: 'refund' },
        ],
        required: true,
    },
    transactionDate: {
        error: '',
        value: '',
        label: 'Payment Date',
        type: 'date',
    },
    transactionAmount: {
        error: '',
        value: 0,
        label: 'Amount',
        type: 'text',
        required: true,
    },
    transactionMethod: {
        error: '',
        value: 'bank_transfer',
        label: 'Payment Method',
        type: 'select',
        options: transactionMethodOptions,
    },
    transactionDesc: {
        error: '',
        value: '',
        label: 'Description',
        type: 'text',
        required: true,
    },/*,
    sendConfirmation: {
        error: '',
        value: 0,
        label: 'Send Payment Confirmation',
        type: 'select',
        options: yesNoOptions,
    },
    overrideAmount: {
        error: '',
        value: 0,
        label: 'Approve Without Payment',
        type: 'select',
        options: yesNoOptions,
    },*/
};
