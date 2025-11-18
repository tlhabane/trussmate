import React, { JSX } from 'react';
import { ListItemContainer, ListItemHeader } from '../../components';
import { formatPrice } from '../../utils';
import { AccountAging } from '../../models';

type Props = {
    reportData: AccountAging[];
};
export const ReportFooter: React.FC<Props> = ({ reportData }): JSX.Element => {
    const ninetyDays = formatPrice(reportData.reduce((total, item) => total + item.days90 + item.days120 + item.days150 + item.days180, 0), '');
    const sixtyDays = formatPrice(reportData.reduce((total, item) => total + item.days60, 0), '');
    const thirtyDays = formatPrice(reportData.reduce((total, item) => total + item.days30, 0), '');
    const current = formatPrice(reportData.reduce((total, item) => total + item.current, 0), '');
    const total = formatPrice(reportData.reduce((total, item) => total + item.totalBalance, 0), '');
    
    return (
        <ListItemContainer className='no-hover'>
            <ListItemHeader className='flattened bg-dark text-white'>
                <div className='col-2 font-weight-bold d-flex align-items-center'>Total</div>
                <div className='col-2 font-weight-bold d-flex align-items-center justify-content-end'>{ninetyDays}</div>
                <div className='col-2 font-weight-bold d-flex align-items-center justify-content-end'>{sixtyDays}</div>
                <div className='col-2 font-weight-bold d-flex align-items-center justify-content-end'>{thirtyDays}</div>
                <div className='col-2 font-weight-bold d-flex align-items-center justify-content-end'>{current}</div>
                <div className='col-2 font-weight-bold d-flex align-items-center justify-content-end'>{total}</div>
            </ListItemHeader>
        </ListItemContainer>
    );
};
