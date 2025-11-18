import React, { JSX } from 'react';
import { SelectCustomer } from '../selectors';
import { TextInput } from '../form-inputs';
import { ModalFilter } from '../../containers';
import { ButtonClickFn, InputChangeFn, ReactSelectFn, ReactSelectSingleOption } from '../../types';

interface Props extends React.HTMLProps<HTMLDivElement> {
    filterParams: Record<string, any>;
    filterInputChangeFn: InputChangeFn<HTMLInputElement, void>;
    filterParamSelectFn: ReactSelectFn<void>;
    handleClearListFilter: ButtonClickFn<void>;
    handleToggleModal: () => void;
    modalOpened: boolean;
}

export const DataListFilterModal: React.FC<Props> = (props): JSX.Element => {
    const {
        children,
        filterParams,
        filterInputChangeFn,
        filterParamSelectFn,
        handleClearListFilter,
        handleToggleModal,
        modalOpened,
    } = props;
    
    return (
        <ModalFilter
            clearDataFilterParamsHandler={handleClearListFilter}
            dismissModalDataFilter={handleToggleModal}
            openModalDataFilter={modalOpened}
        >
            <div className='row'>
                <div className='col-md-6 pr-md-0'>
                    <TextInput
                        type='date'
                        label='From'
                        name='startDate'
                        defaultValue={filterParams.startDate}
                        onChange={filterInputChangeFn}
                        required
                    />
                </div>
                <div className='col-md-6 pl-md-0'>
                    <TextInput
                        type='date'
                        label='To'
                        name='endDate'
                        defaultValue={filterParams.endDate}
                        onChange={filterInputChangeFn}
                        required
                    />
                </div>
            </div>
            <SelectCustomer
                name='customerId'
                datalistFilter={true}
                selectedOption={filterParams.customerId}
                onChange={(option) => {
                    filterParamSelectFn('customerId', option as ReactSelectSingleOption);
                }}
            />
            {children}
        </ModalFilter>
    );
};
