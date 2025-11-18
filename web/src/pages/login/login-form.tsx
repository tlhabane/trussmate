import React, { JSX } from 'react';
import { Link } from 'react-router-dom';
import { Button, Form, FooterTerms } from '../../components';
import { BlurredContainer } from '../../containers';
import { LoginFields } from './login.fields';
import { FormInput, FormState, FormSubmitFn, InputChangeFn, InputFocusFn } from '../../types';

type Props = {
    getElement: (name: any, props: FormInput<string>, handlers?: any) => JSX.Element;
    formConfig: FormState<LoginFields>;
    formInvalid: boolean;
    onChange: InputChangeFn;
    onBlur: InputFocusFn;
    onSubmit: FormSubmitFn<void>;
};

export const LoginForm: React.FC<Props> = ({ formConfig, formInvalid, getElement, onBlur, onChange, onSubmit }) => (
    <>
        <BlurredContainer>
            <div className="row mb-4 mt-2">
                <div className="col-md-12">
                    <div className="text-center">
                        <h4 className="m-0">Welcome back!</h4>
                        <span className="small m-0">Enter your email or phone number below to get started.</span>
                    </div>
                </div>
            </div>
            <Form onSubmit={onSubmit}>
                {Object.keys(formConfig).map((key) => getElement(key, formConfig[key as keyof LoginFields], { onBlur, onChange}))}
                <div className="form-group">
                    <Button type="submit" className="btn-primary btn-block" disabled={formInvalid}>
                        <i className="custom-icon icon left-icon lock" />
                        Sign in
                    </Button>
                </div>
            </Form>
            <div className="row">
                <div className="col-12 small text-center">
                    Forgot your password?{' '}
                    <Link to="/forgot-password" className="text-primary semi-bold disabled">
                        Reset it
                    </Link>
                    .
                </div>
            </div>
        </BlurredContainer>
        <FooterTerms btnLabel="Sign in" />
    </>
);
