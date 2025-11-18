<?php

namespace App\Domain\Account\Service;

use App\Domain\Account\Repository\AddAccountRepository;
use App\Domain\User\Service\AddSuperAdminUserService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddAccountService
{
    private AddAccountRepository $addAccountRepository;
    private GetAccountNoService $getAccountNoService;
    private AddSuperAdminUserService $addSuperAdminUserService;

    public function __construct(PDO $connection)
    {
        $this->addAccountRepository = new AddAccountRepository($connection);
        $this->getAccountNoService = new GetAccountNoService($connection);
        $this->addSuperAdminUserService = new AddSuperAdminUserService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addAccount(array $data): array
    {
        try {
            $data['accountNo'] = $this->getAccountNoService->getUniqueId();
            /* Add super admin */
            $this->addSuperAdminUserService->addUser($data);
            $sanitizedData = SanitizeAccountDataService::sanitizeData($data);
            if ($this->addAccountRepository->addAccountInfo($sanitizedData['accountNo'])) {
                return [
                    'success' => 'New account created',
                    'id' => $sanitizedData['accountNo']
                ];
            }
        } catch (RuntimeException|ValidationException $exception) {
            // TODO: Rollback - delete account
            if ($exception instanceof ValidationException) {
                throw new ValidationException($exception->getMessage(), $exception->getCode(), $exception->getErrors());
            }

            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
