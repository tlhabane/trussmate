import React, { JSX, useCallback, useEffect, useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { SaleSettingItem } from './sale-setting-item';
import {
    Button,
    ContainerSpinner,
    getRecordsDisplayedOptions,
    EmptyAdditionalNotice,
    EmptyListContainer,
    EmptyNoticeButton,
    pagingLinkClickHandler,
    recordsPerPageSelectionHandler,
    SearchAndFilter,
} from '../../components';
import { ModalPrompt } from '../../containers';
import { getHttpRequestConfig, useFetchData, useAxios } from '../../utils';
import { useSearchHandlers, usePromiseToast, usePreviousLocation } from '../../hooks';
import { WorkflowList } from '../../models';
import type {
    ButtonClickFn,
    HTMLElementClickFn,
    LinkClickFn,
    Pagination,
    ReactSelectSingleOption,
} from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';

export default function SaleSettings(): JSX.Element {
    document.title = `Sale Settings :: ${APP_NAME}`;
    
    const initFilterParams = useMemo<Record<string, any>>(
        () => ({
            search: '',
            page: 1,
            recordsPerPage: 10,
        }),
        [],
    );
    const [filterParams, setFilterParams] = useState(initFilterParams);
    const fetchConfig = useMemo(() => ({
        url: '/workflow',
        queryKey: ['workflows'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    
    const [loadWorkflowList, setLoadWorkflowList] = useState(false);
    const [workflowList, setWorkflowList] = useState<WorkflowList[]>([]);
    const [pagination, setPagination] = useState<Pagination | null>(null);
    const { data, isLoading, isFetching, refetch } = useFetchData(fetchConfig);
    
    const updateData = useCallback((data?: any) => {
        if (data) {
            const updatedWorkflowList = (data?.records || []) as WorkflowList[];
            const updatedPagination = data?.pagination as Pagination;
            setWorkflowList(updatedWorkflowList);
            setPagination(updatedPagination);
            setLoadWorkflowList(false);
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
        setLoadWorkflowList(true);
        refetch()
            .then(({ data: updatedData }) => {
                updateData(updatedData);
            })
            .finally(() => {
                setLoadWorkflowList(false);
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
        pagingLinkClickHandler(event, setFilterParams, setLoadWorkflowList);
    };
    
    const handleUpdateRecordsPerPage: HTMLElementClickFn<void> = (event) => {
        recordsPerPageSelectionHandler(event, setWorkflowList, setFilterParams, setLoadWorkflowList);
    };
    
    const handleRefreshClientList: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        updateRecords();
    };
    
    /* Add Workflow */
    const navigate = useNavigate();
    const location = usePreviousLocation();
    
    const handleAddWorkflow: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        navigate('/sale/process/management', { state: { from: location?.pathname } });
    };
    
    /* Update workflow */
    const handleUpdateWorkflow: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { workflow } = event.currentTarget.dataset;
        if (workflow) {
            navigate(`/sale/process/management/${workflow}`, { state: { from: location?.pathname } });
        }
    };
    
    /* Delete workflow */
    const [promptUser, setPromptUser] = useState(false);
    const [confirmationButton, setConfirmationButton] = useState(<Button />);
    const workflowDeleteMessage = (
        <>
            <h5>Are your sure you want to delete the selected workflow?</h5>
            <p className='small'>The effects of this action cannot be reversed.</p>
        </>
    );
    
    const handleDismissPromptModal = () => {
        setConfirmationButton(<Button />);
        setPromptUser(false);
    };
    
    const axios = useAxios();
    const toast = usePromiseToast();
    const handleProceedDeletingWorkflow: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const { workflow } = event.currentTarget.dataset;
        const httpRequestConfig = {
            ...getHttpRequestConfig('DELETE'),
            url: 'workflow',
            data: { workflowId: workflow || '' },
        };
        
        await toast(axios(httpRequestConfig, undefined, event.currentTarget));
        updateRecords();
    };
    
    const handleDeleteWorkflow: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { workflow } = event.currentTarget.dataset;
        if (workflow) {
            setConfirmationButton(
                <Button
                    className='btn-danger btn-block'
                    data-workflow={workflow}
                    onClick={handleProceedDeletingWorkflow}
                >
                    <i className='custom-icon icon left-icon trash' />
                    Delete
                </Button>,
            );
            setPromptUser(true);
        }
    };
    
    const {
        handleClearSearch,
        handleSearchValueChange,
        searchInputRef,
    } = useSearchHandlers(setFilterParams, updateRecords);
    
    const RenderWorkflowList = () => {
        if (isLoading && !data) {
            return <ContainerSpinner />;
        }
        
        if (workflowList.length > 0) {
            return (
                <div className='d-block mt-3 mb-5'>
                    {workflowList.map((workflow) => (
                        <SaleSettingItem
                            key={workflow.workflowId}
                            workflow={workflow}
                            deleteWorkflow={handleDeleteWorkflow}
                            updateWorkflow={handleUpdateWorkflow}
                        />
                    ))}
                </div>
            );
        }
        
        const listFiltered = filterParams.search.trim().length > 0;
        
        return (
            <div className='row p-5'>
                <div className='col-md-6 offset-md-3'>
                    <EmptyListContainer>
                        <i className='custom-icon icon activity' style={{ width: 48, height: 48 }} />
                        <p className='hint-text text-center mt-2'>
                            {listFiltered ? (
                                <>No workflows matching your criteria available</>
                            ) : (
                                <>
                                    No workflows currently available.
                                    <br /> Once available, all your workflows will be displayed here.
                                </>
                            )}
                        </p>
                        <EmptyNoticeButton onClick={handleAddWorkflow}>
                            Add {listFiltered ? 'Missing' : 'New'} workflow
                        </EmptyNoticeButton>
                        <EmptyAdditionalNotice>
                            <Button className='btn-link'>
                                Upload {listFiltered ? 'Missing' : 'New'} workflows
                            </Button>
                        </EmptyAdditionalNotice>
                    </EmptyListContainer>
                </div>
            </div>
        );
    };
    
    return (
        <>
            <ModalPrompt
                openModalPrompt={promptUser}
                dismissModalPrompt={handleDismissPromptModal}
                promptConfirmationButton={confirmationButton}
            >
                {workflowDeleteMessage}
            </ModalPrompt>
            <div className='d-flex flex-fill align-items-center justify-content-center'>
                <div className='d-flex flex-fill flex-column pl-lg-3'>
                    <SearchAndFilter
                        ref={searchInputRef}
                        disabled={isLoading || isFetching}
                        loading={loadWorkflowList || isFetching}
                        filterParams={filterParams}
                        pagination={pagination}
                        addNewHandler={handleAddWorkflow}
                        clearSearchHandler={handleClearSearch}
                        refreshDataHandler={handleRefreshClientList}
                        searchValueChangeHandler={handleSearchValueChange}
                        recordsPerPageOptions={recordsPerPageOptions}
                        resetFilterParamsHandler={handleResetFilterParams}
                        paginationLinkHandler={handlePaginationLinkClick}
                        updateRecordsPerPageHandler={handleUpdateRecordsPerPage}
                    />
                    <RenderWorkflowList />
                </div>
            </div>
        </>
    );
}
