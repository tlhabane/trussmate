import React from 'react';
import Sticky from 'react-stickynode';
import { useScreenWidth } from '../../hooks';

export const StickyContainer: React.FC<React.HTMLProps<HTMLDivElement>> = ({ children }) => {
    const screenWidth = useScreenWidth();

    const handleStateChange = (status: any) => {
        if (status.status === Sticky.STATUS_FIXED) {
            console.log('the component is sticky');
        }
    };

    return (
        <Sticky
            top={60}
            enabled={screenWidth >= 594}
            innerActiveClass="fixed"
            innerZ={1045}
            onStateChange={handleStateChange}
        >
            {children}
        </Sticky>
    );
};
