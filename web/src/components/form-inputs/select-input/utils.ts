import React from 'react';

export const handleSelectFocus = (event: React.FocusEvent<HTMLInputElement>) => {
    const selectParent = event.target?.parentNode?.parentNode?.parentNode?.parentNode?.parentNode?.parentNode;
    if (selectParent) {
        (selectParent as HTMLDivElement).classList.add('focused');
    }
};

export const handleSelectBlur = (event: React.FocusEvent<HTMLInputElement>) => {
    const selectParent = event.target?.parentNode?.parentNode?.parentNode?.parentNode?.parentNode?.parentNode;
    if (selectParent) {
        (selectParent as HTMLDivElement).classList.remove('focused');
    }
};
