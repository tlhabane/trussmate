import React, { Suspense, lazy } from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { LayoutAuth, LayoutChild, LayoutPortal, LayoutSettings } from '../containers';
import { FullPageSpinner } from '../components';

const LoginPage = lazy(() => import('../pages/login'));
const ForgotPasswordPage = lazy(() => import('../pages/forgot-password'));
const HomePage = lazy(() => import('../pages/home'));
const InboxPage = lazy(() => import('../pages/inbox'));
const SalesPage = lazy(() => import('../pages/finance'));
const SaleManagementPage = lazy(() => import('../pages/sale-form'));
const SaleTaskManagementPage = lazy(() => import('../pages/sale-task-form'));
const CustomerPage = lazy(() => import('../pages/customers'));
const AddCustomerPage = lazy(() => import('../pages/customer-form'));
const AccountSettingsPage = lazy(() => import('../pages/account-settings'));
const BankAccountSettingsPage = lazy(() => import('../pages/bank-account-settings'));
const SaleSettingsPage = lazy(() => import('../pages/sale-settings'));
const SalesSettingManagementPage = lazy(() => import('../pages/sale-settings-form'));
const TeamSettingsPage = lazy(() => import('../pages/team-settings'));
const TaskSettingsPage = lazy(() => import('../pages/task-settings'));
const TaskMonitorPage = lazy(() => import('../pages/task-monitor'));

export const DefaultRouter = () => (
    <BrowserRouter>
        <Suspense fallback={<FullPageSpinner />}>
            <Routes>
                <Route path='/'>
                    <Route path='' element={<LayoutAuth />}>
                        <Route index element={<LoginPage />} />
                        <Route path='login' element={<LoginPage />} />
                        <Route path='forgot-password' element={<ForgotPasswordPage />} />
                    </Route>
                    <Route path='settings' element={<LayoutSettings />}>
                        <Route index element={<AccountSettingsPage />} />
                        <Route path='account' element={<AccountSettingsPage />} />
                        <Route path='bank' element={<BankAccountSettingsPage />} />
                        <Route path='sale/task/' element={<TaskSettingsPage />} />
                        <Route path='sale/process' element={<SaleSettingsPage />} />
                        <Route path='sale/monitor' element={<TaskMonitorPage />} />
                        <Route path='team' element={<TeamSettingsPage />} />
                    </Route>
                    
                    <Route path='' element={<LayoutPortal />}>
                        <Route path='home' element={<HomePage />} />
                        <Route path='inbox' element={<InboxPage />} />
                        <Route path='sales' element={<SalesPage />} />
                        <Route path='customers' element={<CustomerPage />} />
                    </Route>
                    <Route path='' element={<LayoutChild />}>
                        <Route index element={<AddCustomerPage />} />
                        <Route path='/customer/management' element={<AddCustomerPage />} />
                        <Route path='/customer/management/:customerId' element={<AddCustomerPage />} />
                        <Route path='/sale/process/management' element={<SalesSettingManagementPage />} />
                        <Route path='/sale/process/management/:workflowId' element={<SalesSettingManagementPage />} />
                        <Route path='/sale/management' element={<SaleManagementPage />} />
                        <Route path='/sale/management/:saleId' element={<SaleManagementPage />} />
                        <Route path='/sale/task/management/:saleTaskId' element={<SaleTaskManagementPage />} />
                    </Route>
                </Route>
            </Routes>
        </Suspense>
    </BrowserRouter>
);
