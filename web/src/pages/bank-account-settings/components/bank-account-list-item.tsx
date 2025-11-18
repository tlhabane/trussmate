import React, { JSX } from 'react';
import { Button, ListItemContainer, ListItemHeader } from '../../../components';
import { ButtonClickFn } from '../../../types';
import { BankAccount } from '../../../models';

type Props = {
    account: BankAccount,
    deleteBankAccount: ButtonClickFn<void>;
    updateBankAccount: ButtonClickFn<void>;
};

export const BankAccountListItem: React.FC<Props> = ({
                                                         account,
                                                         deleteBankAccount,
                                                         updateBankAccount,
                                                     }): JSX.Element => (
    <ListItemContainer className='striped'>
        <ListItemHeader>
            <div className='col-6 title'>
                <div>
                    <i className='custom-icon icon money-1' />
                </div>
                <div>
                    <h2>
                        <small className='font-weight-bold text-wrap'>
                            {account.bankAccountNo}
                        </small>
                        <small className='text-wrap small'>
                            {account.bankAccountName}
                        </small>
                    </h2>
                </div>
            </div>
            <div className='col-6 title'>
                <div>
                    <i className='custom-icon icon tag' />
                </div>
                <div>
                    <h2>
                        <small className='font-weight-bold text-wrap'>
                            {account.bankName}
                        </small>
                        <small className='text-wrap small'>
                            {account.branchCode}
                        </small>
                    </h2>
                </div>
            </div>
            <div className='col-4 action-col'>
                <Button
                    className='btn-link no-text tooltip-top'
                    data-tooltip='Update'
                    data-account={account.bankId}
                    onClick={updateBankAccount}
                >
                    <i className='custom-icon icon icon-only edit' />
                </Button>
                <div className='v-divider' />
                <Button
                    className='btn-link delete-btn no-text tooltip-top'
                    data-tooltip='Delete'
                    data-account={account.bankId}
                    onClick={deleteBankAccount}
                >
                    <i className='custom-icon icon icon-only trash' />
                </Button>
            </div>
        </ListItemHeader>
    </ListItemContainer>
);
