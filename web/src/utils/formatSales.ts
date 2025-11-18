import { SaleList, UserRole } from '../models';

export const formatSales = (sales: SaleList[], userRole?: UserRole): SaleList[] => {
    return sales.map((sale) => {
        const { tasks } = sale;
        const sortedTasks = tasks.sort((a, b) => a.taskNo - b.taskNo);
        
        return { ...sale, tasks: sortedTasks };
    });
};
