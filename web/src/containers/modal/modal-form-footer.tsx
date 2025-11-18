import React from 'react';
import { Button } from '../../components';

export const ModalFormFooter: React.FC<React.HTMLProps<HTMLButtonElement>> = ({ disabled, onClick }) => (
    <div className="form-footer">
        <div className="row">
            <div className="col-4">
                <Button className="btn-default btn-block bg-white" onClick={onClick}>
                    <i className="custom-icon icon left-icon close" />
                    Cancel
                </Button>
            </div>
            <div className="col-7 offset-1">
                <Button
                    type="submit"
                    className={`${disabled ? 'btn-default bg-white' : 'btn-primary'} btn-block`}
                    disabled={disabled}
                >
                    <i className="custom-icon icon left-icon save" />
                    Save
                </Button>
            </div>
        </div>
    </div>
);
