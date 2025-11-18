import React, { JSX } from 'react';
import { ListItemContainer, ListItemHeader } from '../../components';

export const ReportHeader = (): JSX.Element => (
    <ListItemContainer className='no-hover'>
        <ListItemHeader className='flattened bg-dark text-white'>
            <div className='col-2 font-weight-bold d-flex align-items-center'>Customer Name</div>
            <div className='col-2 font-weight-bold d-flex align-items-center justify-content-end'>90+ Days</div>
            <div className='col-2 font-weight-bold d-flex align-items-center justify-content-end'>60 Days</div>
            <div className='col-2 font-weight-bold d-flex align-items-center justify-content-end'>30 Days</div>
            <div className='col-2 font-weight-bold d-flex align-items-center justify-content-end'>Current</div>
            <div className='col-2 font-weight-bold d-flex align-items-center justify-content-end'>Total</div>
        </ListItemHeader>
    </ListItemContainer>
);
