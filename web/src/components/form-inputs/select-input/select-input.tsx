import React from 'react';
import Select, { Props } from 'react-select';
import { SelectOptionType } from './types';
import { Styles } from './config';
import { handleSelectBlur, handleSelectFocus } from './utils';

interface SelectProps<Option> extends Props<Option> {
    label: string;
    error?: string;
    required?: boolean;
}

export const SelectInput: React.FC<SelectProps<SelectOptionType>> = ({ error, label, defaultValue, required, ...props }, ...rest) => {
    const customSelectStyles = Styles<SelectOptionType>();
    const hasError = Boolean(error);
    return (
        <>
            <div className={`form-group form-group-react-select${(hasError && ' has-error') || ''}${required ? ' required' : ''}`}>
                {/* eslint-disable-next-line jsx-a11y/label-has-associated-control */}
                <label>{label}</label>
                <div className="controls">
                    <Select
                        /* eslint-disable-next-line react/jsx-props-no-spreading */
                        {...rest}
                        /* eslint-disable-next-line react/jsx-props-no-spreading */
                        {...props}
                        value={defaultValue}
                        styles={customSelectStyles}
                        onFocus={handleSelectFocus}
                        onBlur={handleSelectBlur}
                        menuPortalTarget={document.body}
                        menuPosition="fixed"
                        className={`react-select-container${(hasError && ' has-error') || ''}`}
                    />
                    {hasError && <label className="error">{error}</label>}
                </div>
            </div>
            
        </>
    );
};
