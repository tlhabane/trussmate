import { useCallback, useEffect, useRef, useState } from 'react';
import { ButtonClickFn, InputChangeFn, SetState } from '../types';

export const useSearchHandlers = (setFilterParams: SetState<Record<string, any>>, updateRecordsHandler?: () => void) => {
    const searchInputRef = useRef<HTMLInputElement | null>(null);
    const [searchValue, setSearchValue] = useState('');
    
    useEffect(() => {
        const searchTimer = setTimeout(() => {
            if (searchValue.trim() === '') {
                return;
            }
            updateRecordsHandler && updateRecordsHandler();
        }, 1500);
        
        return () => {
            clearTimeout(searchTimer);
        };
    }, [searchValue, updateRecordsHandler]);
    
    const handleSearchValueChange = useCallback<InputChangeFn<HTMLInputElement>>((event) => {
        setSearchValue(event.target.value);
        setFilterParams((prevState) => ({
            ...prevState,
            page: 1,
            search: event.target.value,
        }));
    }, [setFilterParams]);
    
    const handleClearSearch = useCallback<ButtonClickFn<void>>((event) => {
        event.preventDefault();
        const searchInputElement = searchInputRef.current as HTMLInputElement;
        if (searchInputElement?.value?.trim() !== '') {
            searchInputElement.value = '';
        }
        
        setFilterParams((prevState) => ({
            ...prevState,
            page: 1,
            search: '',
        }));
        updateRecordsHandler && updateRecordsHandler();
    }, [setFilterParams, updateRecordsHandler]);
    
    return { handleClearSearch, handleSearchValueChange, searchInputRef };
};
