import React, { JSX } from 'react';
import { Dropdown } from 'react-bootstrap';
import { DropdownMenu, DropdownToggle } from '../dropdown';
import { TaskInvoiceMenu } from './task-invoice-menu';
import { TaskQuotationMenu } from './task-quotation-menu';
import { SaleTaskList, TaskStatus } from '../../models';
import { HTMLElementClickFn } from '../../types';

type Props = {
    task: SaleTaskList;
    payInvoice: HTMLElementClickFn<void>;
    paymentButtonDisabled: boolean;
    downloadInvoice: HTMLElementClickFn<void>;
    menuDisabled: boolean;
    previewInvoice: HTMLElementClickFn<void>;
    sendInvoice: HTMLElementClickFn<void>;
    downloadQuotation: HTMLElementClickFn<void>;
    previewQuotation: HTMLElementClickFn<void>;
    sendQuotation: HTMLElementClickFn<void>;
    uploadEstimate?: HTMLElementClickFn<void>;
}

export const TaskOptionsMenu: React.FC<Props> = (props): JSX.Element => {
    const {
        task,
        payInvoice,
        paymentButtonDisabled,
        downloadInvoice,
        menuDisabled,
        previewInvoice,
        sendInvoice,
        downloadQuotation,
        previewQuotation,
        sendQuotation,
        uploadEstimate,
    } = props;
    const smallIconSize = { style: { width: 16, height: 16 } };
    const smallWhiteIcon = {
        style: {
            ...smallIconSize.style,
            backgroundColor: '#fff',
        },
    };
    
    /*const thickerIcon = (strokeWidth: any) => ({
        style: { ...smallIconSize.style, strokeWidth },
    });*/
    
    const quotation = task.taskAction === 'quotation';
    const invoice = task.taskAction.search('invoice') > -1;
    const payment = task.taskPayment > 0;
    const simpleTask = task.taskAction === 'task';
    const quotationTask = task.taskAction === 'estimate';
    
    const startButtonDisabled = (
        !task.taskEnabled || (
            task.taskStatus !== TaskStatus.PENDING &&
            task.taskStatus !== TaskStatus.TENTATIVE
        )
    );
    
    return (
        <Dropdown className='mr-1'>
            <Dropdown.Toggle
                as={DropdownToggle as React.ElementType}
                className='btn btn-link bg-transparent border-0 no-text profile-dropdown-toggle'
                disabled={menuDisabled}
            />
            <Dropdown.Menu className='profile-dropdown' align='end' as={DropdownMenu as React.ElementType}>
                {payment && (
                    <Dropdown.Item
                        
                        eventKey='pay'
                        href='#'
                        onClick={payInvoice}
                        data-task={task.saleTaskId}
                        disabled={paymentButtonDisabled}
                    >
                        <span>Pay</span>
                        <i className='custom-icon icon check' {...smallIconSize} />
                    </Dropdown.Item>
                )}
                {quotationTask && (
                    <Dropdown.Item
                        disabled={startButtonDisabled}
                        eventKey='upload'
                        href='#'
                        data-task={task.saleTaskId}
                        onClick={uploadEstimate}
                    >
                        <span>Upload Estimate</span>
                        <i className='custom-icon icon upload' {...smallIconSize} />
                    </Dropdown.Item>
                )}
                {simpleTask && !payment && !quotationTask && (
                    <Dropdown.Item
                        disabled={startButtonDisabled}
                        eventKey='start'
                        href='#'
                        data-task={task.saleTaskId}
                    >
                        <span>Start Task</span>
                        <i className='custom-icon icon check' {...smallIconSize} />
                    </Dropdown.Item>
                )}
                {quotation && (
                    <TaskQuotationMenu
                        saleTaskId={task.saleTaskId}
                        taskEnabled={task?.taskEnabled || false}
                        downloadQuotation={downloadQuotation}
                        previewQuotation={previewQuotation}
                        sendQuotation={sendQuotation}
                    />
                )}
                {invoice && (
                    <TaskInvoiceMenu
                        saleTaskId={task.saleTaskId}
                        taskEnabled={task?.taskEnabled || false}
                        downloadInvoice={downloadInvoice}
                        previewInvoice={previewInvoice}
                        sendInvoice={sendInvoice}
                    />
                )}
                <Dropdown.Divider />
                <Dropdown.Item
                    disabled={!task.taskEnabled}
                    eventKey='update'
                    href='#'
                >
                    <span>Update</span>
                    <i className='custom-icon icon edit' {...smallIconSize} />
                </Dropdown.Item>
                <Dropdown.Divider />
                
                <Dropdown.Item
                    className='bg-danger text-white'
                    eventKey='delete'
                    href='#'
                    disabled={!task.taskEnabled}
                >
                    <span>Cancel Task</span>
                    <i className='custom-icon icon trash' {...smallWhiteIcon} />
                </Dropdown.Item>
            </Dropdown.Menu>
        </Dropdown>
    );
};
