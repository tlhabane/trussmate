import { Job } from './Job';
import { TaskStatus } from './TaskStatus';

export interface SaleTaskForm {
    saleId: string;
    saleTaskId: string;
    taskStatus: TaskStatus;
    taskCompletionDate: string;
    taskDays: number;
    taskFrequency: number;
    taskPayment: number;
    taskPaymentType: string;
    comments: string;
    job?: Job;
}
