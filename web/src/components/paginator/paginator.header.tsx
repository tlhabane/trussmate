import React from 'react';
import { PaginatorLinks } from './paginator.links';
import { PaginatorText } from './paginator.text';
import { RecordsPerPageSelector } from './paginator.options';
import { PaginatorDropdown } from './paginator.dropdown';
import type { HTMLElementClickFn, LinkClickFn, Pagination, ReactSelectFn, ReactSelectSingleOption } from '../../types';

type Props = {
    paginationLinkHandler: LinkClickFn<void>;
    recordsPerPageOptions: ReactSelectSingleOption[];
    recordsLimitSelectHandler?: ReactSelectFn<void>;
    updateRecordsPerPageHandler?: HTMLElementClickFn<void>;
    paginationObject: Pagination;
    recordsPerPage: number;
    itemType?: string;
    resetListFilterHandler?: LinkClickFn<void>;
};

export const PaginatorHeader: React.FC<Props> = (props) => {
    const {
        paginationLinkHandler,
        recordsPerPageOptions,
        recordsLimitSelectHandler,
        resetListFilterHandler,
        updateRecordsPerPageHandler,
        paginationObject,
        recordsPerPage,
        itemType,
    } = props;
    
    let updatedRecordsPerPageHandler = updateRecordsPerPageHandler;
    let updatedRecordsPerPageOptions = recordsPerPageOptions;
    let selectedRecordsPerPage =
        updatedRecordsPerPageOptions.find((option) => +option.value === recordsPerPage) ||
        updatedRecordsPerPageOptions[0];
    
    const { totalPages, totalRecords } = paginationObject;
    if (totalPages <= 1) {
        const onlyAvailableRecordsPerPageOption: ReactSelectSingleOption = {
            label: totalRecords.toString(),
            value: totalRecords.toString(),
        };
        updatedRecordsPerPageOptions = [onlyAvailableRecordsPerPageOption];
        selectedRecordsPerPage = onlyAvailableRecordsPerPageOption;
        updatedRecordsPerPageHandler = (event) => {
            event.preventDefault();
        };
    }
    
    return (
        <div className="d-flex p-2 full-width flex-row align-items-center justify-content-end">
            <PaginatorText
                paginationObject={paginationObject}
                recordsPerPage={recordsPerPage}
                itemType={itemType || ''}
                compact
            />
            {recordsLimitSelectHandler ? (
                <RecordsPerPageSelector
                    options={recordsPerPageOptions}
                    onChange={recordsLimitSelectHandler as any}
                    defaultValue={selectedRecordsPerPage}
                />
            ) : null}
            <PaginatorLinks {...paginationObject} onClick={paginationLinkHandler} compact />
            {updatedRecordsPerPageHandler ? (
                <PaginatorDropdown
                    enabled={paginationObject.totalRecords > 0}
                    activeRecordsPerPage={selectedRecordsPerPage}
                    recordsPerPage={updatedRecordsPerPageOptions}
                    pagingHandler={updatedRecordsPerPageHandler as any}
                    resetListFilterHandler={resetListFilterHandler}
                />
            ) : null}
        </div>
    );
}
