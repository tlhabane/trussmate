<?php

namespace App\Domain\Account\Service\UpdateAccountInfo;

use App\Contract\DataValidationContract;
use App\Domain\Account\Service\GetAccountInfoService;
use App\Domain\Account\Repository\AccountEmailExistsRepository;
use App\Domain\Account\Repository\AccountPhoneNumberExistsRepository;
use App\Exception\ValidationException;
use App\Util\Utilities;
use PDO;

final class ValidateUpdateAccountInfoDataService implements DataValidationContract
{
    private GetAccountInfoService $getAccountService;
    private AccountEmailExistsRepository $accountEmailExistsRepository;
    private AccountPhoneNumberExistsRepository $accountPhoneNumberExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->getAccountService = new GetAccountInfoService($connection);
        $this->accountEmailExistsRepository = new AccountEmailExistsRepository($connection);
        $this->accountPhoneNumberExistsRepository = new AccountPhoneNumberExistsRepository($connection);
    }

    public function validateData(array $data): array
    {
        $accounts = $this->getAccountService->getAccount($data['accountNo']);
        if (empty($data['accountNo']) && count($accounts['records']) !== 1) {
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

        foreach ($accounts['records'] as $account) {
            // Validate phone number
            $currentNo = Utilities::removeNonDigits($account['tel']);
            $newNo = Utilities::removeNonDigits($data['tel']);
            $phoneUpdate = $currentNo !== $newNo;

            if (empty($data['tel'])) {
                $fields['tel'] = 'Invalid phone number provided';
            } elseif ($phoneUpdate && $this->accountPhoneNumberExistsRepository->numberExists($data['tel'])) {
                $fields['tel'] = sprintf('Phone number provided (%s) is already registered', $data['tel']);
            }

            if (!empty($data['altTel'])) {
                $currentNo = Utilities::removeNonDigits($account['altTel']);
                $newNo = Utilities::removeNonDigits($data['altTel']);
                $phoneUpdate = $currentNo !== $newNo;
                if ($phoneUpdate && $this->accountPhoneNumberExistsRepository->numberExists($data['altTel'])) {
                    $fields['altTel'] = sprintf('Phone number provided (%s) is already registered', $data['altTel']);
                }
            }

            // Validate email address
            $emailUpdate = trim(strtolower($account['email'])) !== trim(strtolower($data['email']));
            if (empty($data['email'])) {
                $fields['email'] = 'Invalid email address provided';
            } elseif ($emailUpdate && $this->accountEmailExistsRepository->emailExists($data['email'])) {
                $fields['email'] = sprintf('Email address provided (%s) is already registered', $data['email']);
            }

            if (empty($data['logo'])) {
                $data['logo'] = $account['logo'];
            }
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
