import React, { JSX, useMemo } from 'react';
import { ContainerSpinner } from '../../components';
import { useBarChart } from '../../hooks';

type Props = {
    data: Record<string, any>[];
    loading: boolean;
};

export const AccountBalanceChart: React.FC<Props> = ({ data, loading }): JSX.Element => {
    const chartData = useMemo(() => {
        return data.map((item) => ({
            invoiceMonth: item.invoiceMonth,
            saleTotal: item.saleTotal,
            invoiceBalance: item.invoiceBalance,
            overdueInvoiceBalance: item.overdueInvoiceBalance,
            paymentTotal: item.paymentTotal,
        }));
    }, [data]);
    
    const BarChart = useBarChart(chartData, '400px', '100%');
    
    return (
        <div style={{ width: '100%', height: '400px' }}>
            {loading && (
                <ContainerSpinner
                    style={{ position: 'absolute', opacity: 0.45, minHeight: '100%', height: '100%' }}
                />
            )}
            {BarChart}
        </div>
    );
};
