<?php

namespace App\Domain\User\Service\AddUserAccountInfo;

use App\Contract\DataValidationContract;
use App\Domain\User\Repository\UserPhoneNumberExistsRepository;
use App\Domain\User\Repository\UserEmailExistsRepository;
use App\Exception\ValidationException;
use PDO;

final class ValidateAddUserInfoAccountInfoService implements DataValidationContract
{
    private UserEmailExistsRepository $emailExistsRepository;
    private UserPhoneNumberExistsRepository $phoneNumberExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->emailExistsRepository = new UserEmailExistsRepository($connection);
        $this->phoneNumberExistsRepository = new UserPhoneNumberExistsRepository($connection);
    }

    public function validateData(array $data): array
    {
        $fields = [];
        if (empty($data['firstName'])) {
            $fields['firstName'] = 'Invalid first name provided';
        }
        // Validate phone number
        if (!empty($data['tel']) && $this->phoneNumberExistsRepository->numberExists($data['tel'])) {
            $fields['tel'] = sprintf('Phone number provided (%s) is already registered', $data['tel']);
        }
        if (!empty($data['altTel']) && $this->phoneNumberExistsRepository->numberExists($data['altTel'])) {
            $fields['altTel'] = sprintf('Phone number provided (%s) is already registered', $data['altTel']);
        }
        // Validate email address
        if (empty($data['email'])) {
            $fields['email'] = 'Invalid email address provided';
        } elseif ($this->emailExistsRepository->emailExists($data['email'])) {
            $fields['email'] = sprintf('Email address provided (%s) is already registered', $data['email']);
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
