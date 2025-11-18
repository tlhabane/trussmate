import { JobDesignInfo } from './JobDesignInfo';
import { JobLineItem } from './JobLineItem';

export interface Job {
    jobNo: string;
    jobDescription: string;
    designInfo: JobDesignInfo;
    lineItems: JobLineItem[];
    subtotal: number;
    vat: number;
    total: number;
}
