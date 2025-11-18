import { STORAGE_KEY } from '../config';

export const saveToLocalStorage = (state: any) => {
    try {
        const serializedState = JSON.stringify(state);
        localStorage.setItem(STORAGE_KEY, serializedState);
    } catch (error) {
        console.error('Could not save state to local storage:', error);
    }
};

export const loadFromLocalStorage = () => {
    try {
        const serializedState = localStorage.getItem(STORAGE_KEY);
        if (serializedState) {
            return JSON.parse(serializedState);
        }
        return undefined;
    } catch (error) {
        console.error('Could not load state from local storage:', error);
        return undefined; // Return undefined in case of error
    }
};

export const clearLocalStorage = () => {
    try {
        localStorage.removeItem(STORAGE_KEY);
    } catch (error) {
        console.error('Could not clear local storage:', error);
    }
};
