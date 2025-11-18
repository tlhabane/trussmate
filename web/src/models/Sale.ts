import { SaleStatus } from './SaleStatus';

export interface Sale {
    saleId: string;
    saleStatus: SaleStatus;
    customerId: string;
    contactId: string;
    billingAddressId: string;
    deliveryAddressId: string;
    labour: number;
    delivery: number;
    workflowId: string;
}
