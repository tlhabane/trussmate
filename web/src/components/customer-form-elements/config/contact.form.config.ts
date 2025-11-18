import { FormState } from '../../../types';
import { CustomerContactPerson } from '../../../models';

export const contactFormConfig: FormState<CustomerContactPerson> = {
    customerId: {
        error:'',
        value: '',
        label: 'ID/Passport No.',
        type: 'text',
        placeholder: '8000015200080'
    },
    contactId: {
        error:'',
        value: '',
        label: 'ID/Passport No.',
        type: 'text',
        placeholder: '8000015200080'
    },
    firstName: {
        error:'',
        value: '',
        label: 'First Name',
        type: 'text',
        placeholder: 'Sibongile',
        required: true,
    },
    lastName: {
        error:'',
        value: '',
        label: 'Surname',
        type: 'text',
        placeholder: 'Mazibuko',
        required: false,
    },
    jobTitle: {
        error:'',
        value: '',
        label: 'Job Title',
        type: 'text',
        placeholder: 'Manager',
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
    }
};
