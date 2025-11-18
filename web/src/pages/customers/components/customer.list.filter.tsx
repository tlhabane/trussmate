import React, { JSX } from 'react';
import { Button, SearchBar } from '../../../components';
import { ButtonClickFn, InputChangeFn } from '../../../types';

interface Props extends React.HTMLProps<HTMLInputElement> {
    clearSearchHandler: ButtonClickFn<void>
    refreshDataHandler: ButtonClickFn<void>;
    searchHandler: InputChangeFn<HTMLInputElement>;
}

export const CustomerListFilter = React.forwardRef<HTMLInputElement, Props>((props, ref): JSX.Element => {
    const { disabled, clearSearchHandler, refreshDataHandler, searchHandler } = props;
    
    return (
        <div className="row">
            <div className="col-11 pr-0">
                <SearchBar
                    ref={ref}
                    clearSearchHandler={clearSearchHandler}
                    onChange={searchHandler}
                />
            </div>
            <div className="col-1 pl-0">
                <Button
                    disabled={disabled}
                    className="btn-default btn-block border-0 tooltip-bottom"
                    data-tooltip="Refresh"
                    onClick={refreshDataHandler}
                >
                    <div className="d-flex align-items-center justify-content-center">
                        <i className="custom-icon icon icon-only refresh" />
                    </div>
                </Button>
            </div>
        </div>
    )
});
