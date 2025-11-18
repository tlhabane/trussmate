import { SaleDocument } from './SaleDocument';
import { SaleTask } from './SaleTask';
import { TaskLog } from './TaskLog';

export interface SaleTaskList extends SaleTask {
    contactId: string;
    contactFirstName: string;
    contactLastName: string;
    customerId: string;
    customerName: string;
    documents?: SaleDocument[];
    taskLogs: TaskLog[];
    saleNo: string;
    invoiceNo: string;
    saleTotal: number;
    totalPaid: number;
    totalPayable: number;
    balanceDue: number;
    taskDate: string;
}

