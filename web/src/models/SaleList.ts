import { Sale } from './Sale';
import { SaleDocument } from './SaleDocument';
import { SaleStatus } from './SaleStatus';
import { SaleTaskList } from './SaleTaskList';
import { ContactPerson } from './ContactPerson';
import { Customer } from './Customer';
import { Address } from './Address';
import { Job } from './Job';

export interface SaleList extends Sale {
    saleNo: number;
    saleDate: string;
    saleStatus: SaleStatus;
    saleTotal: number;
    customer: Customer;
    contact: ContactPerson;
    documents: SaleDocument[];
    jobs: Job[];
    tasks: SaleTaskList[];
    billingAddress: Address;
    deliveryAddress: Address;
}
