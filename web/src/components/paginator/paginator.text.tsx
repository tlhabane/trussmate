import React from 'react';
import type { Pagination } from '../../types';

type PaginationTextType = {
    compact?: boolean;
    paginationObject: Pagination;
    recordsPerPage: number;
    itemType?: string;
};

export const PaginatorText: React.FC<PaginationTextType> = ({
  compact = true,
  paginationObject,
  recordsPerPage,
  itemType = 'Item(s)',
}) => {
    const { totalRecords, currentPage } = paginationObject;
    
    let pageShowing = currentPage;
    if (currentPage > 1) {
        pageShowing = currentPage * recordsPerPage - recordsPerPage + 1;
    }
    
    let recordOffset = recordsPerPage;
    
    if (recordsPerPage >= totalRecords) {
        recordOffset = totalRecords;
    } else if (currentPage > 1 && totalRecords > 0) {
        if (totalRecords > currentPage * recordsPerPage) {
            recordOffset = currentPage * recordsPerPage;
        } else {
            recordOffset = totalRecords;
        }
    }
    
    const getPaginationText = () => {
        let paginationText = `0 ${itemType}`;
        if (totalRecords > 0) {
            paginationText = `${pageShowing} - ${recordOffset} of ${totalRecords} ${itemType}`;
            if (!compact) {
                paginationText = `Showing ${paginationText}`;
            }
        }
        
        return paginationText;
    };
    return (
        <span className="small hint-text bold fs-13 d-flex align-items-center">
            {getPaginationText()}
        </span>
    );
};
