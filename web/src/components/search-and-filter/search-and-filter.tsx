import React, { JSX } from 'react';
import { PaginatorHeader } from '../paginator';
import { SearchControls } from './search-controls';
import { StickyContainer, StickyRow } from '../../containers';
import type {
    ButtonClickFn,
    HTMLElementClickFn,
    InputChangeFn,
    LinkClickFn,
    Pagination,
    ReactSelectSingleOption,
} from '../../types';

interface Props extends React.HTMLProps<HTMLInputElement> {
    loading?: boolean;
    addNewHandler?: ButtonClickFn<void>;
    downloadHandler?: ButtonClickFn<void>;
    filterParams: Record<string, any>;
    filterOptionsVisible?: boolean;
    pagination: Pagination | null;
    clearSearchHandler: ButtonClickFn<void>;
    refreshDataHandler: ButtonClickFn<void>;
    searchValueChangeHandler: InputChangeFn<HTMLInputElement>;
    recordsPerPageOptions: ReactSelectSingleOption[];
    resetFilterParamsHandler?: LinkClickFn<void>;
    paginationLinkHandler: LinkClickFn<void>;
    toggleFilterOptionsHandler?: ButtonClickFn<void>;
    updateRecordsPerPageHandler?: HTMLElementClickFn<void>;
}

export const SearchAndFilter = React.forwardRef<HTMLInputElement, Props>((props, ref) => {
    const {
        loading,
        addNewHandler,
        downloadHandler,
        disabled,
        filterParams,
        filterOptionsVisible,
        pagination,
        clearSearchHandler,
        refreshDataHandler,
        searchValueChangeHandler,
        recordsPerPageOptions,
        resetFilterParamsHandler,
        paginationLinkHandler,
        toggleFilterOptionsHandler,
        updateRecordsPerPageHandler,
    } = props;
    
    return (
        <StickyContainer>
            <StickyRow>
                <div className='row'>
                    <div className={`col-${pagination ? '8' : '12'}`}>
                        <SearchControls
                            ref={ref}
                            disabled={disabled}
                            loading={loading}
                            addNewHandler={addNewHandler}
                            clearSearchHandler={clearSearchHandler}
                            downloadHandler={downloadHandler}
                            refreshDataHandler={refreshDataHandler}
                            optionsVisible={filterOptionsVisible}
                            searchHandler={searchValueChangeHandler}
                            toggleFilterOptionsHandler={toggleFilterOptionsHandler}
                        />
                    </div>
                    {pagination && (
                        <div className='col-4'>
                            <PaginatorHeader
                                recordsPerPageOptions={recordsPerPageOptions}
                                paginationObject={pagination}
                                recordsPerPage={filterParams.recordsPerPage}
                                resetListFilterHandler={resetFilterParamsHandler}
                                paginationLinkHandler={paginationLinkHandler}
                                updateRecordsPerPageHandler={updateRecordsPerPageHandler}
                            />
                        </div>
                    )}
                </div>
            </StickyRow>
        </StickyContainer>
    );
});
