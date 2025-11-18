import React from 'react';
import Select, { Props } from 'react-select';
import { ReactSelectSingleOption } from '../../types';

type RecordsPerPageSelectorType<Option> = Props<Option>;

export const RecordsPerPageSelector: React.FC<RecordsPerPageSelectorType<ReactSelectSingleOption>> = ({
  defaultValue,
  onChange,
  options,
}) => (
    <div
        style={{ width: '100%', height: '100%', display: 'none' }}
        className="d-flex flex-row align-items-center justify-content-around d-none"
    >
        <span className="small hint-text bold fs-13">Show up to</span>
        <Select isSearchable={false} defaultValue={defaultValue} onChange={onChange} options={options} />
        <span className="small hint-text bold fs-13">Items</span>
    </div>
);

