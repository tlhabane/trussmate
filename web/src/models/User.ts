import { UserRole } from './UserRole';
import { UserStatus } from './UserStatus';

export interface User {
    username: string;
    userRole: UserRole;
    userStatus: UserStatus;
    firstName: string;
    lastName: string;
    jobTitle: string;
    email: string;
    tel: string;
    altTel: string;
}
