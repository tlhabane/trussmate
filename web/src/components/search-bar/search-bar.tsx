import React, { JSX, useState } from 'react';
import { Button } from '../button';
import { ButtonClickFn, InputChangeFn } from '../../types';
import './styles.scss';

interface Props extends React.HTMLProps<HTMLInputElement> {
    clearSearchHandler: ButtonClickFn<void>;
}

export const SearchBar = React.forwardRef<HTMLInputElement, Props>((props, ref): React.JSX.Element | null => {
    const { clearSearchHandler, defaultValue, onChange } = props;
    
    const [activateClearSearch, setActivateClearSearch] = useState(false);
    const handleSearchValueUpdate: InputChangeFn<HTMLInputElement> = (event) => {
        setActivateClearSearch(event.target.value.toString().trim().length > 0);
        if (onChange) {
            onChange(event);
        }
    };
    
    const handleClearSearch: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        setActivateClearSearch(false);
        clearSearchHandler(event);
    };
    
    if (!onChange) {
        return null;
    }
    
    return (
        <div id='globalSearchBar' className={`default-search ${activateClearSearch ? 'focused' : ''}`}>
            <div>
                <i className='custom-icon icon' aria-label='Search' />
            </div>
            <input
                type='search'
                ref={ref}
                placeholder='Search...'
                defaultValue={defaultValue}
                onChange={handleSearchValueUpdate}
            />
            <div className='clear-search '>
                <Button
                    className={`btn-link ${activateClearSearch ? '' : 'disabled'}`}
                    onClick={handleClearSearch}
                    disabled={!activateClearSearch}
                >
                    <i className='custom-icon icon' aria-label='Close' />
                </Button>
            </div>
        </div>
    );
});
