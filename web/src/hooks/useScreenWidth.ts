import { useState, useEffect } from 'react';

export const useScreenWidth = (currentScreenWidth = window.innerWidth) => {
    const [screenWidth, setScreenWidth] = useState(currentScreenWidth);

    useEffect(() => {
        const updateScreenWidth = () => {
            setScreenWidth(window.innerWidth);
        };

        window.addEventListener('resize', updateScreenWidth);
        return () => {
            window.removeEventListener('resize', updateScreenWidth);
        };
    }, []);

    return screenWidth;
};
