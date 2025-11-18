import { FormState } from '../../../types';
import { AddCustomer } from '../../../models';

export const customerTypeOptions = [
    { label: 'Business', value: 'business' },
    { label: 'individual', value: 'individual' }
];

export const customerFormConfig: FormState<AddCustomer> = {
    customerId: {
        error:'',
        value: '',
        label: 'ID/Passport No.',
        type: 'text',
        placeholder: '8000015200080'
    },
    customerType: {
        error:'',
        value: 'business',
        label: 'Customer Type',
        type: 'select',
        options: customerTypeOptions,
    },
    customerName: {
        error:'',
        value: '',
        label: 'Business Name',
        type: 'text',
        placeholder: 'Building Contractor',
        required: true,
    },
    registrationNo: {
        error:'',
        value: '',
        label: 'Registration No.',
        type: 'text',
        placeholder: '2000/000000/23',
        required: false,
    },
    vatNo: {
        error:'',
        value: '',
        label: 'VAT No.',
        type: 'text',
        placeholder: '4000010006',
        required: false,
    },
    tel: {
        error:'',
        value: '',
        label: 'Tel',
        type: 'text',
        placeholder: '0126784123',
        required: true,
    },
    altTel: {
        error:'',
        value: '',
        label: 'Alt. Tel',
        type: 'text',
        placeholder: '0829874321',
        required: false,
    },
    email: {
        error:'',
        value: '',
        label: 'Email',
        type: 'email',
        placeholder: 'you@somewhere.co.za',
        required: true,
    },
    web: {
        error:'',
        value: '',
        label: 'Website',
        type: 'text',
        placeholder: 'www.somewhere.co.za',
        required: false,
    },
    address: {
        error:'',
        value: '',
        label: 'Address',
        type: 'textarea',
        placeholder: '123 Main Street, City, Country',
        required: true,
        style: { minHeight: 50 }
    },
}
