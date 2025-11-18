import React, { JSX } from 'react';
import { Dropdown } from 'react-bootstrap';
import { Button, DropdownMenu, DropdownToggle, ListItemContainer, ListItemHeader } from '../../components';
import { capitalizeFirstLetter, formatPrice } from '../../utils';
import { InvoiceMenu } from './invoice-menu';
import { PaymentMenu } from './payment-menu';
import { TransactionList } from '../../models';
import { ButtonClickFn, HTMLElementClickFn } from '../../types';

type Props = {
    transaction: TransactionList,
    viewTransactionDetail: boolean;
    viewTransactionHandler: ButtonClickFn<void>;
    payInvoice: HTMLElementClickFn<void>;
    cancelTransactionHandler: ButtonClickFn<void>;
    invoiceActionHandler: HTMLElementClickFn<void>;
    transactionActionHandler: HTMLElementClickFn<void>;
};

export const TransactionListItem: React.FC<Props> = ({
                                                         viewTransactionDetail,
                                                         viewTransactionHandler,
                                                         payInvoice,
                                                         transaction,
                                                         cancelTransactionHandler,
                                                         invoiceActionHandler,
                                                         transactionActionHandler,
                                                     }): JSX.Element => {
    
    const contactName = `${transaction.firstName} ${transaction.lastName}`;
    const renderContactName = contactName.trim() === '' ? 'Customer' : contactName;
    const isPayment = (
        transaction.transactionType === 'payment' ||
        transaction.transactionType === 'credit_memo' ||
        transaction.transactionType === 'debit_memo'
    );
    const transactionDesc = transaction.transactionDesc || (
        transaction.transactionType === 'payment'
            ? `Payment Invoice ${transaction.invoiceNo}`
            : `${capitalizeFirstLetter(transaction.transactionType.split('_').join(' '))} #${transaction.invoiceNo}`
    );
    const itemIcon = isPayment ? 'money-1' : 'invoice';
    const primaryButtonDisabled = transaction.transactionCancelled === 1;
    const cancelPaymentButtonClass = primaryButtonDisabled ? '' : 'btn-danger bg-danger';
    const paymentButtonDisabled = !isPayment && transaction.invoiceBalance <= 0.01;
    const paymentButtonClass = paymentButtonDisabled ? '' : 'btn-success bg-success';
    const CancelPaymentButton = (
        <Button
            className={`btn-link ${cancelPaymentButtonClass} no-text tooltip-top`}
            data-transaction={transaction.transactionId}
            data-tooltip='Cancel payment'
            disabled={primaryButtonDisabled}
            onClick={cancelTransactionHandler}
        >
            <i className='custom-icon icon icon-only return' />
        </Button>
    );
    
    const PaymentButton = (
        <Button
            className={`btn-link ${paymentButtonClass} no-text tooltip-top`}
            data-transaction={transaction.transactionId}
            data-tooltip='Pay invoice'
            disabled={paymentButtonDisabled}
            onClick={payInvoice}
        >
            <i className='custom-icon icon icon-only money-1' />
        </Button>
    );
    
    const PaymentOptionsMenu = (
        <PaymentMenu transactionActionHandler={transactionActionHandler} transactionId={transaction.transactionId} />
    );
    const InvoiceOptionsMenu = (
        <InvoiceMenu invoiceActionHandler={invoiceActionHandler} transactionId={transaction.transactionId} />
    );
    
    return (
        <ListItemContainer className='striped'>
            <ListItemHeader>
                <div className='col-3 title'>
                    <div>
                        <i className={`custom-icon icon ${itemIcon}`} />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>
                                {transaction.invoiceNo}
                            </small>
                            <small className='text-wrap small'>
                                {capitalizeFirstLetter(`${transaction.transactionType.split('_').join(' ')}`)}
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-4 title'>
                    <div>
                        <i className='custom-icon icon invoice' />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>
                                {transactionDesc}
                            </small>
                            <small className='text-wrap small'>
                                Description
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-3 title'>
                    <div>
                        <i className='custom-icon icon invoice' />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>
                                {capitalizeFirstLetter(transaction.customerName)}
                            </small>
                            <small className='text-wrap small'>
                                {capitalizeFirstLetter(renderContactName)}
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-3 title text-right'>
                    <h2>
                        <small className='font-weight-bold text-wrap'>
                            {formatPrice(transaction.transactionAmount)}
                        </small>
                        <small className='text-wrap small'>
                            Amount
                        </small>
                    </h2>
                
                </div>
                <div className='col-4 action-col'>
                    {isPayment ? CancelPaymentButton : PaymentButton}
                    <Button
                        className='btn-link no-text tooltip-top'
                        data-transaction={transaction.transactionId}
                        data-tooltip={`Send ${isPayment ? 'receipt' : 'invoice'}`}
                        data-url={isPayment ? '/transaction/send' : '/invoice/send'}
                        data-action='send'
                        onClick={isPayment ? transactionActionHandler : invoiceActionHandler}
                    >
                        <i className='custom-icon icon icon-only mail' />
                    </Button>
                    <Button
                        onClick={viewTransactionHandler}
                        className='btn-link no-text tooltip-top'
                        data-toggle={transaction.transactionId}
                        data-tooltip={`${viewTransactionDetail ? 'Close' : 'More'}`}
                    >
                        <i className={`custom-icon icon icon-only ${viewTransactionDetail ? 'close' : 'chevron-down'}`} />
                    </Button>
                    <div className='v-divider' />
                    <Dropdown className='mr-1'>
                        <Dropdown.Toggle
                            as={DropdownToggle as React.ElementType}
                            className='btn btn-link bg-transparent border-0 no-text profile-dropdown-toggle'
                        />
                        <Dropdown.Menu className='profile-dropdown' align='end' as={DropdownMenu as React.ElementType}>
                            {isPayment ? PaymentOptionsMenu : InvoiceOptionsMenu}
                        </Dropdown.Menu>
                    </Dropdown>
                </div>
            </ListItemHeader>
        </ListItemContainer>
    );
};
