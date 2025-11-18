import React, { JSX } from 'react';
import { Link } from 'react-router-dom';
import { Button, Form, FooterTerms } from '../../components';
import { BlurredContainer } from '../../containers';
import { FormInput, FormState, FormSubmitFn, InputChangeFn, InputFocusFn } from '../../types';
import { FormFields } from './form.fields';

type Props = {
    getElement: (name: any, props: FormInput<string>, handlers?: any) => React.JSX.Element;
    formConfig: FormState<FormFields>;
    formInvalid: boolean;
    onChange: InputChangeFn;
    onBlur: InputFocusFn;
    onSubmit: FormSubmitFn<void>;
};

export const ForgotPasswordForm: React.FC<Props> = ({ formConfig, formInvalid, getElement, onBlur, onChange, onSubmit }) => (
    <>
        <BlurredContainer>
            <div className="row mb-4 mt-2">
                <div className="col-md-12">
                    <div className="text-center">
                        <h4 className="m-0">
                            Having trouble accessing
                            <br />
                            your account?
                        </h4>
                        <span className="small m-0">Enter your email or phone number below to get started.</span>
                    </div>
                </div>
            </div>
            <Form onSubmit={onSubmit}>
                {Object.keys(formConfig).map((key) => getElement(key, formConfig[key as keyof FormFields], { onBlur, onChange }))}
                <div className="form-group">
                    <Button type="submit" className="btn-primary btn-block" disabled={formInvalid}>
                        <i className="custom-icon icon left-icon lock" />
                        Reset my password
                    </Button>
                </div>
            </Form>
            <div className="row">
                <div className="col-12 small text-center">
                    Back to{' '}
                    <Link to="/" className="text-primary semi-bold disabled">
                        Login
                    </Link>
                    .
                </div>
            </div>
        </BlurredContainer>
        <FooterTerms btnLabel="Reset my password" />
    </>
);
