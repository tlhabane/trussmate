import React, { JSX } from 'react';
import { HeaderLogo } from './header.logo';
import { Button } from '../../../components';
import { ButtonClickFn, InputChangeFn } from '../../../types';

interface Props extends React.HTMLProps<HTMLInputElement> {
    uploading: boolean;
    addNewHandler: ButtonClickFn<void>;
    logoutHandler: ButtonClickFn<void>;
    onUpload: InputChangeFn<HTMLInputElement>;
}

export const HeaderBranding = React.forwardRef<HTMLInputElement, Props>((props, ref): JSX.Element => {
    const { addNewHandler, uploading, logoutHandler, onUpload } = props;

    return (
        <div className="header-inner">
            <HeaderLogo />
            <input
                accept=".csv"
                onChange={onUpload}
                type="file"
                style={{ opacity: 0, position: 'absolute' }}
                ref={ref}
                multiple
            />
            <div className="d-flex flex-row align-items-center justify-content-end">
                <div className="d-flex flex-row align-items-center mr-4">
                    <Button
                        className={`btn btn-success px-2 ${uploading ? 'loading' : ''}`}
                        onClick={addNewHandler}
                        disabled={uploading}
                    >
                        <i className="custom-icon icon left-icon plus-circle" style={{ width: 20, height: 20 }} />
                        <span className="text-right pl-4 pr-2 ml-3">Add Member</span>
                    </Button>
                    <div className="btn-divider mx-3" />
                    <button
                        type="button"
                        className="profile-dropdown-toggle tooltip-bottom"
                        data-tooltip="Logout"
                        onClick={logoutHandler}
                    >
                        <i className="custom-icon icon power" style={{ width: 24, height: 24 }} />
                    </button>
                </div>
            </div>
        </div>
    );
});
