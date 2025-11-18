import React, { JSX } from 'react';
import { Dropdown } from 'react-bootstrap';
import { HTMLElementClickFn } from '../../types';

type Props = {
    invoiceActionHandler: HTMLElementClickFn<void>;
    transactionId: string;
};

export const InvoiceMenu: React.FC<Props> = ({
                                                 invoiceActionHandler,
                                                 transactionId,
                                             }): JSX.Element => {
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
                onClick={invoiceActionHandler}
                data-url='/invoice/download'
                data-action='preview'
            >
                <span>Preview Invoice</span>
                <i className='custom-icon icon quick-view' {...thickerIcon(2)} />
            </Dropdown.Item>
            <Dropdown.Item
                eventKey='download'
                href='#'
                data-transaction={transactionId}
                onClick={invoiceActionHandler}
                data-url='/invoice/download'
                data-action='download'
            >
                <span>Download Invoice</span>
                <i className='custom-icon icon download' {...smallIconSize} />
            </Dropdown.Item>
            <Dropdown.Divider />
            <Dropdown.Item
                eventKey='send'
                href='#'
                data-transaction={transactionId}
                onClick={invoiceActionHandler}
                data-url='/invoice/send'
                data-action='send'
            >
                <span>Send Invoice</span>
                <i className='custom-icon icon mail' {...smallIconSize} />
            </Dropdown.Item>
            <Dropdown.Divider />
            <Dropdown.Item
                className='bg-danger text-white'
                eventKey='delete'
                href='#'
            >
                <span>Cancel Invoice</span>
                <i className='custom-icon icon trash' {...smallWhiteIcon} />
            </Dropdown.Item>
        </>
    );
};
