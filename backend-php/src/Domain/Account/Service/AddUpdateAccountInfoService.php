<?php

namespace App\Domain\Account\Service;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\Account\Service\AddAccountInfo\AddAccountInfoService;
use App\Domain\Account\Service\UpdateAccountInfo\UpdateAccountInfoService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddUpdateAccountInfoService
{
    private AddAccountInfoService $addAccountInfoService;
    private UpdateAccountInfoService $updateAccountInfoService;
    private GetAccountInfoService $getAccountInfoService;

    public function __construct(PDO $connection)
    {
        $this->addAccountInfoService = new AddAccountInfoService($connection);
        $this->updateAccountInfoService = new UpdateAccountInfoService($connection);
        $this->getAccountInfoService = new GetAccountInfoService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addUpdateAccountInfo(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);

        $sanitizedData = SanitizeAccountDataService::sanitizeData($data);
        if (!empty($data['logo']) && is_array($data['logo'])) {
            $logo_source = '';
            foreach ($data['logo'] as $logo) {
                $logo_source = $logo['source'];
            }
            $sanitizedData['logo'] = $logo_source;
        }
        $sanitizedData['accountNo'] = $data['accountNo'];

        $accounts = $this->getAccountInfoService->getAccount($sanitizedData['accountNo']);
        if (count($accounts['records']) > 0) {
            return $this->updateAccountInfoService->updateAccountInfo($sanitizedData);
        }

        return $this->addAccountInfoService->addAccountInfo($sanitizedData);
    }
}
