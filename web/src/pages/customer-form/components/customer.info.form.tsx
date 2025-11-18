import React, { JSX } from 'react';
import { Button, CustomerFormElements } from '../../../components';
import { StickyFooter } from '../../../containers';
import { Customer } from '../../../models';
import {
    ButtonClickFn,
    FormInput,
    FormState,
    InputChangeFn,
    InputFocusFn,
    ReactSelectFn,
} from '../../../types';

type Props = {
    getElement: (name: any, props: FormInput<Customer>, handlers?: any) => JSX.Element;
    formConfig: FormState<Customer>;
    formInvalid: boolean;
    onBlur?: InputFocusFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSelect: ReactSelectFn<void>;
    onChange: InputChangeFn<HTMLInputElement | HTMLTextAreaElement, void>;
    onSwitchTabHandler?: ButtonClickFn<void>;
    mobilePadding: string;
    tabletPadding: string;
};

export const CustomerInfoForm: React.FC<Props> = (props): JSX.Element => {
    const {
        formConfig,
        formInvalid,
        getElement,
        onBlur,
        onChange,
        onSelect,
        onSwitchTabHandler,
        mobilePadding,
        tabletPadding,
    } = props;
    
    const businessAccount = formConfig.customerType.value === 'business';
    
    return (
        <>
            <div className='row mb-5 pb-5'>
                <div className='col-lg-6 offset-lg-3 col-md-10 offset-md-1 col-sm-10 offset-sm-1 mb-5'>
                    <CustomerFormElements
                        getElement={getElement}
                        formConfig={formConfig}
                        onBlur={onBlur}
                        onChange={onChange}
                        onSelect={onSelect}
                        mobilePadding={mobilePadding}
                        tabletPadding={tabletPadding}
                    />
                </div>
            </div>
            <StickyFooter>
                <div className='row'>
                    <div className='col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1'>
                        <div className='form-group mt-3 mb-3'>
                            <div className='row'>
                                <div className='col-3 pr-sm-0 pl-sm-0'>
                                    <Button
                                        type='button'
                                        className='btn-default btn-block'
                                        data-tab='cancel'
                                        onClick={onSwitchTabHandler}
                                    >
                                        Cancel
                                    </Button>
                                </div>
                                <div className='col-8 offset-1 pr-sm-0 pl-sm-0'>
                                    <Button
                                        type='submit'
                                        className={`btn-${businessAccount ? 'primary' : 'success'} btn-block`}
                                        disabled={formInvalid}
                                    >
                                        <i className={`custom-icon icon right-icon ${businessAccount ? 'chevrons-right' : 'save'}`} />
                                        {businessAccount ? 'Next' : 'Save'}
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </StickyFooter>
        </>
    );
};
