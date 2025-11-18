import React from 'react';

export const toggleListItem = (event: React.MouseEvent<HTMLButtonElement>) => {
    event.preventDefault();
    const toggleButton = event.currentTarget;
    
    const parentContainer = toggleButton?.parentElement?.offsetParent || toggleButton?.parentElement;
    const datalistContainer = parentContainer as HTMLDivElement;
    
    const buttonContent = toggleButton.querySelector('.btn-content');
    
    if (datalistContainer && event.currentTarget?.classList.contains('active')) {
        datalistContainer.classList.remove('active');
        toggleButton?.classList.remove('active');
        
        let preferredIcon = toggleButton?.dataset.icon;
        if (typeof preferredIcon === 'undefined') {
            preferredIcon = 'Chevron-Down';
        }
        
        if (buttonContent) {
            buttonContent?.children[0]?.setAttribute('aria-label', preferredIcon);
            return;
        }
        
        event.currentTarget?.children[0]?.setAttribute('aria-label', preferredIcon);
        return;
    }
    
    datalistContainer?.classList.add('active');
    toggleButton?.classList.add('active');
    
    if (buttonContent) {
        buttonContent?.children[0]?.setAttribute('aria-label', 'Close');
        return;
    }
    toggleButton?.children[0]?.setAttribute('aria-label', 'Close');
};
