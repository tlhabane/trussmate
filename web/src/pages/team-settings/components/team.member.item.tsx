import React, { JSX } from 'react';
import { Button, ListItemContainer, ListItemHeader } from '../../../components';
import { capitalizeFirstLetter } from '../../../utils';
import { User, UserStatus } from '../../../models';
import { ButtonClickFn } from '../../../types';

type Props = {
    user: User,
    toggleUserStateHandler: ButtonClickFn<void>;
    updateUserHandler: ButtonClickFn<void>;
};

export const TeamMemberItem: React.FC<Props> = ({ toggleUserStateHandler, updateUserHandler, user }): JSX.Element => {
    const { firstName, lastName, tel, email, username, userRole, userStatus } = user;
    
    const strUserRole = capitalizeFirstLetter(userRole.toString().split('_').join(' '));
    
    const userActive = userStatus === UserStatus.active;
    const buttonIcon = userActive ? 'lock' : 'unlock';
    const buttonClass = userActive ? 'delete-btn' : 'btn-success';
    const buttonTooltip = userActive ? 'Deactivate' : 'reactivate';
    const statusColor = `status-${userActive ? 'success' : 'cancelled'}`;
    
    return (
        <ListItemContainer className={`striped status-indicator ${statusColor}`}>
            <ListItemHeader>
                <div className='col-4 title'>
                    <div>
                        <i className='custom-icon icon user' />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>
                                {capitalizeFirstLetter(`${firstName} ${lastName}`)}
                            </small>
                            <small className='text-wrap small'>
                                {strUserRole}
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-4 title'>
                    <div>
                        <i className='custom-icon icon tel' />
                    </div>
                    <div>
                        <h2>
                            <small className='font-weight-bold text-wrap'>{tel}</small>
                            <small className='text-wrap small'>
                                {email}
                            </small>
                        </h2>
                    </div>
                </div>
                <div className='col-4 action-col'>
                    <Button
                        className='btn-link no-text tooltip-top'
                        data-tooltip='Update'
                        data-username={username}
                        onClick={updateUserHandler}
                    >
                        <i className='custom-icon icon icon-only edit' />
                    </Button>
                    <div className='v-divider' />
                    <Button
                        className={`btn-link ${buttonClass} no-text tooltip-top`}
                        data-tooltip={buttonTooltip}
                        data-username={username}
                        onClick={toggleUserStateHandler}
                    >
                        <i className={`custom-icon icon icon-only ${buttonIcon}`} />
                    </Button>
                </div>
            </ListItemHeader>
        </ListItemContainer>
    );
};
