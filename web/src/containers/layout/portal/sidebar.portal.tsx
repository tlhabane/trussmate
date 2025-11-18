import React, { JSX } from 'react';
import { NavLink, NavLinkProps, Link } from 'react-router-dom';
import { Logo } from '../../../components';
import { Sidebar } from '../sidebar';
import { UserRole } from '../../../models';

const NavAnchor: React.FC<NavLinkProps> = ({ className, ...props }) => (
    <NavLink {...props} className={({ isActive }) => (`tooltip-right ${isActive ? 'active' : ''}`)} />
);

export const SidebarPortal: React.FC<{ role: UserRole }> = ({ role }): JSX.Element => (
    <Sidebar>
        <div className='logo'>
            <Link to='/home'>
                <Logo style={{ width: 40, height: 40 }} />
            </Link>
        </div>
        <nav>
            <span>
                <NavAnchor
                    to='/home'
                    data-tooltip='Home'
                >
                    <i className='custom-icon icon bar-chart' />
                    <span>Home</span>
                </NavAnchor>
                {(role.toString() !== 'super_admin' && role.toString() !== 'admin') && (
                    <NavAnchor
                        to='/inbox'
                        data-tooltip='Inbox'
                    >
                        <i className='custom-icon icon inbox' />
                        <span>Inbox</span>
                    </NavAnchor>
                )}
                {(role.toString() === 'super_admin' || role.toString() === 'admin') && (
                    <NavAnchor
                        to='/sales'
                        data-tooltip='Sales'
                    >
                        <i className='custom-icon icon money-1' />
                        <span>Sales</span>
                    </NavAnchor>
                )}
    
                <hr className='nav-divider' aria-hidden />
                <NavAnchor
                    to='/customers'
                    data-tooltip='Customers'
                >
                    <i className='custom-icon icon users' />
                    <span>Customers</span>
                </NavAnchor>
            </span>
            {(role.toString() === 'super_admin' || role.toString() === 'admin') && (
                <span className='nav-footer'>
                    <NavAnchor
                        to='/settings/account'
                        data-tooltip='Settings'
                    >
                    <i className='custom-icon icon settings' />
                    <span>Settings</span>
                    </NavAnchor>
                </span>
            )}
        </nav>
    </Sidebar>
);
