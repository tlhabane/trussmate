import React, { JSX } from 'react';
// eslint-disable-next-line import/no-extraneous-dependencies
import { Worker, Viewer } from '@react-pdf-viewer/core';
// eslint-disable-next-line import/no-extraneous-dependencies
// import { defaultLayoutPlugin } from '@react-pdf-viewer/default-layout';
import { PDFViewerToolbar } from './pdf-viewer-toolbar';
import { ContainerSpinner } from '../spinner';

// eslint-disable-next-line import/no-extraneous-dependencies
import '@react-pdf-viewer/core/lib/styles/index.css';
// eslint-disable-next-line import/no-extraneous-dependencies
import '@react-pdf-viewer/default-layout/lib/styles/index.css';

type Props = {
    documentUrl: string;
    showSidebar?: boolean;
}
export const PDFViewerContainer: React.FC<Props> = ({ documentUrl, showSidebar = false }): JSX.Element => {
    const defaultLayoutPluginInstance = PDFViewerToolbar(showSidebar);
    
    return (
        <Worker workerUrl='https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js'>
            <Viewer
                fileUrl={documentUrl}
                renderLoader={(percentages: number) => (
                    <div style={{ width: '240px' }}>
                        <ContainerSpinner>
                            Loading document ({Math.round(percentages)}%)...
                        </ContainerSpinner>
                    </div>
                )}
                plugins={[defaultLayoutPluginInstance]}
            />
        </Worker>
    );
};

