import React from 'react';
import { Modal } from 'react-bootstrap';
import './styles.scss';

type Props = {
    show: boolean;
    closeModal: () => void;
    children: React.ReactNode;
    modalSize?: 'sm' | 'lg' | 'xl';
    modalStyle?: string;
};

export const ModalContainer: React.FC<Props> = ({ show, closeModal, children, modalSize = 'lg', modalStyle = 'slide-right' }) => (
    <Modal size={modalSize} show={show} onHide={closeModal} backdrop="static" className={modalStyle}>
        {children}
    </Modal>
);
