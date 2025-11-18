import React, { JSX, ReactElement } from 'react';
// eslint-disable-next-line import/no-extraneous-dependencies
import { defaultLayoutPlugin, ToolbarProps, ToolbarSlot } from '@react-pdf-viewer/default-layout';

const renderToolbar = (Toolbar: (props: ToolbarProps) => ReactElement): JSX.Element => (
    <Toolbar>
        {(slots: ToolbarSlot) => {
            const {
                CurrentPageInput,
                Download,
                EnterFullScreen,
                GoToNextPage,
                GoToPreviousPage,
                NumberOfPages,
                Print,
                ShowSearchPopover,
                Zoom,
                ZoomIn,
                ZoomOut,
            } = slots;
            
            return (
                <span style={{ width: '100%', height: '100%' }} className='d-flex flex-row justify-content-between'>
                    <div style={{ padding: '0px 2px' }}>
                        <ShowSearchPopover />
                    </div>
                    <div style={{ padding: '0px 2px' }}>
                        <ZoomOut />
                    </div>
                    <div style={{ padding: '0px 2px' }}>
                        <Zoom />
                    </div>
                    <div style={{ padding: '0px 2px' }}>
                        <ZoomIn />
                    </div>
                    <div className='d-flex flex-row justify-content-around' style={{ marginLeft: 'auto' }}>
                        <div style={{ padding: '0px 2px' }}>
                            <GoToPreviousPage />
                        </div>
                        <div
                            style={{ padding: '0px 2px' }}
                            className='d-flex flex-row align-items-center justify-content-center'
                        >
                            <CurrentPageInput /> / <NumberOfPages />
                        </div>
                        <div style={{ padding: '0px 2px' }}>
                            <GoToNextPage />
                        </div>
                    </div>
                    <div style={{ padding: '0px 2px', marginLeft: 'auto' }}>
                        <EnterFullScreen />
                    </div>
                    <div style={{ padding: '0px 2px' }}>
                        <Download />
                    </div>
                    <div style={{ padding: '0px 2px' }}>
                        <Print />
                    </div>
                </span>
            );
        }}
    </Toolbar>
);

export const PDFViewerToolbar = (showSidebar = false) =>
    defaultLayoutPlugin({
        renderToolbar,
        sidebarTabs: (defaultTabs: any[]) => (showSidebar ? defaultTabs : []),
    });
