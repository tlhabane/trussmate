import React, { JSX, useEffect, useRef, useState } from 'react';
import { Outlet, useNavigate } from 'react-router-dom';
import { HeaderBranding, HeaderChild } from './header';
import { getHttpRequestConfig, useAxios } from '../../utils';
import { clearLocalStorage } from '../../store';
import { useAuthenticatedUser, usePromiseToast } from '../../hooks';
import { ContainerSpinner } from '../../components';
import { ButtonClickFn, InputChangeFn } from '../../types';
import { AuthorisedUser } from '../../models';
import './styles.scss';

let initAuthentication = true;
type RefreshDataHandler = () => void;
type Props = {
    authPage?: boolean;
    childPage?: boolean;
};
export const Layout: React.FC<Props> = ({ authPage, childPage }): JSX.Element => {
    const [refreshDataHandler, setRefreshDataHandler] = useState<RefreshDataHandler | null>(null);
    const navigate = useNavigate();
    const handleAddCustomer: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        navigate('/member-management');
    };
    
    const uploadInput = useRef<HTMLInputElement | null>(null);
    const handleUploadCustomer: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        uploadInput.current?.click();
    };
    
    const [authorisedUser, setAuthorisedUser] = useState<AuthorisedUser | null>(null);
    const [authenticating, setAuthenticating] = useState(!authPage);
    const authenticate = useAuthenticatedUser();
    
    useEffect(() => {
        if (authenticating && initAuthentication) {
            initAuthentication = false;
            authenticate()
                .then((user) => {
                    if (user) {
                        setAuthorisedUser(user);
                    }
                })
                .finally(() => {
                    setAuthenticating(false);
                });
        }
    }, [authenticate, authenticating]);
    
    const axios = useAxios();
    const toast = usePromiseToast();
    
    const onLogout: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        try {
            const httpRequestConfig = {
                ...getHttpRequestConfig('POST', authorisedUser?.token || ''),
                url: '/logout',
            };
            await toast(axios(httpRequestConfig));
            clearLocalStorage();
            navigate('/');
        } catch (error: any) {
            clearLocalStorage();
            navigate('/');
        }
    };
    
    const [uploading, setUploading] = useState(false);
    const onUpload: InputChangeFn<HTMLInputElement> = async (event) => {
        try {
            const { files } = event.currentTarget;
            if (files) {
                setUploading(true);
                const formData = new FormData();
                for (let i = 0; i < files.length; i++) {
                    formData.append('file[]', files[i]);
                }
                
                const httpRequestConfig = {
                    ...getHttpRequestConfig('POST'),
                    url: '/customer/load',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    data: formData,
                };
                
                await toast(axios(httpRequestConfig), 'Uploading...');
                setUploading(false);
                refreshDataHandler && refreshDataHandler();
            }
        } catch (e) {
            setUploading(false);
        }
    };
    
    return (
        <main>
            {!authPage && (
                <>
                    <header>
                        {childPage ? (
                            <HeaderChild />
                        ) : (
                            <HeaderBranding
                                ref={uploadInput}
                                addNewHandler={handleAddCustomer}
                                logoutHandler={onLogout}
                                onUpload={onUpload}
                                uploading={uploading}
                            />
                        )}
                    </header>
                    <div className='content'>
                        {authenticating && <ContainerSpinner />}
                        <Outlet
                            context={{
                                authorisedUser,
                                setIsRefreshing: setUploading,
                                setRefreshDataHandler,
                                addCustomerHandler: handleAddCustomer,
                                uploadCustomerDataHandler: handleUploadCustomer,
                            }}
                        />
                    </div>
                </>
            )}
            {authPage && (
                <div className='auth-wrapper'>
                    <Outlet />
                </div>
            )}
        </main>
    );
};
