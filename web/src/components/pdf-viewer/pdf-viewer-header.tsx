import React, { JSX } from 'react';
import { Button } from '../button';
import { ButtonClickFn } from '../../types';

type Props = {
    closeDocumentPreviewHandler: ButtonClickFn<void>;
    openNewTabHandler?: ButtonClickFn<void>;
    previewTitle?: string;
    documentTitle: string;
};
export const PDFViewerHeader: React.FC<Props> = ({
                                                     closeDocumentPreviewHandler,
                                                     openNewTabHandler,
                                                     previewTitle = 'Preview',
                                                     documentTitle,
                                                 }): JSX.Element => (
    <div className='header'>
        <div className='header-inner'>
            <div className='d-flex flex-column align-items-start justify-content-center'>
                <p className='m-0 font-weight-bold'>{documentTitle}</p>
                <p className='m-0 small'>{previewTitle || 'Preview'}</p>
            </div>
            <div className='d-flex flex-row'>
                <Button className='btn-link' onClick={openNewTabHandler}>
                    <i className='custom-icon icon icon-only external-link' />
                </Button>
                <Button className='btn-link' onClick={closeDocumentPreviewHandler}>
                    <i className='custom-icon icon icon-only close' />
                </Button>
            </div>
        </div>
    </div>
);
