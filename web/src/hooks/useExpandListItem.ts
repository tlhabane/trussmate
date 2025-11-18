import { useCallback, useState } from 'react';
import { ButtonClickFn } from '../types';

export const useExpandListItem = () => {
    const [listItemViewState, setListItemViewState] = useState<Record<string, boolean>>({})
    
    const expandListItem =  useCallback<ButtonClickFn<void>>((event) => {
        event.preventDefault();
        const { toggle } = event.currentTarget.dataset;
        if (toggle) {
            setListItemViewState((prevState) => {
                const updatedState = Object.keys(prevState).reduce((acc, key) => {
                    acc[key] = false;
                    return acc;
                }, {} as Record<string, boolean>);
                updatedState[toggle] = !prevState[toggle];
                return updatedState;
            });
        }
    }, []);
    
    return { expandListItem, listItemViewState };
};
