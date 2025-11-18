import React, { JSX } from 'react';
import { ListItemContainer, ListItemHeader } from '../../components';
import { formatPrice } from '../../utils';
import { AccountAging } from '../../models';

type Props = {
    reportData: AccountAging[];
};
export const ReportBody: React.FC<Props> = ({ reportData }): JSX.Element => (
    <>
        {reportData.map((item) => (
            <ListItemContainer key={`${item.customerId}-${item.customerName}`} className='striped no-hover'>
                <ListItemHeader>
                    <div className='col-2 d-flex align-items-center'>{item.customerName}</div>
                    <div className='col-2 d-flex align-items-center justify-content-end'>
                        {formatPrice(item.days90 + item.days120 + item.days150 + item.days180, '')}
                    </div>
                    <div className='col-2 d-flex align-items-center justify-content-end'>
                        {formatPrice(item.days60, '')}
                    </div>
                    <div className='col-2 d-flex align-items-center justify-content-end'>
                        {formatPrice(item.days30, '')}
                    </div>
                    <div className='col-2 d-flex align-items-center justify-content-end'>
                        {formatPrice(item.current, '')}
                    </div>
                    <div className='col-2 d-flex align-items-center justify-content-end'>
                        {formatPrice(item.totalBalance, '')}
                    </div>
                </ListItemHeader>
            </ListItemContainer>
        ))}
    </>
);
