import React, { JSX } from 'react';
import { Sale } from '../../models';
import { Button } from '../../components';
import {
    ButtonClickFn,
    FormInput,
    FormState,
    InputChangeFn,
    ReactSelectFn,
} from '../../types';

interface Props {
    addCustomer: ButtonClickFn<void>;
    deliveryAvailable: number;
    getElement: (name: any, props: FormInput<string>, handlers?: any) => JSX.Element;
    formConfig: FormState<any>;
    onSelect?: ReactSelectFn<void>;
    showContactPerson: boolean;
    uploadFloorPlans: ButtonClickFn<void>;
    onUpload?: InputChangeFn<HTMLInputElement>;
    uploadedFileCount?: number;
}

export const SaleFormInputElements = React.forwardRef<HTMLInputElement, Props>((props, ref): JSX.Element => {
    const {
        addCustomer,
        deliveryAvailable,
        getElement,
        formConfig,
        onSelect,
        showContactPerson,
        onUpload,
        uploadFloorPlans,
        uploadedFileCount = 0,
    } = props;
    
    const getElementProps = (key: keyof Sale) => formConfig[key.toString()];
    const SaleWorkflowSelector = getElement('workflowId', getElementProps('workflowId'), { onSelect });
    const CustomerSelector = getElement('customerId', getElementProps('customerId'), { onSelect });
    const ContactSelector = getElement('contactId', getElementProps('contactId'), { onSelect });
    const BillingAddressSelector = getElement('billingAddressId', getElementProps('billingAddressId'), { onSelect });
    const DeliveryAddressSelector = getElement('deliveryAddressId', getElementProps('deliveryAddressId'), { onSelect });
    /*
    const LabourOptionSelector = getElement('labour', getElementProps('labour'), { onSelect });
    */
    const DeliveryOptionSelector = getElement('delivery', getElementProps('delivery'), { onSelect });
    
    return (
        <div className='row mb-5 pb-5'>
            <div className='col-lg-6 offset-lg-3 col-md-10 offset-md-1 col-sm-10 offset-sm-1 mb-5'>
                <>
                    <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                        <>
                            <div className='row'>
                                <div className='col-8 d-flex flex-row align-items-center'>
                            <span className='pr-3'>
                                <i className='custom-icon icon activity' />
                            </span>
                                    <h6>Sale Options</h6>
                                </div>
                                <div className='col-4'>
                                    <input
                                        accept='.pdf'
                                        onChange={onUpload}
                                        ref={ref}
                                        type='file'
                                        style={{ opacity: 0, position: 'absolute' }}
                                        multiple
                                    />
                                    <Button
                                        className='btn-success btn-block count-badge badge-simple'
                                        data-badge={uploadedFileCount}
                                        onClick={uploadFloorPlans}>
                                        <i className='custom-icon icon position-relative upload mx-2' />
                                        Floor Plans
                                    </Button>
                                </div>
                            </div>
                            <hr className='default' />
                            {SaleWorkflowSelector}
                            {/*{LabourOptionSelector}
                            {DeliveryOptionSelector}*/}
                            {deliveryAvailable > 0 && DeliveryOptionSelector}
                        </>
                    </div>
                    <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                        <div className='row'>
                            <div className='col-8 d-flex flex-row align-items-center'>
                                <div className='d-flex flex-row align-items-center'>
                                <span className='pr-3'>
                                    <i className='custom-icon icon user' />
                                </span>
                                    <h6>Customer</h6>
                                </div>
                            </div>
                            <div className='col-4'>
                                <Button
                                    className='btn-success btn-block'
                                    onClick={addCustomer}>
                                    <i className='custom-icon icon position-relative plus-circle mx-2' />
                                    Add Customer
                                </Button>
                            </div>
                        </div>
                        
                        <hr className='default' />
                        {CustomerSelector}
                        {showContactPerson ? ContactSelector : null}
                        {BillingAddressSelector}
                    </div>
                    {(formConfig['delivery'].value === 1 || formConfig['labour'].value === 1) && (
                        <div className='form-group form-group-default bg-transparent pt-2 pb-2'>
                            <div className='d-flex flex-row align-items-center'>
                        <span className='pr-3'>
                            <i className='custom-icon icon location' />
                        </span>
                                <h6>Delivery Address</h6>
                            </div>
                            <hr className='default' />
                            {DeliveryAddressSelector}
                        </div>
                    )}
                </>
            </div>
        </div>
    );
});
