<?php

namespace App\Domain\Account\Service\UpdateAccountStatus;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\Account\Repository\UpdateAccountStatusRepository;
use App\Domain\Account\Service\SanitizeAccountDataService;
use App\Domain\Account\Service\MapAccountDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateAccountStatusService
{
    private UpdateAccountStatusRepository $updateAccountStatusRepository;
    private ValidateUpdateAccountStatusDataService $validateUpdateAccountStatusDataService;

    public function __construct(PDO $connection)
    {
        $this->updateAccountStatusRepository = new UpdateAccountStatusRepository($connection);
        $this->validateUpdateAccountStatusDataService = new ValidateUpdateAccountStatusDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateStatus(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['userRole']);

        $sanitizedData = SanitizeAccountDataService::sanitizeData($data);
        $validatedData = $this->validateUpdateAccountStatusDataService->validateData($sanitizedData);

        $accountData = MapAccountDataService::mapData($validatedData);
        $accountData->account_no = $sanitizedData['accountNo'];
        if ($this->updateAccountStatusRepository->updateStatus($accountData)) {
            return [
                'success' => 'Account status updated',
                'id' => $accountData->account_no
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
