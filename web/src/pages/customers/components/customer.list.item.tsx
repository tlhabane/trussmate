import React, { JSX } from 'react';
import { CustomerOptionsMenu } from './customer-options-menu';
import { Button, EmptyListContainer, ListItemContainer, ListItemHeader, Tabs, Tab } from '../../../components';
import { capitalizeFirstLetter } from '../../../utils';
import { ButtonClickFn, HTMLElementClickFn } from '../../../types';
import { CustomerList, CustomerType } from '../../../models';

type Props = {
    customer: CustomerList;
    addAddress: HTMLElementClickFn<void>;
    deleteAddress: ButtonClickFn<void>;
    updateAddress: ButtonClickFn<void>;
    addContact: HTMLElementClickFn<void>;
    deleteContact: ButtonClickFn<void>;
    updateContact: ButtonClickFn<void>;
    deleteCustomer: HTMLElementClickFn<void>;
    updateCustomer: ButtonClickFn<void>;
    toggleListItem: ButtonClickFn<void>;
    viewCustomerDetail: boolean;
};

const customerListItemTabs: Tab[] = [
    {
        id: `addressListTab`,
        title: 'Address',
        active: true,
    },
    {
        id: `contactListTab`,
        title: 'Contact Persons',
    },
];

export const CustomerListItem: React.FC<Props> = (props): JSX.Element => {
    const {
        customer,
        addAddress,
        deleteAddress,
        updateAddress,
        addContact,
        deleteContact,
        updateContact,
        deleteCustomer,
        updateCustomer,
        toggleListItem,
        viewCustomerDetail,
    } = props;
    const { addresses, contacts, customerId, customerName, customerType, tel, email } = customer;
    const businessCustomer = customerType === CustomerType.BUSINESS;
    const customerIcon = businessCustomer ? 'users' : 'user';
    const activeClass = viewCustomerDetail ? 'active' : '';
    
    customerListItemTabs.push();
    
    return (
        <>
            <ListItemContainer className={`striped ${activeClass}`}>
                <ListItemHeader>
                    <div className='col-3 title'>
                        <div>
                            <i className={`custom-icon icon ${customerIcon}`} />
                        </div>
                        <div>
                            <h2>
                                <small
                                    className='font-weight-bold text-wrap'>{capitalizeFirstLetter(`${customerName}`)}</small>
                                <small className='text-wrap small'>
                                    Name
                                </small>
                            </h2>
                        </div>
                    </div>
                    <div className='col-3 title'>
                        <div>
                            <i className='custom-icon icon tel' />
                        </div>
                        <div>
                            <h2>
                                <small className='font-weight-bold text-wrap'>{tel}</small>
                                <small className='text-wrap small'>
                                    Tel
                                </small>
                            </h2>
                        </div>
                    </div>
                    <div className='col-3 title'>
                        <div>
                            <i className='custom-icon icon mail' />
                        </div>
                        <div>
                            <h2>
                                <small className='font-weight-bold text-wrap'>{email}</small>
                                <small className='text-wrap small'>
                                    Email
                                </small>
                            </h2>
                        </div>
                    </div>
                    <div className='col-3 action-col'>
                        <Button
                            className='btn-link no-text tooltip-top'
                            data-tooltip='Update'
                            data-customer={customerId}
                            onClick={updateCustomer}
                        >
                            <i className='custom-icon icon icon-only edit' />
                        </Button>
                        <Button
                            onClick={toggleListItem}
                            className='btn-link no-text tooltip-top'
                            data-toggle={customerId}
                            data-tooltip={`${viewCustomerDetail ? 'Close' : 'More'}`}
                        >
                            <i className={`custom-icon icon icon-only ${viewCustomerDetail ? 'close' : 'chevron-down'}`} />
                        </Button>
                        <div className='v-divider' />
                        <CustomerOptionsMenu
                            addAddress={addAddress}
                            addContact={addContact}
                            businessCustomer={businessCustomer}
                            customerId={customerId}
                            deleteCustomer={deleteCustomer}
                        />
                    </div>
                </ListItemHeader>
            </ListItemContainer>
            {viewCustomerDetail && (
                <div className='form-group form-group-default bg-transparent px-0 pt-2 pb-0' style={{ marginTop: -5 }}>
                    <Tabs
                        tabs={customerListItemTabs}
                        className='nav-tabs nav-tabs-simple nav-tabs-info justify-content-center'
                    />
                    <div className='tab-content px-0 pb-0'>
                        <div className='tab-pane fade show active' id='addressListTab'>
                            {addresses.map(({ addressId, billingAddress, fullAddress }) => (
                                <ListItemContainer className='striped' key={addressId}>
                                    <ListItemHeader>
                                        <div className='col-9 title'>
                                            <div>
                                                <i className='custom-icon icon map-pin' />
                                            </div>
                                            <div>
                                                <h2>
                                                    <small className='font-weight-bold text-wrap'>{fullAddress}</small>
                                                    <small className='text-wrap small'>
                                                        Address
                                                    </small>
                                                </h2>
                                            </div>
                                        </div>
                                        <div className='col-3 action-col'>
                                            <Button
                                                className='btn-link no-text tooltip-top'
                                                data-tooltip='Billing address'
                                                data-address={addressId}
                                                onClick={updateCustomer}
                                                disabled={!!billingAddress}
                                            >
                                                <i className='custom-icon icon icon-only check' />
                                            </Button>
                                            <Button
                                                className='btn-link no-text tooltip-top'
                                                data-tooltip='Update'
                                                data-customer={customerId}
                                                data-address={addressId}
                                                onClick={updateAddress}
                                            >
                                                <i className='custom-icon icon icon-only edit' />
                                            </Button>
                                            <div className='v-divider' />
                                            <Button
                                                className='btn-link delete-btn no-text tooltip-top'
                                                data-tooltip='Delete'
                                                data-customer={customerId}
                                                data-address={addressId}
                                                onClick={deleteAddress}
                                            >
                                                <i className='custom-icon icon icon-only trash' />
                                            </Button>
                                        </div>
                                    </ListItemHeader>
                                </ListItemContainer>
                            ))}
                        </div>
                        <div className='tab-pane fade' id='contactListTab'>
                            {contacts.length === 0 && (
                                <div className='row p-5'>
                                    <div className='col-md-6 offset-md-3'>
                                        <EmptyListContainer>
                                            <i className='custom-icon icon users' style={{ width: 48, height: 48 }} />
                                            <p className='hint-text text-center mt-2'>
                                                No contact persons available.
                                                <br />
                                                <em>Contact persons are only available, for business customers.</em>
                                            </p>
                                        </EmptyListContainer>
                                    </div>
                                </div>
                            )}
                            {contacts.map((contact) => (
                                <ListItemContainer className='striped' key={contact.contactId}>
                                    <ListItemHeader>
                                        <div className='col-3 title'>
                                            <div>
                                                <i className={`custom-icon icon user`} />
                                            </div>
                                            <div>
                                                <h2>
                                                    <small className='font-weight-bold text-wrap'>
                                                        {capitalizeFirstLetter(`${contact.firstName} ${contact.lastName}`)}
                                                    </small>
                                                    <small className='text-wrap small'>
                                                        Name
                                                    </small>
                                                </h2>
                                            </div>
                                        </div>
                                        <div className='col-3 title'>
                                            <div>
                                                <i className='custom-icon icon tel' />
                                            </div>
                                            <div>
                                                <h2>
                                                    <small className='font-weight-bold text-wrap'>{contact.tel}</small>
                                                    <small className='text-wrap small'>
                                                        {contact.altTel || 'Tel'}
                                                    </small>
                                                </h2>
                                            </div>
                                        </div>
                                        <div className='col-3 title'>
                                            <div>
                                                <i className='custom-icon icon mail' />
                                            </div>
                                            <div>
                                                <h2>
                                                    <small
                                                        className='font-weight-bold text-wrap'>{contact.email}</small>
                                                    <small className='text-wrap small'>
                                                        Email
                                                    </small>
                                                </h2>
                                            </div>
                                        </div>
                                        <div className='col-3 action-col'>
                                            <Button
                                                className='btn-link no-text tooltip-top'
                                                data-tooltip='Update'
                                                data-customer={contact.customerId}
                                                data-contact={contact.contactId}
                                                onClick={updateContact}
                                            >
                                                <i className='custom-icon icon icon-only edit' />
                                            </Button>
                                            <div className='v-divider' />
                                            <Button
                                                className='btn-link delete-btn no-text tooltip-top'
                                                data-tooltip='Delete'
                                                data-customer={contact.customerId}
                                                data-contact={contact.contactId}
                                                onClick={deleteContact}
                                            >
                                                <i className='custom-icon icon icon-only trash' />
                                            </Button>
                                        </div>
                                    </ListItemHeader>
                                </ListItemContainer>
                            ))}
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};
