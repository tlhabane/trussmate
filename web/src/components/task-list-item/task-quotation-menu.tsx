import React, { JSX } from 'react';
import { Dropdown } from 'react-bootstrap';
import { HTMLElementClickFn } from '../../types';

type Props = {
    saleTaskId: string;
    taskEnabled?: boolean;
    downloadQuotation: HTMLElementClickFn<void>;
    previewQuotation: HTMLElementClickFn<void>;
    sendQuotation: HTMLElementClickFn<void>;
}

export const TaskQuotationMenu: React.FC<Props> = (props): JSX.Element => {
    const { saleTaskId, taskEnabled = false, downloadQuotation, previewQuotation, sendQuotation } = props;
    const smallIconSize = { style: { width: 16, height: 16 } };
    
    const thickerIcon = (strokeWidth: any) => ({
        style: { ...smallIconSize.style, strokeWidth },
    });
    
    return (
        <>
            <Dropdown.Item
                disabled={!taskEnabled}
                eventKey='approve'
                href='#'
            >
                <span>Approve Quotation</span>
                <i className='custom-icon icon check' {...smallIconSize} />
            </Dropdown.Item>
            <Dropdown.Divider />
            <Dropdown.Item
                eventKey='preview'
                href='#'
                disabled={!taskEnabled}
                onClick={previewQuotation}
                data-task={saleTaskId}
            >
                <span>Preview Quotation</span>
                <i className='custom-icon icon quick-view' {...thickerIcon(2)} />
            </Dropdown.Item>
            <Dropdown.Item
                eventKey='download'
                href='#'
                disabled={!taskEnabled}
                onClick={downloadQuotation}
                data-task={saleTaskId}
            >
                <span>Download Quotation</span>
                <i className='custom-icon icon download' {...smallIconSize} />
            </Dropdown.Item>
            <Dropdown.Divider />
            <Dropdown.Item
                eventKey='send'
                href='#'
                disabled={!taskEnabled}
                onClick={sendQuotation}
                data-task={saleTaskId}
            >
                <span>Send Quotation</span>
                <i className='custom-icon icon mail' {...smallIconSize} />
            </Dropdown.Item>
        </>
    );
};
