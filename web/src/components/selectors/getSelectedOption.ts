import type { ReactSelectSingleOption } from '../../types';

export const getSelectedOption = (
    options: ReactSelectSingleOption[],
    selectedOption?: string | null,
    defaultOption?: number,
) => {
    const userSelectedValue = options.find((option) => option.value === (selectedOption?.toString() || ''));
    
    return userSelectedValue || options[defaultOption || 0];
};
