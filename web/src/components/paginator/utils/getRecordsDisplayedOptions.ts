import { ReactSelectSingleOption } from "../../../types";

const initialRecordsPerPageOptions: ReactSelectSingleOption[] = [
    {
        value: '5',
        label: '5',
    },
    {
        value: '10',
        label: '10',
    },
    {
        value: '25',
        label: '25',
    },
    {
        value: '50',
        label: '50',
    },
];

export const getRecordsDisplayedOptions = (totalRecords: number) => {
    let updatedOptions: ReactSelectSingleOption[] = [];
    if (totalRecords > 0) {
        updatedOptions = initialRecordsPerPageOptions.filter((limit) => +limit.value <= totalRecords);
    }
    
    return updatedOptions;
};
