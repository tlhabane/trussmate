export interface DebitMemo {
    amount: number;
    reason: string;
    comments: string;
    sendConfirmation: number;
    transactionId: string;
}
