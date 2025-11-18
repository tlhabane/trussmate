import type { SingleValue, MultiValue } from 'react-select';

export interface ReactSelectSingleOption {
    readonly label: string;
    readonly value: any;
    readonly enabled?: boolean;
}

export interface ReactSelectGroupOption {
    readonly label: string;
    readonly options: ReactSelectSingleOption[];
}

export type ReactSelectOption = ReactSelectSingleOption | ReactSelectGroupOption;
export type ReactSelectEvent = SingleValue<ReactSelectSingleOption> | MultiValue<ReactSelectSingleOption>;
export type ReactSelectFn<TReturn = any> = (optionName: string, option: ReactSelectEvent) => TReturn;
