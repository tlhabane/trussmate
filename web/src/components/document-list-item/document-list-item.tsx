import React, { JSX } from 'react';
import { Button } from '../button';
import { ListItemContainer, ListItemHeader } from '../list-item';
import { useBasicNotification } from '../../hooks';
import { SaleDocument } from '../../models';
import { ButtonClickFn } from '../../types';

interface Props extends React.HTMLProps<HTMLButtonElement> {
    doc: SaleDocument;
}

export const DocumentListItem: React.FC<Props> = ({ className, doc, onClick }): JSX.Element => {
    const toast = useBasicNotification();
    const { docId, docSrc, docName, docDate, firstName, lastName } = doc;
    const handleDownload: ButtonClickFn = (event) => {
        event.preventDefault();
        if (onClick) {
            onClick(event as React.MouseEvent<HTMLButtonElement>);
            return;
        }
        
        if (docSrc.trim() === '') {
            toast('Invalid or missing document link', 'error');
            return;
        }
        window.open(docSrc, '_blank');
    };
    
    return (
        <ListItemContainer className={className || ''}>
            <ListItemHeader>
                <div className='col-6 title d-flex align-items-center'>
                    <div>
                        <i className='custom-icon icon file-text' />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>
                                {docName}
                            </small>
                            <small className='text-wrap small'>
                                {docDate}
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-3 title d-flex align-items-center'>
                    <div>
                        <i className='custom-icon icon user' />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>
                                {`${firstName} ${lastName}`}
                            </small>
                            <small className='text-wrap small'>
                                Provider
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-3 action-col'>
                    <Button className='btn-link no-text' data-doc={docId} onClick={handleDownload}>
                        <i className='custom-icon icon icon-only download' />
                    </Button>
                </div>
            </ListItemHeader>
        </ListItemContainer>
    );
};
