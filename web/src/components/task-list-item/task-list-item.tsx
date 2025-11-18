import React, { JSX } from 'react';
import { differenceInDays } from 'date-fns';
import { Button } from '../button';
import { Tab, Tabs } from '../tabs';
import { DocumentListItem } from '../document-list-item';
import { ListItemContainer, ListItemHeader } from '../list-item';
import { TaskLogs } from '../task-logs';
import { getStatusColor } from '../../utils';
import { TaskOptionsMenu } from './task-options-menu';
import { useExpandListItem } from '../../hooks';
import { SaleTaskList, TaskStatus } from '../../models';
import { ButtonClickFn, HTMLElementClickFn } from '../../types';

interface Props extends React.HTMLProps<HTMLDivElement> {
    task: SaleTaskList,
    payInvoice: ButtonClickFn<void> | HTMLElementClickFn<void>;
    previousTaskDays?: number;
    downloadInvoice: HTMLElementClickFn<void>;
    previewInvoice: HTMLElementClickFn<void>;
    sendInvoice: ButtonClickFn<void> | HTMLElementClickFn<void>;
    downloadQuotation: HTMLElementClickFn<void>;
    previewQuotation: HTMLElementClickFn<void>;
    sendQuotation: ButtonClickFn<void> | HTMLElementClickFn<void>;
    updateTask: ButtonClickFn<void>;
    updateTaskStatus: ButtonClickFn<void> | HTMLElementClickFn<void>;
    uploadEstimate?: ButtonClickFn<void> | HTMLElementClickFn<void>;
}

