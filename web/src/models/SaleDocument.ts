import { DocumentType } from './DocumentType';

export interface SaleDocument {
    docId: string;
    docType: DocumentType;
    docSrc: string;
    docName: string;
    firstName: string;
    lastName: string;
    docDate: string;
}
