<?php

namespace App\Domain\Transaction\Service;

use App\Domain\Transaction\Repository\TransactionIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetTransactionIdService
{
    private TransactionIdExistsRepository $transactionIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->transactionIdExistsRepository = new TransactionIdExistsRepository($connection);
    }

    public function getId(int $length = 64): string
    {
        do {
            $transaction_id = Utilities::generateToken($length);
        } while (empty($transaction_id) || $this->transactionIdExistsRepository->idExists($transaction_id));

        return $transaction_id;
    }
}
