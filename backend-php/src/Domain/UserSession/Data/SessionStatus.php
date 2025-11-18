<?php

namespace App\Domain\UserSession\Data;

enum SessionStatus: int
{
    case active = 1;
    case ended = 2;
    case expired = 3;
}
