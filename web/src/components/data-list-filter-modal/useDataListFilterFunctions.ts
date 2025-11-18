import { useCallback, useState } from 'react';
import { ButtonClickFn, InputChangeFn, ReactSelectFn, ReactSelectSingleOption, SetState } from '../../types';

export const useDataListFilterFunctions = (setFilterParams: SetState<Record<string, any>>, resetFilterParams: () => void) => {
    const [filterModalOpen, setFilterModalOpen] = useState(false);
    const toggleFilterModal = useCallback(() => {
        setFilterModalOpen((filterModalOpened) => !filterModalOpened);
    }, []);
    
    const handleToggleFilterModal: ButtonClickFn<void> = useCallback((event) => {
        event.preventDefault();
        toggleFilterModal();
    }, [toggleFilterModal]);
    
    const handleClearFilterParams: ButtonClickFn<void> = useCallback((event) => {
        event.preventDefault();
        resetFilterParams();
        toggleFilterModal();
    }, [resetFilterParams, toggleFilterModal]);
    
    const handleSelectFilterOption: ReactSelectFn<void> = useCallback((optionName, option) => {
        const selectedOption = (option as ReactSelectSingleOption)?.value || '';
        setFilterParams((prevState) => ({ ...prevState, page: 1, [optionName]: selectedOption }));
    }, [setFilterParams]);
    
    const handlerFilterInputChange: InputChangeFn<HTMLInputElement, void> = useCallback((event) => {
        const { name, value } = event.currentTarget;
        setFilterParams((prevState) => ({ ...prevState, page: 1, [name]: value }));
    }, [setFilterParams]);
    
    return {
        filterModalOpen,
        handleClearFilterParams,
        handlerFilterInputChange,
        handleSelectFilterOption,
        handleToggleFilterModal,
        toggleFilterModal,
    };
};
