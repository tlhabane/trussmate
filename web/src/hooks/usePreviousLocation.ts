import { Location, useLocation } from 'react-router-dom';
import { useEffect, useRef } from 'react';

export const usePreviousLocation = () => {
    const location = useLocation();
    const prevLocRef = useRef<Location<any> | null>(null);
    
    useEffect(() => {
        prevLocRef.current = location;
    }, [location]);
    
    return prevLocRef.current;
};
