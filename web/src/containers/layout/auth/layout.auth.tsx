import React from 'react';
import { Outlet } from 'react-router-dom';
import { LayoutContainer } from '../layout.container';

export const LayoutAuth: React.FC = () => (
    <LayoutContainer>
        <div className="auth-wrapper">
            <div className="content">
                <Outlet />
            </div>
        </div>
    </LayoutContainer>
);
