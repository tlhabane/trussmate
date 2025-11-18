import React, { JSX } from 'react';
import { Dropdown } from 'react-bootstrap';
import { DropdownMenu, DropdownToggle } from '../../../components';
import { capitalizeFirstLetter } from '../../../utils';
import { AuthorisedUser } from '../../../models';
import { HTMLElementClickFn } from '../../../types';

type Props = {
    user: AuthorisedUser;
    logout: HTMLElementClickFn<void>;
};

export const UserOptions: React.FC<Props> = (props): JSX.Element => {
    const { user, logout } = props;
    const smallIconSize = { style: { width: 16, height: 16 } };
    const marginedIconSize = { style: { ...smallIconSize.style, marginRight: 16 } };
    
    return (
        <div className='d-flex flex-row align-items-center mr-4'>
            <Dropdown>
                <Dropdown.Toggle
                    as={DropdownToggle as React.ElementType}
                    className='profile-dropdown-toggle dropdown-toggle'
                    id='dropdown-custom-components'
                >
                    <i className='custom-icon icon' aria-label='User' />
                </Dropdown.Toggle>
                <Dropdown.Menu className='dropdown-menu profile-dropdown' as={DropdownMenu as React.ElementType}>
                    <Dropdown.Item eventKey='1' href='#'>
                        <i className='custom-icon icon' aria-label='User' {...marginedIconSize} />
                        <span
                            className='d-flex flex-column align-items-start justify-content-around'
                            style={{ lineHeight: 1.5 }}
                        >
                        <span className='small pl-2'>
                            Hi {user.firstName ? capitalizeFirstLetter(user.firstName) : 'User'}!
                            <span className='hint-text d-block'>Manage your profile</span>
                        </span>
                    </span>
                    </Dropdown.Item>
                    <Dropdown.Divider />
                    <Dropdown.Item eventKey='billing' href='#'>
    
                        <span className='d-flex flex-row align-items-start justify-content-around'>
                            <i className='custom-icon icon invoice' {...marginedIconSize} />
                            <span className='pl-2 small'>Billing Settings</span>
                        </span>
                    </Dropdown.Item>
                    <Dropdown.Item eventKey='team' href='#'>
                        <span className='d-flex flex-row align-items-start justify-content-around'>
                            <i className='custom-icon icon' aria-label='Users' {...marginedIconSize} />
                            <span className='pl-2 small'>My Team</span>
                        </span>
                    </Dropdown.Item>
                    <Dropdown.Divider />
                    <Dropdown.Item className='logout' eventKey='logout' onClick={logout}>
                        <span className='small'>Logout</span>
                        <i className='custom-icon icon' aria-label='Power' {...smallIconSize} />
                    </Dropdown.Item>
                </Dropdown.Menu>
            </Dropdown>
        </div>
    );
};
