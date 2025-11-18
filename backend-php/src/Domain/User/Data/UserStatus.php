<?php

namespace App\Domain\User\Data;

enum UserStatus: int
{
    case none = 0;
    case active = 1;
    case inactive = 2;
    case locked = 3;
}
