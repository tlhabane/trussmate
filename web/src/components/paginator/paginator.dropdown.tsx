import React from 'react';
import { Dropdown } from 'react-bootstrap';
import { DropdownToggle, DropdownMenu } from '../dropdown';
import type { HTMLElementClickFn, LinkClickFn, ReactSelectSingleOption } from '../../types';

type Props = {
    enabled: boolean;
    recordsPerPage: ReactSelectSingleOption[];
    activeRecordsPerPage: ReactSelectSingleOption;
    pagingHandler?: HTMLElementClickFn<void>;
    resetListFilterHandler?: LinkClickFn<void>;
};

export const PaginatorDropdown: React.FC<Props> = (props) => {
    const { enabled, activeRecordsPerPage, pagingHandler, recordsPerPage, resetListFilterHandler } = props;
    
    return (
        <Dropdown>
            <Dropdown.Toggle
                as={DropdownToggle as React.ElementType}
                className={`profile-dropdown-toggle dropdown-toggle inline-flex flex-row${enabled ? '' : ' disabled'}`}
            >
                <i className="custom-icon icon" aria-label="More-Vertical" style={{ width: 20, height: 20 }} />
            </Dropdown.Toggle>
            <Dropdown.Menu className="dropdown-menu profile-dropdown" as={DropdownMenu as React.ElementType}>
                {pagingHandler ? (
                    <>
                        <Dropdown.Item eventKey="Show up to" href="#">
                        <span className="d-flex flex-column align-items-start justify-content-around">
                            <span className="hint-text fs-11">Show up to</span>
                        </span>
                        </Dropdown.Item>
                        <Dropdown.Divider />
                        {recordsPerPage.map((option) => (
                            <Dropdown.Item
                                key={`${option.value}}`}
                                eventKey={option.value}
                                href="#"
                                data-records={option.value}
                                onClick={pagingHandler}
                                className={activeRecordsPerPage.value === option.value ? 'active' : ''}
                            >
                            <span className="d-flex flex-row align-items-center justify-content-around">
                                {activeRecordsPerPage.value === option.value ? (
                                    <i
                                        className="custom-icon icon"
                                        aria-label="Check"
                                        style={{
                                            width: 16,
                                            height: 16,
                                            backgroundColor: '#061223',
                                            strokeWidth: 2,
                                        }}
                                    />
                                ) : (
                                    <i style={{ width: 16, height: 16, display: 'inline-block' }} />
                                )}
                                <span className="hint-text fs-11 ml-3">{option.label} Item(s)</span>
                            </span>
                            </Dropdown.Item>
                        ))}
                    </>
                ) : null}
            
                {pagingHandler && resetListFilterHandler ? <Dropdown.Divider /> : null}
            
                {resetListFilterHandler ? (
                    <Dropdown.Item eventKey="clearFilters" onClick={resetListFilterHandler as any}>
                    <span className="d-flex flex-row align-items-center justify-content-around">
                        <i
                            className="custom-icon icon bg-danger"
                            aria-label="Trash"
                            style={{
                                width: 16,
                                height: 16,
                            }}
                        />
                        <span className="hint-text fs-11 ml-3 text-danger">Clear Filters</span>
                    </span>
                    </Dropdown.Item>
                ) : null}
            </Dropdown.Menu>
        </Dropdown>
    )
}
