<?php

namespace App\Domain\Account\Service\AddAccountInfo;

use App\Domain\Account\Repository\AddAccountInfoRepository;
use App\Domain\Account\Service\MapAccountDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddAccountInfoService
{
    private AddAccountInfoRepository $addAccountInfoRepository;
    private ValidateAddAccountInfoDataService $validateAddAccountInfoDataService;

    public function __construct(PDO $connection)
    {
        $this->addAccountInfoRepository = new AddAccountInfoRepository($connection);
        $this->validateAddAccountInfoDataService = new ValidateAddAccountInfoDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addAccountInfo(array $sanitizedData): array
    {
        $validatedData = $this->validateAddAccountInfoDataService->validateData($sanitizedData);

        $accountData = MapAccountDataService::mapData($validatedData);
        $accountData->account_no = $sanitizedData['accountNo'];
        if ($this->addAccountInfoRepository->addAccountInfo($accountData)) {
            return [
                'success' => 'Account details saved',
                'id' => $accountData->account_no
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
