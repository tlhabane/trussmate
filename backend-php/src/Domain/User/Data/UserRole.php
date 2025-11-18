<?php

namespace App\Domain\User\Data;

enum UserRole: int
{
    case none = 0;
    case super_admin = 1;
    case admin = 2;
    case estimator = 3;
    case production = 4;
    case customer = 5;
    case user = 6;
    case system = 7;
}
