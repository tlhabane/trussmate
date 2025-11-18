import React from 'react';
import { ModalContainer } from '../container';
import { Button, TextArea } from '../../../components';
import { ButtonClickFn, InputChangeFn } from '../../../types';
import './styles.scss';

interface Props extends React.HTMLProps<HTMLDivElement> {
    fatalPrompt?: boolean;
    openModalPrompt: boolean;
    commentsMinLength?: number;
    commentsUpdateHandler?: InputChangeFn<HTMLTextAreaElement>;
    dismissModalPrompt: () => void;
    onDismissModalPromptHandler?: ButtonClickFn<void>;
    promptConfirmationButton: React.ReactNode;
}

export const ModalPrompt: React.FC<Props> = (props) => {
    const {
        children,
        fatalPrompt,
        openModalPrompt,
        commentsMinLength,
        commentsUpdateHandler,
        dismissModalPrompt,
        onDismissModalPromptHandler,
        promptConfirmationButton,
    } = props;
    
    const handleDismissPrompt: ButtonClickFn<void> = (event) => {
        event.preventDefault();
        if (onDismissModalPromptHandler) {
            onDismissModalPromptHandler(event);
            return;
        }
        dismissModalPrompt();
    };
    
    return (
        <ModalContainer
            show={openModalPrompt}
            closeModal={dismissModalPrompt}
            modalSize="xl"
            modalStyle="fill-in user-prompt"
        >
            <div className="form-group-default bg-white p-5">
                {children}
                {commentsUpdateHandler && (
                    <>
                        <TextArea label="Reason" style={{ minHeight: 80 }} onChange={commentsUpdateHandler} required />
                        {commentsMinLength && commentsMinLength > 0 && (
                            <span className="d-block small text-danger">
                                Kindly provide a reason of no less than {commentsMinLength} characters long.
                            </span>
                        )}
                    </>
                )}
                <hr className="default" />
                <div className="row">
                    <div className="col-4">
                        <Button
                            className={`btn-${fatalPrompt ? 'success' : 'default'} btn-block`}
                            onClick={handleDismissPrompt}
                        >
                            <i className="custom-icon icon left-icon d-sm-none d-md-block close" />
                            Cancel
                        </Button>
                    </div>
                    <div className="col-8">{promptConfirmationButton}</div>
                </div>
            </div>
        </ModalContainer>
    );
}
