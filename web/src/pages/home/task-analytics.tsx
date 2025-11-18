import React, { JSX, useMemo } from 'react';
import { ContainerSpinner } from '../../components';
import { useRadarChart } from '../../hooks';

type Props = {
    data: Record<string, any>[];
    loading: boolean;
};

export const TaskAnalytics: React.FC<Props> = ({ data, loading }): JSX.Element => {
    const chartData = useMemo(() => {
        const allTasks = data.reduce((total, item) => total + item.taskCount, 0);
        const completedTasks = data.reduce((total, item) => total + item.completed, 0);
        const pendingTasks = data.reduce((total, item) => total + item.pending, 0);
        const overdueTasks = data.reduce((total, item) => total + item.overdue, 0);
        return [
            { title: 'Completed', total: (completedTasks / allTasks) * 100 },
            { title: 'Pending', total: (pendingTasks / allTasks) * 100 },
            { title: 'Overdue', total: (overdueTasks / allTasks) * 100 },
        ];
    }, [data]);
    
    const RadarChart = useRadarChart(chartData);
    
    return (
        <div style={{ width: '100%', height: '400px' }}>
            {loading && (
                <ContainerSpinner
                    style={{ position: 'absolute', opacity: 0.45, minHeight: '100%', height: '100%' }}
                />
            )}
            {RadarChart}
        </div>
    );
};
