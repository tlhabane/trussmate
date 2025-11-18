export interface TaskMonitor {
    taskId: string;
    username: string;
    escalationId: string;
    escalationTaskId: string;
    escalationType: string;
    escalationDays: number;
}
