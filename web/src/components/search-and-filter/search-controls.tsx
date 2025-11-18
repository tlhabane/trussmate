import React, { JSX } from 'react';
import { ControlButton } from './control-button';
import { SearchBar } from '../search-bar';
import { ButtonClickFn, InputChangeFn } from '../../types';

interface Props extends React.HTMLProps<HTMLInputElement> {
    loading?: boolean;
    addNewHandler?: ButtonClickFn<void>;
    clearSearchHandler: ButtonClickFn<void>;
    downloadHandler?: ButtonClickFn<void>;
    refreshDataHandler: ButtonClickFn<void>;
    searchHandler: InputChangeFn<HTMLInputElement>;
    optionsVisible?: boolean;
    toggleFilterOptionsHandler?: ButtonClickFn<void>;
}

export const SearchControls = React.forwardRef<HTMLInputElement, Props>((props, ref): JSX.Element => {
    const {
        addNewHandler,
        clearSearchHandler,
        downloadHandler,
        disabled,
        loading,
        refreshDataHandler,
        searchHandler,
        optionsVisible,
        toggleFilterOptionsHandler,
    } = props;
    const filterOptionsIcon = `${optionsVisible ? 'close' : 'customise'}`;
    const allButtons = !!(toggleFilterOptionsHandler && (addNewHandler || downloadHandler));
    const someButtons = !!(toggleFilterOptionsHandler || addNewHandler || downloadHandler);
    
    let colSize = `${someButtons ? '9' : '11'}`;
    if (allButtons) {
        colSize = '8';
    }
    
    return (
        <div className='row'>
            <div className={`col-${colSize} pr-0`}>
                <SearchBar
                    ref={ref}
                    clearSearchHandler={clearSearchHandler}
                    onChange={searchHandler}
                />
            </div>
            {someButtons && (
                <div className={`col-${allButtons ? '4' : '3'} pl-0`}>
                    <div className='row'>
                        {toggleFilterOptionsHandler && (
                            <div className={`col-${allButtons ? '4' : '6'} pl-0 pr-0`}>
                                <ControlButton
                                    data-tooltip='Filter Options'
                                    disabled={disabled}
                                    onClick={toggleFilterOptionsHandler}
                                >
                                    <i className={`custom-icon icon icon-only ${filterOptionsIcon}`} />
                                </ControlButton>
                            </div>
                        )}
                        <div className={`col-${allButtons ? '4' : '6'} pl-0 pr-0`}>
                            <ControlButton
                                data-tooltip='Refresh'
                                disabled={disabled}
                                loading={loading}
                                onClick={refreshDataHandler}
                            >
                                <i className='custom-icon icon icon-only refresh' />
                            </ControlButton>
                        </div>
                        {addNewHandler && (
                            <div className={`col-${allButtons ? '4' : '6'} pl-0`}>
                                <ControlButton
                                    data-tooltip='Add New'
                                    disabled={disabled}
                                    onClick={addNewHandler}
                                >
                                    <i className='custom-icon icon icon-only plus-circle' />
                                </ControlButton>
                            </div>
                        )}
                        {downloadHandler && (
                            <div className={`col-${allButtons ? '4' : '6'} pl-0`}>
                                <ControlButton
                                    data-tooltip='Download'
                                    disabled={disabled}
                                    onClick={downloadHandler}
                                >
                                    <i className='custom-icon icon icon-only download' />
                                </ControlButton>
                            </div>
                        )}
                    </div>
                </div>
            )}
            {!someButtons && (
                <div className='col-1 pl-0'>
                    <ControlButton
                        data-tooltip='Refresh'
                        disabled={disabled}
                        onClick={refreshDataHandler}
                    >
                        <i className='custom-icon icon icon-only refresh' />
                    </ControlButton>
                </div>
            )}
        </div>
    );
});
