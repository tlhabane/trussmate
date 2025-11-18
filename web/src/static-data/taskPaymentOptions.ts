import { ReactSelectSingleOption } from '../types';

const getPercentageOptions = (incrementStep = 1): ReactSelectSingleOption[] => {
    const percentageOptions: any[] = [];
    let value = 0;
    
    do {
        percentageOptions.push({
            value,
            label: `${value}%`,
        });
        value += incrementStep;
    } while (value < 101);
    
    return percentageOptions;
};

export const taskPaymentOptions = getPercentageOptions();
