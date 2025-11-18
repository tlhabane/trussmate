<?php

namespace App\Domain\BankAccount\Service\AddBankAccount;

use App\Domain\BankAccount\Repository\BankAccountExistsRepository;
use App\Domain\BankAccount\Service\MapBankAccountDataService;
use App\Domain\BankAccount\Service\ValidateAddUpdateBankAccountData;
use App\Exception\ValidationException;
use PDO;

final class ValidateAddBankAccountDataService
{
    private BankAccountExistsRepository $bankAccountExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->bankAccountExistsRepository = new BankAccountExistsRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $sanitizedData): array
    {
        ValidateAddUpdateBankAccountData::validateData($sanitizedData);

        $bankAccountData = MapBankAccountDataService::getData($sanitizedData);
        $bankAccountData->account_no = $sanitizedData['accountNo'];
        if ($this->bankAccountExistsRepository->accountExists($bankAccountData)) {
            throw new ValidationException(
                'A bank account with similar details is already registered.',
                422,
                [
                    'bankAccountNo'
                ]
            );
        }

        return $sanitizedData;
    }
}
