import React from 'react';

export const LayoutContainer = React.forwardRef<HTMLElement, React.HTMLProps<HTMLElement>>(({ children, className, ...rest }, ref) => (
    <main {...rest} className={className || ''} ref={ref}>
        {children}
    </main>
));
