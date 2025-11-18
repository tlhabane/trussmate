import React from 'react';
import { Button, ListItemContainer, ListItemHeader } from '../../components';
import { WorkflowList } from '../../models';
import { ButtonClickFn } from '../../types';

type Props = {
    workflow: WorkflowList;
    deleteWorkflow: ButtonClickFn<void>;
    updateWorkflow: ButtonClickFn<void>;
}

export const SaleSettingItem: React.FC<Props> = ({ workflow, deleteWorkflow, updateWorkflow }) => (
    <ListItemContainer className="striped">
        <ListItemHeader>
            <div className="col-6 title">
                <div>
                    <i className="custom-icon icon tag" />
                </div>
                <div>
                    <h2>
                        <small className="font-weight-bold text-wrap">
                            {workflow.workflowName}
                        </small>
                        <small className="text-wrap small">
                            Process Name
                        </small>
                    </h2>
                </div>
            </div>
            <div className="col-2 title">
                <div>
                    <i className="custom-icon icon activity" />
                </div>
                <div>
                    <h2>
                        <small className="font-weight-bold text-wrap">
                            {workflow.tasks.length}
                        </small>
                        <small className="text-wrap small">
                            Tasks
                        </small>
                    </h2>
                </div>
            </div>
            <div className="col-4 action-col">
                <Button
                    className="btn-link no-text tooltip-top"
                    data-tooltip="Update"
                    data-workflow={workflow.workflowId}
                    onClick={updateWorkflow}
                >
                    <i className="custom-icon icon icon-only edit" />
                </Button>
                <div className="v-divider" />
                <Button
                    className="btn-link delete-btn no-text tooltip-top"
                    data-tooltip="Delete"
                    data-workflow={workflow.workflowId}
                    onClick={deleteWorkflow}
                >
                    <i className="custom-icon icon icon-only trash" />
                </Button>
            </div>
        </ListItemHeader>
    </ListItemContainer>
);
