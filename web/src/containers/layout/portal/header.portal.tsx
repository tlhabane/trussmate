import React from 'react';
import { Divide as Hamburger } from 'hamburger-react';
import { HeaderLogo } from '../header';
import { Button } from '../../../components';
import { UserOptions } from './user-options';
import { AuthorisedUser } from '../../../models';
import { ButtonClickFn, HTMLElementClickFn } from '../../../types';

type Props = {
    addNewHandler?: ButtonClickFn<void> | null;
    logout: HTMLElementClickFn<void>;
    navigationOpen: boolean;
    toggleNavigation: () => void;
    uploading?: boolean;
    user: AuthorisedUser;
};

export const HeaderPortal: React.FC<Props> = (props) => {
    const { addNewHandler, logout, navigationOpen, toggleNavigation, uploading, user } = props;
    return (
        <header>
            <div className='header-inner'>
                <div
                    className='d-flex flex-fill flex-row align-items-center justify-content-between justify-content-lg-end'>
                    <div className='d-flex flex-row align-items-center d-lg-none'>
                        <Hamburger
                            toggled={navigationOpen}
                            toggle={toggleNavigation}
                            label='Open menu'
                            size={28}
                            rounded
                        />
                        <span className='mx-2'>
                            <HeaderLogo />
                        </span>
                    </div>
                    <div className='d-flex flex-row align-items-center mr-4'>
                        {addNewHandler && (
                            <>
                                <Button
                                    className={`btn btn-success btn-rounded px-2 ${uploading ? 'loading' : ''}`}
                                    style={{ height: '44px !important' }}
                                    onClick={addNewHandler}
                                >
                                    <i
                                        className='custom-icon icon left-icon plus-circle'
                                    />
                                    <span className='text-right pl-4 pr-2 ml-3'>Add New</span>
                                </Button>
                                <div className='btn-divider mx-3' />
                            </>
                        )}
                        
                        {/*<button
                            type="button"
                            className="profile-dropdown-toggle tooltip-bottom"
                            data-tooltip="Logout"
                            onClick={logout}
                        >
                            <i className="custom-icon icon power" style={{ width: 24, height: 24 }} />
                        </button>*/}
                        <UserOptions user={user} logout={logout} />
                    </div>
                </div>
            </div>
        </header>
    );
};
