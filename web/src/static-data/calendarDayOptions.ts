import { ReactSelectSingleOption } from '../types';

const getSuffix = (calendarDay = 0) => {
    let suffix = '';
    if (calendarDay > 0) {
        const lastDigit = parseInt(calendarDay.toString().charAt(calendarDay.toString().length - 1), 10);
        if (lastDigit === 1 && calendarDay > 11) {
            suffix = 'st';
        } else if (lastDigit === 2) {
            suffix = 'nd';
        } else if (lastDigit === 3) {
            suffix = 'rd';
        } else {
            suffix = 'th';
        }
    }
    return suffix;
};

const getCalendarDayOptions = () => {
    let dayCount = 1;
    const calendarDayOptions: ReactSelectSingleOption[] = [];
    do {
        const suffix = getSuffix(dayCount);
        calendarDayOptions.push({
            value: dayCount,
            label: dayCount === 1 ? `(${dayCount}) day` : `${dayCount}${suffix} day`,
        });
        dayCount += 1;
    } while (dayCount < 31);
    
    calendarDayOptions.push({
        value: 31,
        label: 'last day',
    });
    
    return calendarDayOptions;
};

export const calendarDayOptions = getCalendarDayOptions();
