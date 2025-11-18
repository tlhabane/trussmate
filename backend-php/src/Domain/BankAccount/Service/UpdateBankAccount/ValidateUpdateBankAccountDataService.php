<?php

namespace App\Domain\BankAccount\Service\UpdateBankAccount;

use App\Domain\BankAccount\Repository\BankAccountExistsRepository;
use App\Domain\BankAccount\Repository\GetBankAccountByIdRepository;
use App\Domain\BankAccount\Service\MapBankAccountDataService;
use App\Domain\BankAccount\Service\ValidateAddUpdateBankAccountData;
use App\Exception\ValidationException;
use PDO;

final class ValidateUpdateBankAccountDataService
{
    private GetBankAccountByIdRepository $getBankAccountByIdRepository;
    private BankAccountExistsRepository $bankAccountExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->getBankAccountByIdRepository = new GetBankAccountByIdRepository($connection);
        $this->bankAccountExistsRepository = new BankAccountExistsRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $sanitizedData): array
    {
        $bankAccounts = $this->getBankAccountByIdRepository->getBankAccount($sanitizedData['bankId']);
        if (empty($sanitizedData['bankId']) || !($bankAccounts->rowCount() > 0)) {
            throw new ValidationException('Invalid or missing bank account details.');
        }

        ValidateAddUpdateBankAccountData::validateData($sanitizedData);

        foreach ($bankAccounts as $bankAccount) {
            if ($bankAccount['bank_name'] !== $sanitizedData['bankName'] ||
                $bankAccount['bank_account_no'] !== $sanitizedData['bankAccountNo']
            ) {
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
            }
        }

        return $sanitizedData;
    }
}
