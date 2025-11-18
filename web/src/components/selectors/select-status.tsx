import React, { JSX } from 'react';
import { Props } from 'react-select';
import { SelectInput } from '../form-inputs';
import { capitalizeFirstLetter } from '../../utils';
import { ReactSelectSingleOption, ReactSelectOption } from '../../types';
import { SaleStatus, TaskStatus } from '../../models';
import { getSelectedOption } from './getSelectedOption';

interface SelectProps<Option> extends Props<Option> {
    label?: string;
    datalistFilter?: boolean;
    selectedOption?: string | null;
}

export const SelectStatus: React.FC<SelectProps<ReactSelectOption>> = (props): JSX.Element => {
    const { datalistFilter, label, selectedOption, name, onChange, placeholder } = props;
    const initOptions = { ...SaleStatus, TaskStatus };
    const options: ReactSelectSingleOption[] = Object.values(initOptions).map((option) => {
        const value = option.toString();
        return {
            label: capitalizeFirstLetter(value),
            value,
        };
    });
    
    let statusOptions = options;
    if (datalistFilter) {
        statusOptions = [{ value: '0', label: 'All' }, ...options];
    }
    
    const selectedValue = getSelectedOption(statusOptions, selectedOption);
    
    return (
        <SelectInput
            name={name || 'status'}
            label={label || 'Status'}
            placeholder={placeholder || 'Select status...'}
            onChange={onChange}
            options={statusOptions}
            defaultValue={selectedValue}
        />
    );
};
