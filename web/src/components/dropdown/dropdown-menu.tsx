import React from 'react';

export const DropdownMenu = React.forwardRef<HTMLDivElement, React.HTMLProps<HTMLDivElement>>((props, ref) => {
    const { children, style, className, 'aria-labelledby': labeledBy } = props;
    return (
        <div ref={ref} style={{ ...style }} className={className} aria-labelledby={labeledBy}>
            {React.Children.toArray(children)}
        </div>
    );
});
