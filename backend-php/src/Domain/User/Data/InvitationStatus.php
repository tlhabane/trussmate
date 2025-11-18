<?php

namespace App\Domain\User\Data;

enum InvitationStatus: int
{
    case pending = 1;
    case accepted = 2;
    case rejected = 3;
    case expired = 4;
}
