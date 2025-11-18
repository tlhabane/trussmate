import React, { JSX } from 'react';
import { Outlet } from 'react-router-dom';
import { HeaderChild } from '../header';
import { LayoutContainer } from '../layout.container';
import { MenuContainer, MenuDivider, MenuItem } from '../../menu-container';

export const LayoutSettings: React.FC = (): JSX.Element => (
    <LayoutContainer>
        <HeaderChild href='home' />
        <div className='content'>
            <div className='d-flex flex-row flex-fill'>
                <MenuContainer>
                    <div className='menu'>
                        <MenuItem to='/settings/account'>
                            <i className='custom-icon icon settings' /> Account
                        </MenuItem>
                        <MenuItem to='/settings/bank'>
                            <i className='custom-icon icon money-1' /> Bank Accounts
                        </MenuItem>
                        <MenuDivider />
                        <MenuItem to='/settings/sale/task'>
                            <i className='custom-icon icon activity' /> Sales Process Tasks
                        </MenuItem>
                        <MenuItem to='/settings/sale/process'>
                            <i className='custom-icon icon invoice' /> Sale Processes
                        </MenuItem>
                        <MenuItem to='/settings/sale/monitor'>
                            <i className='custom-icon icon date-time' /> Monitoring & Escalations
                        </MenuItem>
                        <MenuDivider />
                        <MenuItem to='/settings/team'>
                            <i className='custom-icon icon users' /> Team
                        </MenuItem>
                    </div>
                </MenuContainer>
                <Outlet />
            </div>
        </div>
    </LayoutContainer>
);
