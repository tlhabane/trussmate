export interface Payment {
    invoiceNo: string;
    saleTaskId: string;
    paymentDate: string;
    paymentAmount: number;
    paymentDesc: string;
    sendConfirmation: number;
    overrideAmount: number;
}
