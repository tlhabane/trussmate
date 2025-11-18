<?php

namespace App\Domain\Transaction\Service\CancelTransaction;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\Transaction\Repository\AddTransactionRepository;
use App\Domain\Transaction\Repository\CancelTransactionRepository;
use App\Domain\Transaction\Repository\DeleteTransactionRepository;
use App\Domain\Transaction\Service\SanitizeTransactionDataService;
use App\Domain\Transaction\Service\SetTransactionDataService;
use App\Domain\Transaction\Service\GetTransactionIdService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use Exception;
use PDO;

final class CancelTransactionService
{
    private GetTransactionIdService $getTransactionIdService;
    private AddTransactionRepository $addTransactionRepository;
    private CancelTransactionRepository $cancelTransactionRepository;
    private DeleteTransactionRepository $deleteTransactionRepository;
    private ValidateCancelTransactionDataService $validateCancelTransactionDataService;

    public function __construct(PDO $connection)
    {
        $this->getTransactionIdService = new GetTransactionIdService($connection);
        $this->addTransactionRepository = new AddTransactionRepository($connection);
        $this->cancelTransactionRepository = new CancelTransactionRepository($connection);
        $this->deleteTransactionRepository = new DeleteTransactionRepository($connection);
        $this->validateCancelTransactionDataService = new ValidateCancelTransactionDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function cancelTransaction(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);
        $sanitizedData = SanitizeTransactionDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $sanitizedData['sessionUsername'] = $data['sessionUsername'];
        $sanitizedData['sessionUserRole'] = $data['sessionUserRole'];
        $validatedData = $this->validateCancelTransactionDataService->validateData($sanitizedData);

        $validatedData['transactionId'] = $this->getTransactionIdService->getId();
        $transactionData = SetTransactionDataService::set(array_merge($sanitizedData, $validatedData));
        $transactionData->account_no = $data['accountNo'];
        $transactionData->user_id = $data['sessionUsername'];

        try {
            if ($this->addTransactionRepository->addTransaction($transactionData)) {
                if ($this->cancelTransactionRepository->cancelTransaction($sanitizedData['transactionId'])) {
                    return [
                        'success' => 'Transaction cancelled',
                        'id' => $sanitizedData['transactionId']
                    ];
                }
                $this->deleteTransactionRepository->deleteTransaction($transactionData->transaction_id);
            }
        } catch (Exception $exception) {
            $this->deleteTransactionRepository->deleteTransaction($transactionData->transaction_id);
            $this->cancelTransactionRepository->cancelTransaction($sanitizedData['transactionId'], 0);
            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
