import { SaleList, SaleTaskList, UserRole } from '../models';

export const formatSales = (sales: SaleList[], userRole?: UserRole): SaleList[] => {
    return sales.map((sale) => {
        const { tasks } = sale;
        const sortedTasks = tasks.sort((a, b) => a.taskNo - b.taskNo);
        /*const updatedTasks: SaleTaskList[] = sortedTasks.map((task, index) => {
            const previousTask = tasks[index - 1];
            if (previousTask) {
                return {
                    ...task,
                    documents: [...(task?.documents || []), ...documents],
                    taskEnabled: previousTask.taskStatus === 'completed',
                };
            }
            
            return { ...task, documents: [...(task?.documents || []), ...documents], taskEnabled: true };
        });*/
        
        const adminUser = userRole?.toString() === 'admin' || userRole?.toString() === 'super_admin';
        const assignedTasks = adminUser
            ? sortedTasks
            : sortedTasks.filter((t) => t.assignedTo === userRole?.toString());
        
        return { ...sale, tasks: assignedTasks };
    });
};

export const formatTasks = (sales: SaleList[], userRole?: UserRole): SaleTaskList[] => {
    return formatSales(sales, userRole).map(({ tasks }) => tasks).flat();
};
