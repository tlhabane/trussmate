import React from 'react';
import { Link } from 'react-router-dom';
import { Pagination } from '../../types';

const iconSize = { width: 13, height: 13 };

export const PaginatorLinks: React.FC<Pagination> = ({
    compact = true,
    totalRecords,
    currentPage,
    totalPages,
    first,
    last,
    pages,
    onClick,
}) => {
    if (totalRecords > 0) {
        if (compact) {
            return (
                <div className="d-flex align-items-center justify-content-end">
                    <ul className={`pagination ${compact ? 'compact-pagination' : ''}`}>
                        <li>
                            <Link
                                className={currentPage <= 1 ? 'disabled' : ''}
                                onClick={onClick}
                                to={pages[currentPage - 1]?.url || '/'}
                                data-page={`${currentPage - 1}`}
                            >
                                <i className="custom-icon icon" style={iconSize} aria-label="Chevron-Left" />
                            </Link>
                        </li>
                        <li>
                            <Link
                                className={currentPage >= totalPages ? 'disabled' : ''}
                                onClick={onClick}
                                to={pages[currentPage]?.url || '/'}
                                data-page={`${currentPage + 1}`}
                            >
                                <i className="custom-icon icon" style={iconSize} aria-label="Chevron-Right" />
                            </Link>
                        </li>
                    </ul>
                </div>
            );
        }
        return (
            <div className="d-flex align-items-center justify-content-end">
                <ul className="pagination">
                    <li>
                        <Link
                            className={first === '' && currentPage === 1 ? 'disabled' : ''}
                            onClick={onClick}
                            to={first}
                            data-page="1"
                        >
                            <i className="custom-icon icon" style={iconSize} aria-label="Chevrons-Left" />
                        </Link>
                    </li>
                    <li>
                        <Link
                            className={currentPage <= 1 ? 'disabled' : ''}
                            onClick={onClick}
                            to={pages[currentPage - 1]?.url || '/'}
                            data-page={`${currentPage - 1}`}
                        >
                            <i className="custom-icon icon" style={iconSize} aria-label="Chevron-Left" />
                        </Link>
                    </li>
                    {pages.map((page) => (
                        <li key={page.url}>
                            <Link
                                onClick={onClick}
                                to={page.url}
                                data-page={`${page.page}`}
                                className={page.currentPage ? 'active' : ''}
                            >
                                {page.page}
                            </Link>
                        </li>
                    ))}
                    <li>
                        <Link
                            className={currentPage >= totalPages ? 'disabled' : ''}
                            onClick={onClick}
                            to={pages[currentPage]?.url || '/'}
                            data-page={`${currentPage + 1}`}
                        >
                            <i className="custom-icon icon" style={iconSize} aria-label="Chevron-Right" />
                        </Link>
                    </li>
                    <li>
                        <Link
                            className={last === '' && currentPage === totalPages ? 'disabled' : ''}
                            onClick={onClick}
                            to={last}
                            data-page={`${totalPages}`}
                        >
                            <i className="custom-icon icon" style={iconSize} aria-label="Chevrons-Right" />
                        </Link>
                    </li>
                </ul>
            </div>
        );
    }
    
    return null;
};

