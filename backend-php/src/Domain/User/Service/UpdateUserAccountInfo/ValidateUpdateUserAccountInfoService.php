<?php

namespace App\Domain\User\Service\UpdateUserAccountInfo;

use App\Contract\DataValidationContract;
use App\Domain\User\Repository\UserPhoneNumberExistsRepository;
use App\Domain\User\Repository\UserEmailExistsRepository;
use App\Domain\User\Service\GetUserService;
use App\Exception\ValidationException;
use App\Util\Utilities;
use PDO;

final class ValidateUpdateUserAccountInfoService implements DataValidationContract
{
    private GetUserService $getUserService;
    private UserEmailExistsRepository $emailExistsRepository;
    private UserPhoneNumberExistsRepository $phoneNumberExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->getUserService = new GetUserService($connection);
        $this->emailExistsRepository = new UserEmailExistsRepository($connection);
        $this->phoneNumberExistsRepository = new UserPhoneNumberExistsRepository($connection);
    }

    public function validateData(array $data): array
    {
        $users = $this->getUserService->getUser([
            'accountNo' => $data['accountNo'],
            'userRole' => 'super_admin',
            'username' => $data['username']
        ]);

        if (empty($data) && count($users['records']) !== 1) {
            throw new ValidationException('Invalid or missing user details');
        }

        $fields = [];
        if (empty($data['firstName'])) {
            $fields['firstName'] = 'Invalid first name provided';
        }

        foreach ($users['records'] as $user) {
            // Validate phone number
            $currentNo = Utilities::removeNonDigits($user['tel']);
            $newNo = Utilities::removeNonDigits($data['tel']);
            $phoneUpdate = $currentNo !== $newNo;

            if (!empty($data['tel'])) {
                if ($phoneUpdate && $this->phoneNumberExistsRepository->numberExists($data['tel'])) {
                    $fields['tel'] = sprintf('Phone number provided (%s) is already registered', $data['tel']);
                }
            }

            if (!empty($data['altTel'])) {
                $currentNo = Utilities::removeNonDigits($user['altTel']);
                $newNo = Utilities::removeNonDigits($data['altTel']);
                $phoneUpdate = $currentNo !== $newNo;
                if ($phoneUpdate && $this->phoneNumberExistsRepository->numberExists($data['altTel'])) {
                    $fields['altTel'] = sprintf('Phone number provided (%s) is already registered', $data['altTel']);
                }
            }

            // Validate email address
            $emailUpdate = trim(strtolower($user['email'])) !== trim(strtolower($data['email']));
            if (empty($data['email'])) {
                $fields['email'] = 'Invalid email address provided';
            } elseif ($emailUpdate && $this->emailExistsRepository->emailExists($data['email'])) {
                $fields['email'] = sprintf('Email address provided (%s) is already registered', $data['email']);
            }
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
