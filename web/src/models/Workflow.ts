export interface WorkflowTask {
    taskId: string;
    taskNo: number;
    triggerType: string;
    taskOptional: number;
    assignedTo: string;
    assignmentNote: string;
}

export interface Workflow {
    workflowId: string;
    workflowName: string;
    labour: number;
    delivery: number;
}

export interface WorkflowTaskList extends WorkflowTask {
    taskName: string;
    taskDescription: string;
    taskPayment: number;
    tackAction: string;
}

export interface WorkflowList extends Workflow {
    tasks: WorkflowTaskList[];
}
