import React from 'react';
import { Link } from 'react-router-dom';
import './styles.scss';

type Props = {
    btnLabel: string;
};

export const FooterTerms: React.FC<Props> = ({ btnLabel }) => (
    <div className="auth-wrapper-footer">
        <div className="col-12 text-center">
            <span className="small">
                By clicking on the <em>{btnLabel}</em> button, you agree to our{' '}
                <Link to="/legal/terms-of-service" className="text-primary semi-bold disabled">
                    Terms
                </Link>{' '}
                and have read and acknowledge our{' '}
                <Link to="/legal/privacy-policy" className="text-primary semi-bold disabled">
                    Privacy
                </Link>{' '}
                Statement.
            </span>
        </div>
    </div>
);
