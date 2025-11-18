<?php

namespace App\Domain\Transaction\Service;

use App\Domain\Transaction\Repository\GetTransactionByIdRepository;
use App\Exception\ValidationException;
use App\Util\Utilities;
use PDO;

final class GetTransactionByIdService
{
    private GetTransactionByIdRepository $getTransactionByIdRepository;

    public function __construct(PDO $connection)
    {
        $this->getTransactionByIdRepository = new GetTransactionByIdRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function getTransaction(array $data): array
    {
        $sanitizedData = SanitizeTransactionDataService::sanitizeData($data);
        $transaction = $this->getTransactionByIdRepository->getTransaction($sanitizedData['transactionId']);
        $record = $transaction->fetch(PDO::FETCH_ASSOC);
        if (!$record) {
            return [];
        }

        return [
            'accountNo' => $record['account_no'],
            'userId' => $record['user_id'],
            'invoiceNo' => Utilities::addPadding($record['invoice_no']),
            'transactionId' => $record['transaction_id'],
            'transactionCancelled' => (bool)$record['transaction_cancelled'],
            'transactionAmount' => (float)$record['transaction_amount'],
            'transactionType' => $record['transaction_type'],
            'transactionDate' => date('c', strtotime($record['transaction_date'])),
            'transactionMethod' => $record['transaction_method'],
            'transactionDesc' => Utilities::decodeUTF8($record['transaction_desc']),
            'postingDate' => date('c', strtotime($record['posting_date'])),
            'saleId' => $record['sale_id'],
            'saleNo' => Utilities::addPadding($record['sale_no']),
            'customerId' => $record['customer_id'],
            'customerName' => Utilities::decodeUTF8($record['customer_name']),
            'customerEmail' => $record['customer_email'],
            'contactId' => $record['contact_id'],
            'firstName' => Utilities::decodeUTF8($record['first_name']),
            'lastName' => Utilities::decodeUTF8($record['last_name']),
            'contactEmail' => $record['contact_email'],
            'agentFirstName' => Utilities::decodeUTF8($record['agent_first_name']),
            'agentLastName' => Utilities::decodeUTF8($record['agent_last_name']),
            'agentEmail' => $record['agent_email'],
            'saleDate' => date('c', strtotime($record['sale_date'])),
            'invoiceDate' => date('c', strtotime($record['invoice_date'])),
            'jobTotal' => (float)$record['job_total'],
        ];
    }
}
