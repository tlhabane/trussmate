import React from 'react';
import { Button } from '../button';

interface Props extends React.HTMLProps<HTMLButtonElement> {
    edit?: boolean;
    reload?: boolean;
}

export const EmptyNoticeButton: React.FC<Props> = (props) => {
    const { children, disabled, edit, reload, ...rest } = props;
    
    let iconClass: string;
    
    if (reload) {
        iconClass = 'refresh';
    } else if (edit) {
        iconClass = 'edit';
    } else {
        iconClass = 'plus-circle';
    }
    
    return (
        <Button
            {...rest}
            className={`btn-${disabled ? 'default' : 'outline-success'} btn-block btn-rounded`}
            disabled={disabled}
        >
            <i className={`custom-icon icon left-icon ${iconClass}`} />
            {children || 'Add New'}
        </Button>
    );
};
