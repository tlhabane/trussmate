import { Sale, SaleStatus } from '../../models';
import { FormState } from '../../types';
import { yesNoOptions } from '../../static-data';

export const saleFormConfig: FormState<Sale> = {
    saleId: {
        error: '',
        value: '',
        label: 'saleId',
        type: 'text',
    },
    saleStatus: {
        error: '',
        value: SaleStatus.PENDING,
        label: 'salStatus',
        type: 'text',
    },
    workflowId: {
        error: '',
        value: '',
        label: 'Sale Process',
        type: 'select',
        options: [],
        placeholder: 'Select sale process',
        required: true,
    },
    customerId: {
        error: '',
        value: '',
        label: 'Customer',
        type: 'select',
        options: [],
        placeholder: 'Building Contractor',
        required: true,
    },
    contactId: {
        error: '',
        value: '',
        label: 'Contact Person',
        type: 'select',
        options: [],
        placeholder: 'Sibongile Mazibuko',
    },
    billingAddressId: {
        error: '',
        value: '',
        label: 'Address',
        type: 'select',
        options: [],
        placeholder: '1234 Main Street',
        required: true,
    },
    labour: {
        error: '',
        value: 0,
        label: 'Installation Required',
        type: 'select',
        options: yesNoOptions,
        placeholder: 'Erect trusses (Yes/no)',
    },
    delivery: {
        error: '',
        value: 0,
        label: 'Include Delivery',
        type: 'select',
        options: yesNoOptions,
        placeholder: 'Deliver goods to customer',
    },
    deliveryAddressId: {
        error: '',
        value: '',
        label: 'Delivery Address',
        type: 'select',
        options: [],
        placeholder: '1234 Main Street',
    },
};
