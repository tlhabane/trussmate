import { ClickOrTouchEvent, ReactSelectEvent, ReactSelectSingleOption, SetState } from '../../../types';

type RecordStateActionType = SetState<any[]>;
type RecordFilterStateActionType = SetState<Record<string, any>>;
type ReloadRecordStateActionType = SetState<boolean>;

export const pagingLinkClickHandler = (
    event: ClickOrTouchEvent<HTMLAnchorElement>,
    setRecordFilterState: RecordFilterStateActionType,
    setReloadRecords: ReloadRecordStateActionType,
) => {
    event.preventDefault();
    const nextPage = event.currentTarget.dataset.page;
    if (nextPage) {
        setRecordFilterState((currentRecordFilterState) => ({
            ...currentRecordFilterState,
            page: +nextPage,
        }));
        setReloadRecords(true);
    }
};

const sliceRecords = (updatedRecordCount: number, setRecords: RecordStateActionType) => {
    setRecords((currentRecords) => {
        const updatedRecords = currentRecords.slice(0, updatedRecordCount + 1);
        return [...updatedRecords];
    });
};

export const recordsPerPageSelectionHandler = (
    event: ClickOrTouchEvent<HTMLElement>,
    setRecords: RecordStateActionType,
    setRecordFilterState: RecordFilterStateActionType,
    setReloadRecords: ReloadRecordStateActionType,
) => {
    event.preventDefault();
    const recordsPerPage = event.currentTarget.dataset.records;
    if (recordsPerPage) {
        let reloadRecords = false;
        setRecordFilterState((currentRecordFilterState) => {
            const currentPage = currentRecordFilterState.page as number;
            const currentRecordsPerPage = currentRecordFilterState.recordsPerPage as number;
            
            reloadRecords = +recordsPerPage > currentRecordsPerPage;
            
            if (currentRecordsPerPage > +recordsPerPage) {
                sliceRecords(+recordsPerPage, setRecords);
            }
            
            return {
                ...currentRecordFilterState,
                recordsPerPage: +recordsPerPage,
                page: reloadRecords ? 1 : currentPage,
            };
        });
        
        setReloadRecords(reloadRecords);
    }
};

export const updateRecordsPerPageHandler = (
    setRecords: RecordStateActionType,
    setRecordFilterState: RecordFilterStateActionType,
    setReloadRecords: ReloadRecordStateActionType,
    option?: ReactSelectEvent,
) => {
    let reloadRecords = false;
    const selectedRecordsPerPageOption = (option as ReactSelectSingleOption)?.value || '0';
    
    setRecordFilterState((currentRecordFilterState) => {
        const currentPage = currentRecordFilterState.page as number;
        const currentRecordsPerPage = currentRecordFilterState.recordsPerPage as number;
        
        reloadRecords = +selectedRecordsPerPageOption > currentRecordsPerPage;
        
        if (currentRecordsPerPage > +selectedRecordsPerPageOption) {
            sliceRecords(+selectedRecordsPerPageOption, setRecords);
        }
        
        return {
            ...currentRecordFilterState,
            recordsPerPage: +selectedRecordsPerPageOption,
            page: reloadRecords ? 1 : currentPage,
        };
    });
    
    setReloadRecords(reloadRecords);
};
