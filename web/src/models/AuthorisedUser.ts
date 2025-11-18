import { UserRole } from './UserRole';

export interface AuthorisedUser {
    token: string;
    accountNo: string;
    regionId: string;
    regionName: string;
    firstName: string
    userRole: UserRole
}
