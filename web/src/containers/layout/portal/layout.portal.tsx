import React, { useCallback, useEffect, useState } from 'react';
import { Outlet, useNavigate, useOutletContext } from 'react-router-dom';
import { ContainerSpinner, FullPageSpinner } from '../../../components';
import { LayoutContainer } from '../layout.container';
import { HeaderPortal } from './header.portal';
import { SidebarPortal } from './sidebar.portal';
import { getHttpRequestConfig, useAxios } from '../../../utils';
import { clearLocalStorage } from '../../../store';
import { useAuthenticatedUser, usePromiseToast } from '../../../hooks';
import { AuthorisedUser } from '../../../models';
import { ButtonClickFn, HTMLElementClickFn, SetState } from '../../../types';

export const LayoutPortal = () => {
    const [navigationBarOpen, setNavigationBarOpen] = useState(false);
    const handleToggleNavigation = () => {
        setNavigationBarOpen((prevState) => !prevState);
    };
    
    const authenticate = useAuthenticatedUser();
    const navigate = useNavigate();
    const axios = useAxios();
    const toast = usePromiseToast();
    
    const cleanupAndLeave = useCallback(() => {
        clearLocalStorage();
        navigate('/');
    }, [navigate]);
    
    const [handleAddNew, setAddNewHandler] = useState<ButtonClickFn<void> | null>(null);
    const [authorisedUser, setAuthorisedUser] = useState<AuthorisedUser | null>(null);
    const [authenticating, setAuthenticating] = useState(true);
    useEffect(() => {
        if (authenticating) {
            (async () => {
                try {
                    const user = await authenticate();
                    user && setAuthorisedUser(user);
                } catch {
                    cleanupAndLeave();
                } finally {
                    setAuthenticating(false);
                }
            })();
        }
        
        
    }, [authenticate, authenticating, cleanupAndLeave]);
    
    const className = `${navigationBarOpen ? 'sidebar-open' : ''}`;
    if (!authorisedUser || authenticating) {
        return <FullPageSpinner />;
    }
    
    const handleLogout: HTMLElementClickFn<void> = async (event) => {
        event.preventDefault();
        try {
            const httpRequestConfig = {
                ...getHttpRequestConfig('POST', authorisedUser.token),
                url: '/logout',
            };
            await toast(axios(httpRequestConfig));
            cleanupAndLeave();
        } catch (error: any) {
            cleanupAndLeave();
        }
    };
    
    return (
        <LayoutContainer className={`portal ${className}`}>
            <SidebarPortal role={authorisedUser.userRole} />
            <div className='content-wrapper'>
                <HeaderPortal
                    addNewHandler={handleAddNew}
                    logout={handleLogout}
                    navigationOpen={navigationBarOpen}
                    toggleNavigation={handleToggleNavigation}
                    uploading={authenticating}
                    user={authorisedUser}
                />
                <div className='content'>
                    {authenticating ? <ContainerSpinner /> : <Outlet context={{ authorisedUser, setAddNewHandler }} />}
                </div>
            </div>
        </LayoutContainer>
    );
};

type OutletContextProps = {
    authorisedUser: AuthorisedUser;
    setAddNewHandler: SetState<ButtonClickFn<void> | null>;
}

export function useLayoutContext() {
    return useOutletContext<OutletContextProps>();
}
