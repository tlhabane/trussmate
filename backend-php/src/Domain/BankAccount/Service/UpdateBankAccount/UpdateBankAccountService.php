<?php

namespace App\Domain\BankAccount\Service\UpdateBankAccount;

use App\Domain\BankAccount\Service\SanitizeBankAccountDataService;
use App\Domain\BankAccount\Service\MapBankAccountDataService;
use App\Domain\BankAccount\Repository\UpdateBankAccountRepository;
use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateBankAccountService
{
    private ValidateUpdateBankAccountDataService $validateUpdateBankAccountDataService;
    private UpdateBankAccountRepository $updateBankAccountRepository;

    public function __construct(PDO $connection)
    {
        $this->validateUpdateBankAccountDataService = new ValidateUpdateBankAccountDataService($connection);
        $this->updateBankAccountRepository = new UpdateBankAccountRepository($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateBankAccount(array $data): array
    {
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);

        $sanitizedData = SanitizeBankAccountDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $validatedData = $this->validateUpdateBankAccountDataService->validateData($sanitizedData);
        $bankAccountData = MapBankAccountDataService::getData($validatedData);

        if ($this->updateBankAccountRepository->updateBankAccount($bankAccountData)) {
            return [
                'success' => 'Bank account details updated.',
                'id' => $bankAccountData->bank_id
            ];
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
