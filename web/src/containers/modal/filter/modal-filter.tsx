import React, { JSX } from 'react';
import { ModalContainer } from '../container';
import { Button, DividerHorizontal } from '../../../components';
import type { ButtonClickFn } from '../../../types';

interface Props extends React.HTMLProps<HTMLDivElement> {
    clearDataFilterParamsHandler: ButtonClickFn<void>;
    dismissModalDataFilter: () => void;
    openModalDataFilter: boolean;
}

export const ModalFilter: React.FC<Props> = (props): JSX.Element => {
    const {
        children,
        clearDataFilterParamsHandler,
        dismissModalDataFilter,
        openModalDataFilter,
    } = props;
    
    const handleDismissModal: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        dismissModalDataFilter();
    };
    
    const handleClearFilters: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        clearDataFilterParamsHandler(event);
    };
    
    return (
        <ModalContainer
            show={openModalDataFilter}
            closeModal={dismissModalDataFilter}
            modalSize='xl'
            modalStyle='fill-in data-filter'
        >
            <div className='form-group-default bg-white p-5'>
                <div className='d-flex flex-row align-items-center justify-content-between'>
                    <h5>Filter Options</h5>
                    <Button onClick={handleDismissModal} className='btn-link pr-0 mr-0'>
                        <i className='custom-icon icon icon-only close' />
                    </Button>
                </div>
                <DividerHorizontal />
                {children}
                <DividerHorizontal />
                <div className='row'>
                    <div className='col-12 mb-2'>
                        <Button className='btn-primary btn-block' onClick={handleDismissModal}>
                            <i className='custom-icon icon left-icon close' />
                            Close
                        </Button>
                    </div>
                    <div className='col-12'>
                        <Button className='btn-link btn-block' onClick={handleClearFilters}>
                            Clear Filters
                        </Button>
                    </div>
                
                </div>
            </div>
        </ModalContainer>
    );
};
