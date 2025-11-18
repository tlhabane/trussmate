export interface Address {
    addressId: string;
    billingAddress: number;
    placeId: string;
    fullAddress: string;
    streetAddress: string;
    suburb: string;
    city: string;
    municipality: string;
    province: string;
    country: string;
    postalCode: string;
    latitude: number;
    longitude: number;
}

export interface CustomerAddress extends Address {
    customerId: string;
}
