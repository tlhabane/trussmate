export interface Transaction {
    invoiceNo: string;
    saleTaskId: string;
    transactionId: string;
    transactionAmount: number;
    transactionDate: string; // ISO date string
    transactionType: 'credit_memo' | 'debit_memo' | 'payment' | 'refund' | string;
    transactionMethod: 'credit_card' | 'bank_transfer' | 'cash' | 'mobile_payment' | 'other' | string;
    transactionDesc: string;
}
