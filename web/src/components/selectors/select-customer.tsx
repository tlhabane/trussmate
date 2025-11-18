import React, { JSX, useEffect, useState, useMemo } from 'react';
import { Props } from 'react-select';
import { SelectInput } from '../form-inputs';
import { useFetchData } from '../../utils';
import { ReactSelectSingleOption, ReactSelectOption } from '../../types';
import { CustomerList } from '../../models';
import { getSelectedOption } from './getSelectedOption';
import { ONE_MINUTE } from '../../config';

interface SelectProps<Option> extends Props<Option> {
    label?: string;
    datalistFilter?: boolean;
    selectedOption?: string | null;
}

export const SelectCustomer: React.FC<SelectProps<ReactSelectOption>> = (props): JSX.Element => {
    const { datalistFilter, label, selectedOption, name, onChange, placeholder } = props;
    
    const fetchConfig = useMemo(() => ({
        url: '/customer',
        queryKey: ['customers'],
        refetchInterval: 15 * ONE_MINUTE, // 5 minutes
        staleTime: 14.5 * ONE_MINUTE, // 4.5 minutes
    }), []);
    const { data, isLoading, isFetching } = useFetchData(fetchConfig);
    const [customerOptions, setCustomerOptions] = useState<ReactSelectSingleOption[]>([]);
    
    useEffect(() => {
        if (data) {
            const updatedCustomerList = (data?.records || []) as CustomerList[];
            const options = updatedCustomerList.map(({ customerId, customerName }) => ({
                label: customerName,
                value: customerId,
            }));
            
            let customerListOptions = options;
            if (datalistFilter) {
                customerListOptions = [{ value: '0', label: 'All Customers' }, ...options];
            }
            
            setCustomerOptions(customerListOptions);
        }
    }, [datalistFilter, data]);
    
    const selectedValue = getSelectedOption(customerOptions, selectedOption);
    
    return (
        <SelectInput
            name={name || 'customerId'}
            label={label || 'Select customer'}
            placeholder={placeholder || 'Select customer name...'}
            onChange={onChange}
            options={customerOptions}
            isDisabled={isLoading || isFetching}
            isLoading={isLoading || isFetching}
            defaultValue={selectedValue}
        />
    );
};
