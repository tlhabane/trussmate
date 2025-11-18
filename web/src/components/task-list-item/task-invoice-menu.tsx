import React, { JSX } from 'react';
import { Dropdown } from 'react-bootstrap';
import { HTMLElementClickFn } from '../../types';

type Props = {
    saleTaskId: string;
    taskEnabled?: boolean;
    downloadInvoice: HTMLElementClickFn<void>;
    previewInvoice: HTMLElementClickFn<void>;
    sendInvoice: HTMLElementClickFn<void>;
}
export const TaskInvoiceMenu: React.FC<Props> = (props): JSX.Element => {
    const { saleTaskId, taskEnabled = false, downloadInvoice, previewInvoice, sendInvoice } = props;
    const smallIconSize = { style: { width: 16, height: 16 } };
    
    const thickerIcon = (strokeWidth: any) => ({
        style: { ...smallIconSize.style, strokeWidth },
    });
    
    return (
        <>
            <Dropdown.Item
                eventKey='preview'
                href='#'
                disabled={!taskEnabled}
                onClick={previewInvoice}
                data-task={saleTaskId}
            >
                <span>Preview Invoice</span>
                <i className='custom-icon icon quick-view' {...thickerIcon(2)} />
            </Dropdown.Item>
            <Dropdown.Item
                eventKey='download'
                href='#'
                disabled={!taskEnabled}
                onClick={downloadInvoice}
                data-task={saleTaskId}
            >
                <span>Download Invoice</span>
                <i className='custom-icon icon download' {...smallIconSize} />
            </Dropdown.Item>
            <Dropdown.Divider />
            <Dropdown.Item
                eventKey='send'
                href='#'
                disabled={!taskEnabled}
                onClick={sendInvoice}
                data-task={saleTaskId}
            >
                <span>Send Invoice</span>
                <i className='custom-icon icon mail' {...smallIconSize} />
            </Dropdown.Item>
        </>
    );
};
