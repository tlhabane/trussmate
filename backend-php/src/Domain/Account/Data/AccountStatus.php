<?php

namespace App\Domain\Account\Data;

enum AccountStatus: int
{
    case pending = 1;
    case active = 2;
    case cancelled = 3;
    case suspended = 4;
}
