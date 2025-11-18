import React, { JSX } from 'react';
import { ContainerSpinner, EmptyListContainer } from '../../components';
import { ReportBody } from '../account-aging/report-body';
import { ReportFooter } from '../account-aging/report-footer';
import { ReportHeader } from '../account-aging/report-header';
import { AccountAging } from '../../models';

type Props = {
    loading: boolean;
    reportData: AccountAging[];
}

export const AccountAgingSummary: React.FC<Props> = ({ loading, reportData }): JSX.Element => {
    return (
        <>
            {loading && (
                <ContainerSpinner
                    style={{ position: 'absolute', opacity: 0.45, minHeight: '100%', height: '100%' }}
                />
            )}
            {reportData.length === 0 && (
                <div className='row p-5'>
                    <div className='col-md-6 offset-md-3'>
                        <EmptyListContainer>
                            <i className='custom-icon icon bar-chart' style={{ width: 48, height: 48 }} />
                            <p className='hint-text text-center mt-2'>
                                No customer account balances currently available.
                            </p>
                        </EmptyListContainer>
                    </div>
                </div>
            )}
            
            {reportData.length > 0 && (
                <>
                    <ReportHeader />
                    <ReportBody reportData={reportData} />
                    <ReportFooter reportData={reportData} />
                </>
            )}
        </>
    );
};
