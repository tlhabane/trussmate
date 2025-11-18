import React, { JSX, useCallback, useEffect, useMemo, useState } from 'react';
import {
    ContainerSpinner,
    getRecordsDisplayedOptions,
    EmptyListContainer,
    pagingLinkClickHandler,
    recordsPerPageSelectionHandler,
    SearchAndFilter,
} from '../../components';
import { ReportBody } from './report-body';
import { ReportFooter } from './report-footer';
import { ReportHeader } from './report-header';
import { useAxios, useFetchData, useHttpRequestConfig } from '../../utils';
import { usePromiseToast, useSearchHandlers } from '../../hooks';
import { AccountAging } from '../../models';
import {
    ButtonClickFn,
    LinkClickFn,
    HTMLElementClickFn,
    Pagination,
    ReactSelectSingleOption,
} from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';

export default function AccountAgingPage(): JSX.Element {
    document.title = `Accounts Aging :: ${APP_NAME}`;
    const initFilterParams = useMemo<Record<string, any>>(
        () => ({
            customerId: '0',
            search: '',
            page: 1,
            recordsPerPage: 10,
        }),
        [],
    );
    const [filterParams, setFilterParams] = useState(initFilterParams);
    
    const fetchConfig = useMemo(() => ({
        url: '/report/aging',
        queryKey: ['accountAging'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    const [loadReportData, setLoadReportData] = useState(false);
    const [reportData, setReportData] = useState<AccountAging[]>([]);
    const [pagination, setPagination] = useState<Pagination | null>(null);
    const { data, isLoading, isFetching, refetch } = useFetchData(fetchConfig);
    
    const updateData = useCallback((data?: any) => {
        if (data) {
            const updatedReportData = (data?.records || []) as AccountAging[];
            const updatedPagination = data?.pagination as Pagination;
            setReportData(updatedReportData);
            setPagination(updatedPagination);
            setLoadReportData(false);
        }
    }, []);
    
    useEffect(() => {
        updateData(data);
    }, [data, updateData]);
    
    const [recordsPerPageOptions, setRecordsPerPageOptions] = useState<ReactSelectSingleOption[]>([]);
    useEffect(() => {
        if (pagination) {
            const updatedRecordsPerPageOptions = getRecordsDisplayedOptions(pagination.totalRecords);
            setRecordsPerPageOptions(updatedRecordsPerPageOptions);
        }
    }, [pagination]);
    
    const updateRecords = useCallback(() => {
        setLoadReportData(true);
        refetch()
            .then(({ data: updatedData }) => {
                updateData(updatedData);
            })
            .finally(() => {
                setLoadReportData(false);
            });
    }, [refetch, updateData]);
    
    const resetFilterParams = () => {
        setFilterParams(initFilterParams);
        updateRecords();
    };
    const handleResetFilterParams: LinkClickFn<void> = (event) => {
        event.preventDefault();
        resetFilterParams();
    };
    
    const handlePaginationLinkClick: LinkClickFn<void> = (event) => {
        pagingLinkClickHandler(event, setFilterParams, setLoadReportData);
    };
    
    const handleUpdateRecordsPerPage: HTMLElementClickFn<void> = (event) => {
        recordsPerPageSelectionHandler(event, setReportData, setFilterParams, setLoadReportData);
    };
    
    const handleRefreshReportData: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        updateRecords();
    };
    
    const {
        handleClearSearch,
        handleSearchValueChange,
        searchInputRef,
    } = useSearchHandlers(setFilterParams, updateRecords);
    
    const axios = useAxios();
    const getHttpRequestConfig = useHttpRequestConfig();
    const toast = usePromiseToast();
    
    const handleDownloadReport: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const button = event.currentTarget;
        try {
            button?.classList.add('loading');
            const { customerId, search } = filterParams;
            const httpRequestConfig = {
                ...await getHttpRequestConfig(),
                url: '/report/aging/download',
                params: { customerId, search },
            };
            
            const response = await toast(axios(httpRequestConfig), 'Preparing report...');
            window.open(response.file, '_blank');
            button?.classList.remove('loading');
        } catch (error: any) {
            button?.classList.remove('loading');
        }
    };
    
    const RenderReport = () => {
        if (isLoading && !data) {
            return <ContainerSpinner />;
        }
        
        if (reportData.length > 0) {
            return (
                <div className='d-block mt-3 mb-5'>
                    <ReportHeader />
                    <ReportBody reportData={reportData} />
                    <ReportFooter reportData={reportData} />
                </div>
            );
        }
        
        const listFiltered = filterParams.customerId !== '0' && filterParams.search.trim().length > 0;
        
        return (
            <div className='row p-5'>
                <div className='col-md-6 offset-md-3'>
                    <EmptyListContainer>
                        <i className='custom-icon icon bar-chart' style={{ width: 48, height: 48 }} />
                        <p className='hint-text text-center mt-2'>
                            {listFiltered ? (
                                <>No report data matching your criteria available</>
                            ) : (
                                <>
                                    No report data currently available.
                                    <br /> Once available, your accounts aging report will be displayed here.
                                </>
                            )}
                        </p>
                    </EmptyListContainer>
                </div>
            </div>
        );
    };
    
    return (
        <div className='d-flex flex-fill flex-column'>
            <SearchAndFilter
                ref={searchInputRef}
                disabled={isLoading || isFetching}
                loading={loadReportData || isFetching}
                filterParams={filterParams}
                pagination={pagination}
                clearSearchHandler={handleClearSearch}
                downloadHandler={handleDownloadReport}
                refreshDataHandler={handleRefreshReportData}
                searchValueChangeHandler={handleSearchValueChange}
                recordsPerPageOptions={recordsPerPageOptions}
                resetFilterParamsHandler={handleResetFilterParams}
                paginationLinkHandler={handlePaginationLinkClick}
                updateRecordsPerPageHandler={handleUpdateRecordsPerPage}
            />
            <RenderReport />
        </div>
    );
};
