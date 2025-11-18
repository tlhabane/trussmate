<?php

namespace App\Domain\BankAccount\Service\AddBankAccount;

use App\Domain\BankAccount\Repository\BankIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetBankIdService
{
    private BankIdExistsRepository $bankIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->bankIdExistsRepository = new BankIdExistsRepository($connection);
    }

    public function getBankId(): string
    {
        do {
            $bank_id = Utilities::generateToken();
        } while (empty($bank_id) || $this->bankIdExistsRepository->bankIdExists($bank_id));

        return $bank_id;
    }
}
