import { Task } from './Task';
import { TaskStatus } from './TaskStatus';

export interface SaleTask extends Task {
    saleTaskId: string;
    saleId: string;
    taskNo: number;
    taskCompletionDate: string;
    taskStatus: TaskStatus;
    triggerType: string;
    taskOptional: number;
    assignedTo: string;
    assignmentNote: string;
    taskEnabled?: boolean;
}
