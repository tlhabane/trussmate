<?php

namespace App\Domain\Account\Service;

use App\Domain\Account\Repository\AccountNoExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetAccountNoService
{
    private AccountNoExistsRepository $accountNoExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->accountNoExistsRepository = new AccountNoExistsRepository($connection);
    }

    public function getUniqueId(int $length = 32): string
    {
        do {
            $account_no = Utilities::generateToken($length);
        } while(empty($account_no) || $this->accountNoExistsRepository->accountNoExists($account_no));

        return $account_no;
    }
}
