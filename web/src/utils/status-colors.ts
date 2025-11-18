import { SaleStatus, TaskStatus } from '../models';

export const getStatusColor = (status: SaleStatus | TaskStatus) => {
    switch (status) {
        case (SaleStatus.COMPLETED || TaskStatus.COMPLETED):
            return 'success';
        case (SaleStatus.CANCELLED || TaskStatus.CANCELLED):
            return 'cancelled';
        case (SaleStatus.STARTED || TaskStatus.STARTED):
            return 'progress';
        case (SaleStatus.TENTATIVE || TaskStatus.TENTATIVE):
            return 'tentative';
        default:
            return 'pending';
    }
};
