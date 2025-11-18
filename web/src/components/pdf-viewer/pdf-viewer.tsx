import React, { JSX } from 'react';
import { PDFViewerContainer } from './pdf-viewer-container';
import { PDFViewerHeader } from './pdf-viewer-header';
import { Button } from '../button';
import { ContainerSpinner } from '../spinner';
import { StickyFooter } from '../../containers';
import { ButtonClickFn } from '../../types';

type Props = {
    documentUrl: string;
    previewDocument: boolean;
    previewTitle?: string;
    documentTitle: string;
    closeDocumentPreview: () => void;
    saveOnlyHandler?: ButtonClickFn<void> | undefined;
    saveAndSendHandler?: ButtonClickFn<void> | undefined;
};

export const PDFViewer: React.FC<Props> = ({
                                               documentTitle,
                                               previewTitle = 'Preview',
                                               documentUrl,
                                               closeDocumentPreview,
                                               previewDocument,
                                               saveAndSendHandler,
                                               saveOnlyHandler,
                                           }): JSX.Element | null => {
    if (previewDocument) {
        const handleDismissViewer: ButtonClickFn<void> = (event) => {
            event.preventDefault();
            closeDocumentPreview();
        };
        const handleOpenInNewTab: ButtonClickFn<void> = (event) => {
            event.preventDefault();
            window.open(documentUrl, '_blank', 'noopener,noreferrer');
        };
        
        const handlersAvailable = !!saveOnlyHandler || !!saveAndSendHandler;
        
        return (
            <div className={`pdfContainer ${previewDocument ? 'show' : ''}`}>
                {documentUrl.trim() === '' && <ContainerSpinner>Loading document...</ContainerSpinner>}
                {documentUrl.trim() !== '' && (
                    <div className='pdfViewer'>
                        <PDFViewerHeader
                            previewTitle={previewTitle || 'Preview'}
                            documentTitle={documentTitle}
                            closeDocumentPreviewHandler={handleDismissViewer}
                            openNewTabHandler={handleOpenInNewTab}
                        />
                        
                        <div className='content'>
                            <PDFViewerContainer documentUrl={documentUrl} />
                            {handlersAvailable && (
                                <StickyFooter style={{ height: 80, padding: '10px' }}>
                                    <div className='row'>
                                        <div className='col-3 pr-sm-0 pl-sm-0'>
                                            <Button
                                                className='btn-default btn-block'
                                                onClick={handleDismissViewer}
                                            >
                                                <i className='custom-icon icon left-icon close' />
                                                Close
                                            </Button>
                                        </div>
                                        <div className='col-6 offset-3 pr-sm-0 pl-sm-0'>
                                            <div className='row'>
                                                {saveAndSendHandler && saveOnlyHandler && (
                                                    <>
                                                        <div className='col-2 offset-3 pl-sm-0'>
                                                            <Button
                                                                className='btn-primary btn-block tooltip-top'
                                                                data-tooltip='Save only'
                                                                onClick={saveOnlyHandler}
                                                            >
                                                                <i className='custom-icon icon icon-only save' />
                                                            </Button>
                                                        </div>
                                                        <div className='col-7 pl-sm-0'>
                                                            <Button
                                                                className='btn-success btn-block'
                                                                onClick={saveAndSendHandler}
                                                            >
                                                                <i className='custom-icon icon left-icon mail' />
                                                                Save & Send
                                                            </Button>
                                                        </div>
                                                    </>
                                                )}
                                                {!saveAndSendHandler && saveOnlyHandler && (
                                                    <div className='col-7 offset-5 pl-sm-0'>
                                                        <Button
                                                            className='btn-success btn-block'
                                                            onClick={saveOnlyHandler}
                                                        >
                                                            <i className='custom-icon icon left-icon save' />
                                                            Save
                                                        </Button>
                                                    </div>
                                                )}
                                                {saveAndSendHandler && !saveOnlyHandler && (
                                                    <div className='col-7 offset-5 pl-sm-0'>
                                                        <Button
                                                            className='btn-success btn-block'
                                                            onClick={saveAndSendHandler}
                                                        >
                                                            <i className='custom-icon icon left-icon mail' />
                                                            Save & Send
                                                        </Button>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </StickyFooter>
                            )}
                        </div>
                    
                    </div>
                )}
            
            </div>
        );
    }
    return null;
};
