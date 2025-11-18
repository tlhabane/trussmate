export enum CustomerType {
  BUSINESS = 'business',
  INDIVIDUAL = 'individual'
}

export interface Customer {
  customerId: string;
  customerType: 'business' | 'individual';
  customerName: string;
  registrationNo: string;
  vatNo: string;
  tel: string;
  altTel: string;
  email: string;
  web: string;
}
