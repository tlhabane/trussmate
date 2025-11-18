export interface ContactPerson {
  contactId: string;
  firstName: string;
  lastName: string;
  jobTitle: string;
  tel: string;
  altTel: string;
  email: string;
}

export interface CustomerContactPerson extends ContactPerson {
  customerId: string;
}
