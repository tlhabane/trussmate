import React from 'react';
import { NavLink, NavLinkProps }  from 'react-router-dom';

export const MenuItem: React.FC<NavLinkProps> = ({ className, ...rest }) => (
    <NavLink {...rest} className={({ isActive }) => `${className || ''} ${isActive ? 'active' : ''}`} />
);
