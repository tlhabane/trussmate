import { LinkClickFn } from './event-functions'

export interface PaginationPage {
    url: string;
    page: number;
    currentPage: boolean;
}

export interface Pagination {
    compact?: boolean;
    totalRecords: number;
    totalPages: number;
    currentPage: number;
    first: string;
    last: string;
    pages: PaginationPage[];
    onClick: LinkClickFn<void>;
}
