import React, { JSX } from 'react';
import { Dropdown } from 'react-bootstrap';
import { HTMLElementClickFn } from '../../types';

type Props = {
    transactionActionHandler: HTMLElementClickFn<void>;
    transactionId: string;
};

export const PaymentMenu: React.FC<Props> = ({ transactionActionHandler, transactionId }): JSX.Element => {
    const smallIconSize = { style: { width: 16, height: 16 } };
    
    const thickerIcon = (strokeWidth: any) => ({
        style: { ...smallIconSize.style, strokeWidth },
    });
    const smallWhiteIcon = {
        style: {
            ...smallIconSize.style,
            backgroundColor: '#fff',
        },
    };
    
    return (
        <>
            <Dropdown.Item
                eventKey='preview'
                href='#'
                data-transaction={transactionId}
                onClick={transactionActionHandler}
                data-url='/transaction/download'
                data-action='preview'
            >
                <span>Preview Receipt</span>
                <i className='custom-icon icon quick-view' {...thickerIcon(2)} />
            </Dropdown.Item>
            <Dropdown.Item
                eventKey='download'
                href='#'
                onClick={transactionActionHandler}
                data-url='/transaction/download'
                data-action='download'
            >
                <span>Download Receipt</span>
                <i className='custom-icon icon download' {...smallIconSize} />
            </Dropdown.Item>
            <Dropdown.Divider />
            <Dropdown.Item
                eventKey='send'
                href='#'
                onClick={transactionActionHandler}
                data-url='/transaction/send'
                data-action='send'
            >
                <span>Send Receipt</span>
                <i className='custom-icon icon mail' {...smallIconSize} />
            </Dropdown.Item>
            <Dropdown.Divider />
            <Dropdown.Item
                className='bg-danger text-white'
                eventKey='delete'
                href='#'
            >
                <span>Cancel payment</span>
                <i className='custom-icon icon return' {...smallWhiteIcon} />
            </Dropdown.Item>
        </>
    );
};
