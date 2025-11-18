<?php

namespace App\Domain\Account\Service\UpdateAccountStatus;

use App\Contract\DataValidationContract;
use App\Domain\Account\Repository\AccountNoExistsRepository;
use App\Exception\ValidationException;
use PDO;

final class ValidateUpdateAccountStatusDataService implements DataValidationContract
{
    private AccountNoExistsRepository $accountNoExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->accountNoExistsRepository = new AccountNoExistsRepository($connection);
    }

    public function validateData(array $data): array
    {
        if (empty($data['accountNo']) || !$this->accountNoExistsRepository->accountNoExists($data['accountNo'])) {
            throw new ValidationException('Invalid or missing account details');
        }
        if (empty($data['accountStatus'])) {
            throw new ValidationException('Data validation error', 422, [
                'accountStatus' => 'Invalid account status provided'
            ]);
        }

        return $data;
    }
}
