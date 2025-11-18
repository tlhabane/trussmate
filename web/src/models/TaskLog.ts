import { TaskStatus } from './TaskStatus';

export interface TaskLog {
    taskId: string;
    taskNo: string;
    taskName: string;
    taskDescription: string;
    taskStatus: TaskStatus;
    taskPayment: number;
    taskPaymentType: string;
    taskDays: number;
    taskCompletionDate: string;
    taskFrequency: number;
    firstName: string;
    lastName: string;
    logDate: string;
    comments: string;
}
