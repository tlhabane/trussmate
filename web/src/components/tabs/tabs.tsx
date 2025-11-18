import React from 'react';
import { Link } from 'react-router-dom';

import { Tab } from './types';
import { onTabClick } from './utils';

interface TabType extends React.HTMLProps<HTMLUListElement> {
    tabs: Tab[];
    getActiveTabId?: (activeTabId: string) => void;
}

export const Tabs: React.FC<TabType> = ({ className, style, getActiveTabId, tabs }) => {
    const handleTabClick = (event: React.MouseEvent<HTMLAnchorElement>) => {
        const activeTab = onTabClick(event);
        if (getActiveTabId) {
            getActiveTabId(activeTab);
        }
    };

    return (
        <ul className={`nav ${className || ''}`} style={style}>
            {tabs.map((tab) => (
                <li className={`nav-item ${tab.align || ''}`} key={tab.id}>
                    <Link to={`/${tab.id}`} onClick={handleTabClick} className={`${tab.active ? 'active' : ''}`}>
                        {tab.title}
                    </Link>
                </li>
            ))}
        </ul>
    );
};
