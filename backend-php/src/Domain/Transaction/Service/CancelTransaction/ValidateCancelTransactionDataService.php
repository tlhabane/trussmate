<?php

namespace App\Domain\Transaction\Service\CancelTransaction;

use App\Domain\Transaction\Service\GetTransactionByIdService;
use App\Domain\Transaction\Data\TransactionType;
use App\Exception\ValidationException;
use App\Util\Utilities;
use PDO;

final class ValidateCancelTransactionDataService
{
    private GetTransactionByIdService $getTransactionByIdService;

    public function __construct(PDO $connection)
    {
        $this->getTransactionByIdService = new GetTransactionByIdService($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        $transaction = $this->getTransactionByIdService->getTransaction($data);
        if (empty($transaction)) {
            throw new ValidationException('Invalid or missing transaction details');
        }

        if ($transaction['transactionCancelled']) {
            throw new ValidationException('Selected transaction has already been cancelled');
        }
        $description = empty($data['transactionDesc']) ? 'Incorrect Payment or Charge' : $data['transactionDesc'];
        $type = $transaction['transactionAmount'] > 0 ? TransactionType::CREDIT_MEMO : TransactionType::DEBIT_MEMO;
        return [
            'invoiceNo' => Utilities::removePadding($transaction['invoiceNo']),
            'transactionAmount' => -1 * $transaction['transactionAmount'],
            'transactionType' => $type->value,
            'transactionMethod' => $transaction['transactionMethod'],
            'transactionDate' => date('Y-m-d'),
            'transactionDesc' => $description,
        ];
    }
}
