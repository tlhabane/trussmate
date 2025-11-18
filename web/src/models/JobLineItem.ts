export interface LineItem {
    name: string;
    description: string;
    quantity: string;
}

export interface JobLineItem {
    category: string;
    amount: number;
    items: LineItem[];
}
