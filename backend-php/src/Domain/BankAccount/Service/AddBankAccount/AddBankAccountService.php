<?php

namespace App\Domain\BankAccount\Service\AddBankAccount;

use App\Domain\BankAccount\Service\SanitizeBankAccountDataService;
use App\Domain\BankAccount\Service\MapBankAccountDataService;
use App\Domain\BankAccount\Repository\AddNewBankAccountRepository;
use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddBankAccountService
{
    private AddNewBankAccountRepository $addNewBankAccountRepository;
    private ValidateAddBankAccountDataService $validateAddBankAccountDataService;
    private GetBankIdService $getBankIdService;

    public function __construct(PDO $connection)
    {
        $this->addNewBankAccountRepository = new AddNewBankAccountRepository($connection);
        $this->validateAddBankAccountDataService = new ValidateAddBankAccountDataService($connection);
        $this->getBankIdService = new GetBankIdService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addBankAccount(array $data): array
    {
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);

        $sanitizedData = SanitizeBankAccountDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $validatedData = $this->validateAddBankAccountDataService->validateData($sanitizedData);
        $bankAccountData = MapBankAccountDataService::getData($validatedData);
        $bankAccountData->account_no = $data['accountNo'];
        $bankAccountData->bank_id = $this->getBankIdService->getBankId();

        if ($this->addNewBankAccountRepository->addNewBankAccount($bankAccountData)) {
            return [
                'success' => 'Bank account details saved.',
                'id' => $bankAccountData->bank_id
            ];
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
