import React, { JSX, useCallback, useEffect, useMemo, useState } from 'react';
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
import { TeamMemberItem } from './components';
import { getHttpRequestConfig, useFetchData, useAxios } from '../../utils';
import { usePromiseToast, useSearchHandlers } from '../../hooks';
import { User } from '../../models';
import type {
    ButtonClickFn,
    HTMLElementClickFn,
    LinkClickFn,
    Pagination,
    ReactSelectSingleOption,
} from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';

export default function TeamSettings(): JSX.Element {
    document.title = `Team Settings :: ${APP_NAME}`;
    
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
        url: '/user',
        queryKey: ['team'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    
    const [loadUserList, setLoadUserList] = useState(false);
    const [userList, setUserList] = useState<User[]>([]);
    const [pagination, setPagination] = useState<Pagination | null>(null);
    const { data, isLoading, isFetching, refetch } = useFetchData(fetchConfig);
    
    const updateData = useCallback((data?: any) => {
        if (data) {
            const updatedUserList = (data?.records || []) as User[];
            const updatedPagination = data?.pagination as Pagination;
            setUserList(updatedUserList);
            setPagination(updatedPagination);
            setLoadUserList(false);
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
        setLoadUserList(true);
        refetch()
            .then(({ data: updatedData }) => {
                updateData(updatedData);
            })
            .finally(() => {
                setLoadUserList(false);
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
        pagingLinkClickHandler(event, setFilterParams, setLoadUserList);
    };
    
    const handleUpdateRecordsPerPage: HTMLElementClickFn<void> = (event) => {
        recordsPerPageSelectionHandler(event, setUserList, setFilterParams, setLoadUserList);
    };
    
    const handleRefreshClientList: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        updateRecords();
    };
    
    /* Add User */
    const handleAddUser: ButtonClickFn<void> = (event) => {
        event.preventDefault();
    };
    
    /* Update user */
    const handleUpdateUser: ButtonClickFn<void> = (event) => {
        event.preventDefault();
    };
    
    /* Toggle user account status */
    const handleToggleUserState: ButtonClickFn<void> = (event) => {
        event.preventDefault();
    };
    
    /* Delete user */
    const [promptUser, setPromptUser] = useState(false);
    const [confirmationButton, setConfirmationButton] = useState(<Button />);
    const userDeleteMessage = (
        <>
            <h5>Are your sure you want to delete the selected user?</h5>
            <p className='small'>The effects of this action cannot be reversed.</p>
        </>
    );
    
    const handleDismissPromptModal = () => {
        setConfirmationButton(<Button />);
        setPromptUser(false);
    };
    
    const axios = useAxios();
    const toast = usePromiseToast();
    const handleProceedDeletingUser: ButtonClickFn<void> = async (event) => {
        event.preventDefault();
        const { user } = event.currentTarget.dataset;
        const httpRequestConfig = {
            ...getHttpRequestConfig('DELETE'),
            url: 'user',
            data: { userId: user || '' },
        };
        
        await toast(axios(httpRequestConfig, undefined, event.currentTarget));
        updateRecords();
    };
    
    const handleDeleteUser: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        const { user } = event.currentTarget.dataset;
        if (user) {
            setConfirmationButton(
                <Button
                    className='btn-danger btn-block'
                    data-user={user}
                    onClick={handleProceedDeletingUser}
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
    
    const RenderUserList = () => {
        if (isLoading && !data) {
            return <ContainerSpinner />;
        }
        
        if (userList.length > 0) {
            return (
                <div className='d-block mt-3 mb-5'>
                    {userList.map((user) => (
                        <TeamMemberItem
                            key={user.username}
                            user={user}
                            toggleUserStateHandler={handleToggleUserState}
                            updateUserHandler={handleUpdateUser}
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
                        <i className='custom-icon icon users' style={{ width: 48, height: 48 }} />
                        <p className='hint-text text-center mt-2'>
                            {listFiltered ? (
                                <>No users matching your criteria available</>
                            ) : (
                                <>
                                    No users currently available.
                                    <br /> Once available, all your users will be displayed here.
                                </>
                            )}
                        </p>
                        <EmptyNoticeButton onClick={handleAddUser}>
                            Add {listFiltered ? 'Missing' : 'New'} user
                        </EmptyNoticeButton>
                        <EmptyAdditionalNotice>
                            <Button className='btn-link'>
                                Upload {listFiltered ? 'Missing' : 'New'} users
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
                {userDeleteMessage}
            </ModalPrompt>
            <div className='d-flex flex-fill align-items-center justify-content-center'>
                <div className='d-flex flex-fill flex-column pl-lg-3'>
                    <SearchAndFilter
                        ref={searchInputRef}
                        disabled={loadUserList || isFetching}
                        filterParams={filterParams}
                        pagination={pagination}
                        addNewHandler={handleAddUser}
                        clearSearchHandler={handleClearSearch}
                        refreshDataHandler={handleRefreshClientList}
                        searchValueChangeHandler={handleSearchValueChange}
                        recordsPerPageOptions={recordsPerPageOptions}
                        resetFilterParamsHandler={handleResetFilterParams}
                        paginationLinkHandler={handlePaginationLinkClick}
                        updateRecordsPerPageHandler={handleUpdateRecordsPerPage}
                    />
                    <RenderUserList />
                </div>
            </div>
        </>
    );
}