export const TaskListItem: React.FC<Props> = (props): JSX.Element => {
    const {
        children,
        payInvoice,
        downloadInvoice,
        previewInvoice,
        sendInvoice,
        downloadQuotation,
        previewQuotation,
        sendQuotation,
        task,
        updateTask,
        updateTaskStatus,
        uploadEstimate,
    } = props;
    
    const primaryButtonDisabled = !task.taskEnabled || task.taskStatus === TaskStatus.COMPLETED;
    const primaryButtonClass = primaryButtonDisabled ? '' : 'btn-success bg-success';
    // For payment button, also disable if balanceDue is less than or equal to 0.01
    const paymentButtonDisabled = primaryButtonDisabled || task.balanceDue <= 0.01;
    const paymentButtonClass = paymentButtonDisabled ? '' : 'btn-success bg-success';
    const otherButtonsDisabled = !task.taskEnabled || task.taskStatus === 'pending';
    const invoice = task.taskAction.search('invoice') > -1;
    const quotation = task.taskAction === 'quotation';
    
    const taskCompletionDate = new Date(task.taskCompletionDate);
    
    let taskDaysOverdue = 0;
    if (task.taskStatus !== 'completed' && task.taskStatus !== 'cancelled') {
        taskDaysOverdue = differenceInDays(new Date(), taskCompletionDate);
    }
    const taskOverdueClass = taskDaysOverdue > 0 ? 'danger' : '';
    
    const documentsTab: Tab = {
        id: `taskDocumentsTab`,
        title: 'Documents',
    };
    const historyTab: Tab = {
        id: `taskHistoryTab`,
        title: 'History',
    };
    
    const { expandListItem, listItemViewState } = useExpandListItem();
    const expandTaskInfo = listItemViewState[task.saleTaskId] || false;
    
    const activeClass = expandTaskInfo ? 'active' : '';
    
    const documentsAvailable = (task?.documents || []).length > 0;
    const taskListItemTabs: Tab[] = documentsAvailable ? [{ ...documentsTab, active: true }, { ...historyTab }] : [
        {
            ...historyTab,
            active: true,
        },
    ];
    
    return (
        <>
            <ListItemContainer
                className={`striped status-indicator ${taskOverdueClass} status-${getStatusColor(task.taskStatus)} ${activeClass}`}>
                <ListItemHeader className='align-items-center'>
                    {children}
                    <div className='col-6 action-col'>
                        {(task.taskAction === 'task') && (
                            <>
                                {task.taskStatus === 'started' ? (
                                    <Button
                                        className={`btn-link ${primaryButtonClass} no-text tooltip-top`}
                                        disabled={primaryButtonDisabled}
                                        data-tooltip='Complete'
                                        data-task={task.saleTaskId}
                                        onClick={updateTaskStatus}
                                        data-status='completed'
                                    >
                                        <i className='custom-icon icon icon-only check' />
                                    </Button>
                                ) : (
                                    <Button
                                        className={`btn-link ${primaryButtonClass} no-text tooltip-top`}
                                        disabled={primaryButtonDisabled || task.taskStatus === 'completed'}
                                        data-tooltip='Start'
                                        data-task={task.saleTaskId}
                                        onClick={updateTaskStatus}
                                        data-status='started'
                                    >
                                        <i className='custom-icon icon icon-only check' />
                                    </Button>
                                )}
                            </>
                        )}
                        {(task.taskAction === 'estimate') && (
                            <Button
                                className={`btn-link ${primaryButtonClass} no-text tooltip-top`}
                                disabled={primaryButtonDisabled}
                                data-tooltip='Upload estimate'
                                data-task={task.saleTaskId}
                                onClick={uploadEstimate}
                            >
                                <i className='custom-icon icon icon-only upload' />
                            </Button>
                        )}
                        {(invoice || quotation) && task.taskStatus === 'pending' && (
                            <Button
                                className={`btn-link ${primaryButtonClass} no-text tooltip-top`}
                                disabled={primaryButtonDisabled}
                                data-tooltip={invoice ? 'Send invoice' : 'Send for Approval'}
                                data-task={task.saleTaskId}
                                onClick={invoice ? sendInvoice : sendQuotation}
                                data-status='started'
                            >
                                <i className='custom-icon icon icon-only mail' />
                            </Button>
                        )}
                        {quotation && task.taskStatus !== 'pending' && (
                            <Button
                                className={`btn-link ${primaryButtonClass} no-text tooltip-top`}
                                disabled={primaryButtonDisabled}
                                data-tooltip='Approve'
                                data-task={task.saleTaskId}
                                onClick={updateTaskStatus}
                                data-status='completed'
                            >
                                <i className='custom-icon icon icon-only check' />
                            </Button>
                        )}
                        {invoice && task.taskStatus !== 'pending' && (
                            <Button
                                className={`btn-link ${paymentButtonClass} no-text tooltip-top`}
                                disabled={paymentButtonDisabled}
                                data-tooltip='Pay'
                                data-task={task.saleTaskId}
                                onClick={payInvoice}
                            >
                                <i className='custom-icon icon icon-only money-1' />
                            </Button>
                        )}
                        <Button
                            className='btn-link no-text tooltip-top'
                            data-tooltip='Update'
                            data-task={task.saleTaskId}
                            onClick={updateTask}
                        >
                            <i className='custom-icon icon icon-only edit' />
                        </Button>
                        <Button
                            onClick={expandListItem}
                            className='btn-link no-text tooltip-top'
                            data-toggle={task.saleTaskId}
                            data-tooltip={`${expandTaskInfo ? 'Close' : 'More'}`}
                        >
                            <i className={`custom-icon icon icon-only ${expandTaskInfo ? 'close' : 'chevron-down'}`} />
                        </Button>
                        <div className='v-divider' />
                        <TaskOptionsMenu
                            task={task}
                            payInvoice={payInvoice as HTMLElementClickFn<void>}
                            paymentButtonDisabled={paymentButtonDisabled}
                            downloadInvoice={downloadInvoice}
                            menuDisabled={otherButtonsDisabled}
                            previewInvoice={previewInvoice}
                            sendInvoice={sendInvoice as HTMLElementClickFn<void>}
                            downloadQuotation={downloadQuotation}
                            previewQuotation={previewQuotation}
                            sendQuotation={sendQuotation as HTMLElementClickFn<void>}
                            uploadEstimate={uploadEstimate as HTMLElementClickFn<void>}
                        />
                    </div>
                </ListItemHeader>
            </ListItemContainer>
            {expandTaskInfo && (
                <div className='form-group form-group-default bg-transparent px-0 pt-2 pb-0' style={{ marginTop: -5 }}>
                    <Tabs
                        tabs={taskListItemTabs}
                        className='nav-tabs nav-tabs-simple nav-tabs-info justify-content-center'
                    />
                    <div className='tab-content px-0 pb-0'>
                        {documentsAvailable && (
                            <div className='tab-pane fade show active' id='taskDocumentsTab'>
                                {(task?.documents || []).map((doc, index) => (
                                    <DocumentListItem
                                        key={doc.docId}
                                        className={`${index === (task?.documents || []).length - 1 ? 'mb-0' : ''}`}
                                        doc={doc}
                                    />
                                ))}
                            </div>
                        )}
                        <div
                            className={`tab-pane fade ${documentsAvailable ? '' : 'show active'}`}
                            id='taskHistoryTab'>
                            <TaskLogs logs={task.taskLogs} />
                        </div>
                    </div>
                
                </div>
            )}
        </>
    );
};
