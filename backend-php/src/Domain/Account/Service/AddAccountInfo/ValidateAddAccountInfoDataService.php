<?php

namespace App\Domain\Account\Service\AddAccountInfo;

use App\Contract\DataValidationContract;
use App\Domain\Account\Repository\AccountEmailExistsRepository;
use App\Domain\Account\Repository\AccountPhoneNumberExistsRepository;
use App\Exception\ValidationException;
use PDO;

final class ValidateAddAccountInfoDataService implements DataValidationContract
{
    private AccountEmailExistsRepository $accountEmailExistsRepository;
    private AccountPhoneNumberExistsRepository $accountPhoneNumberExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->accountEmailExistsRepository = new AccountEmailExistsRepository($connection);
        $this->accountPhoneNumberExistsRepository = new AccountPhoneNumberExistsRepository($connection);
    }

    public function validateData(array $data): array
    {
        if (empty($data['accountNo'])) {
            throw new ValidationException('Invalid or missing account details');
        }

        if (!empty($data['logo']) && !file_exists($data['logo'])) {
            throw new ValidationException('Invalid or missing logo file');
        }

        $fields = [];
        if (!empty($data['registeredNo']) && empty($data['registeredName'])) {
            $fields['registeredName'] = 'Invalid union name provided';
        }
        if (empty($data['registeredName']) && empty($data['tradingName'])) {
            $fields['tradingName'] = 'Invalid union name provided';
        }

        // Validate phone number
        if (empty($data['tel'])) {
            $fields['tel'] = 'Invalid phone number provided';
        } elseif ($this->accountPhoneNumberExistsRepository->numberExists($data['tel'])) {
            $fields['tel'] = sprintf('Phone number provided (%s) is already registered', $data['tel']);
        }

        if (!empty($data['altTel']) && $this->accountPhoneNumberExistsRepository->numberExists($data['altTel'])) {
            $fields['altTel'] = sprintf('Phone number provided (%s) is already registered', $data['altTel']);
        }

        // Validate email address
        if (empty($data['email'])) {
            $fields['email'] = 'Invalid email address provided';
        } elseif ($this->accountEmailExistsRepository->emailExists($data['email'])) {
            $fields['email'] = sprintf('Email address provided (%s) is already registered', $data['email']);
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
