import { Transaction } from './Transaction';

export interface TransactionList extends Transaction {
    saleNo: string;
    saleId: string;
    saleTaskId: string;
    invoiceBalance: number;
    transactionCancelled: number;
    customerId: string;
    customerName: string;
    customerEmail: string;
    contactId: string;
    firstName: string;
    lastName: string;
    contactEmail: string;
}
