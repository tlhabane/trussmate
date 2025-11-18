import React, { JSX, useEffect, useState } from 'react';
import { Outlet, useOutletContext } from 'react-router-dom';
import { HeaderChild } from '../header';
import { LayoutContainer } from '../layout.container';
import { SetState } from '../../../types';

export const LayoutChild: React.FC = (): JSX.Element => {
    const [previousLocation, setPreviousLocation] = useState('');
    const [exitLocation, setExitLocation] = useState('');
    useEffect(() => {
        return () => {
            setExitLocation('');
            setPreviousLocation('');
        };
    }, []);
    
    return (
        <LayoutContainer>
            <HeaderChild href={previousLocation || exitLocation} />
            <div className='content'>
                <Outlet context={{ setExitLocation, setPreviousLocation }} />
            </div>
        </LayoutContainer>
    );
};

type OutletContext = {
    setExitLocation: SetState<string>;
    setPreviousLocation: SetState<string>;
}

export function useLayoutChildContext() {
    return useOutletContext<OutletContext>();
}
