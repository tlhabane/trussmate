import React, { JSX } from 'react';
import { Dropdown } from 'react-bootstrap';
import { DropdownMenu, DropdownToggle } from '../../../components';
import { HTMLElementClickFn } from '../../../types';

type Props = {
    addAddress: HTMLElementClickFn<void>;
    addContact: HTMLElementClickFn<void>;
    businessCustomer: boolean;
    customerId: string;
    deleteCustomer: HTMLElementClickFn<void>;
};

export const CustomerOptionsMenu: React.FC<Props> = (props): JSX.Element => {
    const { addAddress, addContact, businessCustomer, customerId, deleteCustomer } = props;
    
    const smallIconSize = { style: { width: 16, height: 16 } };
    const smallWhiteIcon = {
        style: {
            ...smallIconSize.style,
            backgroundColor: '#fff',
        },
    };
    
    return (
        <Dropdown className='mr-1'>
            <Dropdown.Toggle
                as={DropdownToggle as React.ElementType}
                className='btn btn-link bg-transparent border-0 no-text profile-dropdown-toggle'
            />
            <Dropdown.Menu className='profile-dropdown' align='end' as={DropdownMenu as React.ElementType}>
                <Dropdown.Item
                    eventKey='address'
                    href='#'
                    data-customer={customerId}
                    onClick={addAddress}
                >
                    <span>Add Address</span>
                    <i className='custom-icon icon map-pin' {...smallIconSize} />
                </Dropdown.Item>
                <Dropdown.Item
                    eventKey='contact'
                    href='#'
                    data-customer={customerId}
                    onClick={addContact}
                    disabled={!businessCustomer}
                >
                    <span>Add Contact Person</span>
                    <i className='custom-icon icon user' {...smallIconSize} />
                </Dropdown.Item>
                <Dropdown.Divider />
                <Dropdown.Item
                    className='bg-danger text-white'
                    eventKey='delete'
                    href='#'
                    data-customer={customerId}
                    onClick={deleteCustomer}
                >
                    <span>Delete Customer</span>
                    <i className='custom-icon icon trash' {...smallWhiteIcon} />
                </Dropdown.Item>
            </Dropdown.Menu>
        </Dropdown>
    );
};
