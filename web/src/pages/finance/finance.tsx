import React, { JSX, useState } from 'react';
import { Tabs, Tab } from '../../components';
import Sales from '../sales';
import Transactions from '../transactions';
import AccountAging from '../account-aging';

interface FinanceTab extends Tab {
    component: JSX.Element;
}

const financePageTabs: FinanceTab[] = [
    {
        id: `saleListTab`,
        title: 'Sales',
        active: true,
        component: <Sales />,
    },
    {
        id: `transactionListTab`,
        title: 'Transactions',
        active: false,
        component: <Transactions />,
    },
    {
        id: `accountAgingTab`,
        title: 'Accounts Aging',
        active: false,
        component: <AccountAging />,
    },
];

export default function Finance(): JSX.Element {
    const [activeTab, setActiveTab] = useState('saleListTab');
    const getActiveTabId = (tabId: string) => {
        setActiveTab(tabId);
    };
    
    const tabs = financePageTabs.map(({ id, title, active }) => ({ id, title, active }));
    return (
        <>
            <Tabs
                tabs={tabs}
                className='nav-tabs nav-tabs-simple nav-tabs-info header-tabs'
                getActiveTabId={getActiveTabId}
            />
            <div className='flex-fill'>
                <div className='tab-content px-0 pb-0'>
                    {financePageTabs.map((tab) => (
                        <div
                            key={tab.id}
                            className={`tab-pane fade ${tab.id === activeTab ? 'active show' : ''}`}
                            id={tab.id}
                        >
                            {tab.id === activeTab && tab.component}
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}
