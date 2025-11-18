<?php

namespace App\Domain\BankAccount\Service;

use App\Domain\BankAccount\Repository\BankIdExistsRepository;
use App\Domain\BankAccount\Repository\DeleteBankAccountRepository;
use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class DeleteBankAccountService
{
    private BankIdExistsRepository $bankIdExistsRepository;
    private DeleteBankAccountRepository $deleteBankAccountRepository;

    public function __construct(PDO $connection)
    {
        $this->bankIdExistsRepository = new BankIdExistsRepository($connection);
        $this->deleteBankAccountRepository = new DeleteBankAccountRepository($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function deleteBankAccount(array $data): array
    {
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);

        $sanitizedData = SanitizeBankAccountDataService::sanitizeData($data);

        if (empty($sanitizedData['bankId']) || !$this->bankIdExistsRepository->bankIdExists($sanitizedData['bankId'])) {
            throw new ValidationException(
                'Invalid or missing bank account details.'
            );
        }

        if ($this->deleteBankAccountRepository->deleteBankAccount($sanitizedData['bankId'])) {
            return [
                'success' => 'Bank account deleted.',
                'id' => $sanitizedData['bankId']
            ];
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
