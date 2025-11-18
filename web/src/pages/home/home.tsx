import React, { JSX, useCallback, useEffect, useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { addDays, format } from 'date-fns';
import { AccountAgingSummary } from './account-aging-summary';
import { AccountBalanceChart } from './account-balance-chart';
import { TaskAnalytics } from './task-analytics';
import { StickyContainer, StickyRow, useLayoutContext } from '../../containers';
import { CardLabel, LabeledCard, SelectCustomer, TextInput } from '../../components';
import { usePreviousLocation } from '../../hooks';
import { useFetchData } from '../../utils';
import { AccountAging } from '../../models';
import type {
    ButtonClickFn,
    InputChangeFn,
    ReactSelectFn,
    ReactSelectSingleOption,
} from '../../types';
import { APP_NAME, ONE_MINUTE } from '../../config';

export default function Home(): JSX.Element {
    document.title = `Home :: ${APP_NAME}`;
    
    const initFilterParams = useMemo<Record<string, any>>(
        () => ({
            customerId: '0',
            page: 1,
            recordsPerPage: 5,
            startDate: format(addDays(new Date(), -180), 'yyyy-MM-dd'),
            endDate: format(new Date(), 'yyyy-MM-dd'),
        }),
        [],
    );
    
    const [filterParams, setFilterParams] = useState(initFilterParams);
    
    const handleFilterParamsUpdate: InputChangeFn<HTMLInputElement> = (event) => {
        const { name: elementName, value: elementValue } = event.currentTarget;
        setFilterParams((currentParams) => ({
            ...currentParams,
            [elementName]: elementValue,
        }));
    };
    
    const handleCustomerSelect: ReactSelectFn<void> = (option, value) => {
        setFilterParams((currentParams) => ({
            ...currentParams,
            [option]: (value as ReactSelectSingleOption)?.value || '0',
        }));
    };
    
    const fetchFilterConfig = useMemo(() => {
        const { customerId, startDate, endDate } = filterParams;
        return {
            params: { customerId, startDate, endDate },
            refetchInterval: 5 * ONE_MINUTE, // 5 minutes
            staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
        };
    }, [filterParams]);
    
    const fetchTaskAnalyticsConfig = useMemo(() => ({
        ...fetchFilterConfig,
        url: '/report/task/analytics',
        queryKey: ['taskAnalytics'],
    }), [fetchFilterConfig]);
    
    const {
        data: taskAnalyticsData,
        isLoading: taskAnalyticsLoading,
    } = useFetchData(fetchTaskAnalyticsConfig);
    const [taskAnalytics, setTaskAnalytics] = useState<Record<string, any>[]>([]);
    
    useEffect(() => {
        if (taskAnalyticsData) {
            setTaskAnalytics(taskAnalyticsData?.records || []);
        }
    }, [taskAnalyticsData]);
    
    const fetchAccountBalancesConfig = useMemo(() => ({
        ...fetchFilterConfig,
        url: '/report/balances',
        queryKey: ['accountBalances'],
    }), [fetchFilterConfig]);
    
    const {
        data: accountBalancesData,
        isLoading: accountBalancesLoading,
    } = useFetchData(fetchAccountBalancesConfig);
    const [accountBalances, setAccountBalance] = useState<Record<string, any>[]>([]);
    
    useEffect(() => {
        if (accountBalancesData) {
            setAccountBalance(accountBalancesData?.records || []);
        }
    }, [accountBalancesData]);
    
    const fetchAccountAgingConfig = useMemo(() => ({
        url: '/report/aging',
        queryKey: ['accountAging'],
        params: filterParams,
        refetchInterval: 5 * ONE_MINUTE, // 5 minutes
        staleTime: 4.5 * ONE_MINUTE, // 4.5 minutes
    }), [filterParams]);
    
    const { data: accountAgingData, isLoading: accountAgingLoading } = useFetchData(fetchAccountAgingConfig);
    const [reportData, setReportData] = useState<AccountAging[]>([]);
    
    useEffect(() => {
        if (accountAgingData) {
            const updatedReportData = (accountAgingData?.records || []) as AccountAging[];
            setReportData(updatedReportData.slice(0, 5));
        }
    }, [accountAgingData]);
    
    const navigate = useNavigate();
    const location = usePreviousLocation();
    const { setAddNewHandler } = useLayoutContext();
    const handleAddNewSale: ButtonClickFn<void> = useCallback((event) => {
        event?.preventDefault();
        navigate('/sale/management', { state: { from: location?.pathname } });
    }, [navigate, location]);
    
    useEffect(() => {
        setAddNewHandler(() => handleAddNewSale);
        return () => {
            setAddNewHandler(null);
        };
    }, [handleAddNewSale, setAddNewHandler]);
    
    
    return (
        <div className='d-flex flex-fill flex-column'>
            <StickyContainer>
                <StickyRow>
                    <div className='row'>
                        <div className='col-3 pr-md-0'>
                            <TextInput
                                type='date'
                                label='From'
                                name='startDate'
                                defaultValue={filterParams.startDate}
                                onChange={handleFilterParamsUpdate}
                                required
                            />
                        </div>
                        <div className='col-3 pl-md-0 pr-md-0'>
                            <TextInput
                                type='date'
                                label='To'
                                name='endDate'
                                defaultValue={filterParams.endDate}
                                onChange={handleFilterParamsUpdate}
                                required
                            />
                        </div>
                        <div className='col-6 pl-md-0'>
                            <SelectCustomer
                                label='Select customer'
                                datalistFilter={true}
                                selectedOption={filterParams.customerId}
                                onChange={(option) => {
                                    handleCustomerSelect('customerId', option as ReactSelectSingleOption);
                                }}
                            />
                        </div>
                    </div>
                </StickyRow>
            </StickyContainer>
            <div className='row'>
                <div className='col-md-8'>
                    <LabeledCard>
                        <CardLabel>Finances</CardLabel>
                        <AccountBalanceChart data={accountBalances} loading={accountBalancesLoading} />
                    </LabeledCard>
                </div>
                <div className='col-md-4'>
                    <LabeledCard>
                        <CardLabel>Tasks</CardLabel>
                        <TaskAnalytics data={taskAnalytics} loading={taskAnalyticsLoading} />
                    </LabeledCard>
                </div>
            </div>
            
            <div className='row'>
                <div className='col-md-12'>
                    <LabeledCard className='mb-3'>
                        <CardLabel>Account Balances</CardLabel>
                        <AccountAgingSummary loading={accountAgingLoading} reportData={reportData} />
                    </LabeledCard>
                </div>
            </div>
        </div>
    );
}
