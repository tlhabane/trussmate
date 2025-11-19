import React, { JSX } from 'react';
import { Button, DocumentListItem, ListItemContainer, ListItemHeader, Tabs, Tab } from '../../../components';
import { capitalizeFirstLetter, formatPrice, getStatusColor } from '../../../utils';
import { SaleDocument, SaleList, SaleStatus, TaskStatus } from '../../../models';
import { ButtonClickFn } from '../../../types';

const saleListItemTabs: Tab[] = [
    {
        id: `saleTaskTab`,
        title: 'Tasks',
        active: true,
    },
    {
        id: `saleDocumentTab`,
        title: 'Documents',
    },
];

type Props = {
    sale: SaleList;
    approveSale: ButtonClickFn<void>;
    deleteSale: ButtonClickFn<void>;
    saleTaskList: React.ReactNode;
    toggleListItem: ButtonClickFn<void>;
    updateSale: ButtonClickFn<void>;
    viewSaleDetail: boolean;
}

export const SaleListItem: React.FC<Props> = (props): JSX.Element => {
    const { sale, saleTaskList, approveSale, deleteSale, toggleListItem, updateSale, viewSaleDetail } = props;
    const { saleId, saleNo, saleStatus, saleTotal, contact, customer, documents } = sale;
    
    const getSaleStatus = (status: SaleStatus | TaskStatus) => getStatusColor(status);
    
    const documentMap: Record<string, SaleDocument[]> = {};
    const documentTypes = documents.map((document) => document.docType).filter((value, index, self) => {
        return self.indexOf(value) === index;
    });
    documentTypes.forEach((documentType) => {
        const documentList = documents.filter((document) => document.docType === documentType);
        if (documentList.length > 0) {
            const type = capitalizeFirstLetter(documentType.toString().split('_').join(' '));
            documentMap[type] = documentList;
        }
    });
    
    const activeClass = viewSaleDetail ? 'active' : '';
    const contactPerson = capitalizeFirstLetter(`${contact.firstName} ${contact.lastName}`);
    const contactPersonAvailable = contactPerson.replace(/\s/g, '') !== '';
    const businessCustomer = customer.customerType === 'business';
    return (
        <>
            <ListItemContainer
                className={`striped status-indicator status-${getSaleStatus(saleStatus)} ${activeClass}`}
            >
                <ListItemHeader>
                    <div className='col-3 title'>
                        <div>
                            <i className='custom-icon icon invoice' />
                        </div>
                        <div>
                            <h2>
                                <small className='font-weight-bold text-wrap'>
                                    {saleNo}
                                </small>
                                <small className='text-wrap small'>
                                    Reference No.
                                </small>
                            </h2>
                        </div>
                    </div>
                    <div className='col-4 title'>
                        <div>
                            <i className={`custom-icon icon user${businessCustomer ? 's' : ''}`} />
                        </div>
                        <div>
                            <h2>
                                <small className='font-weight-bold text-wrap'>
                                    {customer.customerName}
                                </small>
                                <small className='text-wrap small'>
                                    {contactPersonAvailable ? contactPerson : 'Contact Person'}
                                </small>
                            </h2>
                        </div>
                    </div>
                    <div className='col-2 title'>
                        <div>
                            <i className='custom-icon icon money-1' />
                        </div>
                        <div>
                            <h2>
                                <small className='font-weight-bold'>
                                    {formatPrice(saleTotal, '')}
                                </small>
                                <small>Amount</small>
                            </h2>
                        </div>
                    </div>
                    <div className='col-3 action-col'>
                        <Button
                            onClick={approveSale}
                            data-status='started'
                            data-sale={sale.saleId}
                            className='btn-link success-btn tooltip-top no-text'
                            data-tooltip='Approve'
                            disabled={saleStatus === SaleStatus.PENDING}
                        >
                            <i className='custom-icon icon icon-only check' />
                        </Button>
                        <Button
                            className='btn-link no-text tooltip-top'
                            data-tooltip='Update'
                            data-sale={saleId}
                            onClick={updateSale}
                        >
                            <i className='custom-icon icon icon-only edit' />
                        </Button>
                        <Button
                            onClick={toggleListItem}
                            className='btn-link no-text tooltip-top'
                            data-toggle={saleId}
                            data-tooltip={`${viewSaleDetail ? 'Close' : 'More'}`}
                        >
                            <i className={`custom-icon icon icon-only ${viewSaleDetail ? 'close' : 'chevron-down'}`} />
                        </Button>
                        <div className='v-divider' />
                        <Button
                            className='btn-link delete-btn no-text tooltip-top'
                            data-tooltip='Delete'
                            data-sale={saleId}
                            onClick={deleteSale}
                        >
                            <i className='custom-icon icon icon-only trash' />
                        </Button>
                    
                    </div>
                </ListItemHeader>
            </ListItemContainer>
            {viewSaleDetail && (
                <div className='form-group form-group-default bg-transparent px-0 pt-2 pb-0' style={{ marginTop: -5 }}>
                    <Tabs
                        tabs={saleListItemTabs}
                        className='nav-tabs nav-tabs-simple nav-tabs-info justify-content-center'
                    />
                    <div className='tab-content px-0 pb-0'>
                        <div className='tab-pane fade show active' id='saleTaskTab'>
                            {saleTaskList}
                        </div>
                        <div className='tab-pane fade' id='saleDocumentTab'>
                            {Object.entries(documentMap).map(([key, docs], index) => (
                                <div className='d-flex flex-column' key={key}>
                                    <div className='form-group form-group-default'>
                                        {`${docs.length} ${key}(s)`}
                                    </div>
                                    {docs.map((doc) => (
                                        <DocumentListItem
                                            key={doc.docId}
                                            doc={doc}
                                            className={`${index === Object.entries(documentMap).length - 1 ? 'mb-0' : ''}`}
                                        />
                                    ))}
                                
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};
