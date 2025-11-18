<?php

namespace App\Domain\Account\Service\UpdateAccountInfo;

use App\Domain\Account\Repository\UpdateAccountInfoRepository;
use App\Domain\Account\Service\MapAccountDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateAccountInfoService
{
    private UpdateAccountInfoRepository $updateAccountInfoRepository;
    private ValidateUpdateAccountInfoDataService $validateUpdateAccountDataService;

    public function __construct(PDO $connection)
    {
        $this->updateAccountInfoRepository = new UpdateAccountInfoRepository($connection);
        $this->validateUpdateAccountDataService = new ValidateUpdateAccountInfoDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateAccountInfo(array $sanitizedData): array
    {
        $validatedData = $this->validateUpdateAccountDataService->validateData($sanitizedData);

        $accountData = MapAccountDataService::mapData($validatedData);
        $accountData->account_no = $sanitizedData['accountNo'];
        if ($this->updateAccountInfoRepository->updateAccountInfo($accountData)) {
            return [
                'success' => 'Account details updated',
                'id' => $accountData->account_no
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
