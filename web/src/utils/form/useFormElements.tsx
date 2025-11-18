import React, { useCallback } from 'react';
import { SelectInput, SignatureInput, TextArea, TextInput } from '../../components';
import { FormInput, InputChangeFn, InputFocusFn, ReactSelectFn, SignaturePadChangeFn } from '../../types';

type Handlers =  {
    onChange?: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, any>;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, any>;
    onFocus?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, any>;
    onSelect?: ReactSelectFn<any>;
    onSignature?: SignaturePadChangeFn<any>;
}

export function useFormElements<T extends Record<string, any>>() {
    const getSelectedOption = (options:Array<{ label: string; value: any}> = [], value?: any) => {
        return options.find((option) => option.value === value);
    };
    
    return useCallback((name: keyof T, props: FormInput<any>, handlers?: Handlers) => {
        const { type, ...rest } = props;
        
        const sharedProps = {
            name: name.toString(),
            label: rest.label || '',
            placeholder: rest.placeholder,
            error: rest.error,
            required: rest.required,
            className: rest.className || '',
            style: rest.style || {},
        };
        
        if (type === 'select') {
            const selectOptions = rest.options || [];
            const selectedOption = getSelectedOption(selectOptions, rest.value);
            
            return (
                <SelectInput
                    {...sharedProps}
                    key={name.toString()}
                    defaultValue={selectedOption}
                    options={selectOptions}
                    onChange={(option: any) => handlers?.onSelect?.(name.toString(), option)}
                />
            );
        }
    
        if (type === 'signature') {
            return (
                <SignatureInput {...sharedProps} key={name.toString()} onSignature={handlers?.onSignature} />
            );
        }
    
        const events = {
            onChange: (e: any) => handlers?.onChange?.(e),
            onBlur: (e: any) => handlers?.onBlur?.(e),
            onFocus: (e: any) => handlers?.onFocus?.(e),
        };
        
        const textInputProps = {
            ...sharedProps,
            ...events,
            value: rest.value,
        };
        
        if (type === 'textarea') {
            return (
                <TextArea
                    {...textInputProps}
                    type='text'
                    key={name.toString()}
                />
            );
        }
    
        return (
            <TextInput
                {...textInputProps}
                type={type || 'text'}
                key={name.toString()}
            />
        );
    }, [])
}
