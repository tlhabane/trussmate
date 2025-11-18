import { Address } from './Address';
import { Customer } from './Customer';
import { CustomerContactPerson } from './ContactPerson';

export interface CustomerList extends Customer {
    contacts: CustomerContactPerson[];
    addresses: Address[];
}
