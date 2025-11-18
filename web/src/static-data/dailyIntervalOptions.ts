import { ReactSelectSingleOption } from '../types';

// Sequence generator function (commonly referred to as "range", cf. Python, Clojure, etc.)
const range = (start: number, stop: number, step: number) => (
    Array.from(
        { length: Math.ceil((stop - start) / step) },
        (_, i) => start + i * step,
    )
);

const getDailyIntervalOptions = (limit = 30) => {
    const intervalOptions: ReactSelectSingleOption[] = [];
    /*return Array.from({ length }, (_, dayCount) => {
        let label = dayCount === 1 ? `${dayCount} day` : `${dayCount} days`;
        if (dayCount === 0) {
            label = 'Same day';
        }
        
        return {
            label,
            value: dayCount,
        };
    }) as ReactSelectSingleOption[];*/
    let dayCount = 0;
    
    intervalOptions.push({
        value: dayCount,
        label: 'Same day',
    });
    
    do {
        dayCount += 1;
        intervalOptions.push({
            value: dayCount,
            label: dayCount === 1 ? `${dayCount} day` : `${dayCount} days`,
        });
    } while (dayCount < limit + 1);
    
    return intervalOptions;
};

export const dailyIntervalOptions = getDailyIntervalOptions(90);
