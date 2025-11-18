<?php

namespace App\Domain\BankAccount\Service;

use App\Domain\BankAccount\Repository\GetBankAccountRepository;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetBankAccountService
{
    private GetBankAccountRepository $getBankAccountRepository;

    public function __construct(PDO $connection)
    {
        $this->getBankAccountRepository = new GetBankAccountRepository($connection);
    }

    public function getBankAccount(array $data): array
    {
        $sanitizedData = SanitizeBankAccountDataService::sanitizeData($data);
        $bankAccountData = MapBankAccountDataService::getData($sanitizedData);
        $bankAccountData->account_no = $data['accountNo'];

        $records = [];
        $recordPaging = DataPagination::getRecordOffset($sanitizedData['page']);
        $bankAccounts = $this->getBankAccountRepository->getBankAccount(
            $bankAccountData,
            $recordPaging['recordStart'],
            $recordPaging['recordsPerPage']
        );

        foreach ($bankAccounts as $bankAccount) {
            $records[] = [
                'bankId' => $bankAccount['bank_id'],
                'bankName' => Utilities::decodeUTF8($bankAccount['bank_name']),
                'bankAccountNo' => $bankAccount['bank_account_no'],
                'bankAccountName' => Utilities::decodeUTF8($bankAccount['bank_account_name']),
                'branchCode' => $bankAccount['branch_code']
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getBankAccountRepository->getBankAccount($bankAccountData);
            $pagination = DataPagination::getPagingLinks(
                $sanitizedData['page'],
                $countRecords->rowCount(),
                $recordPaging['recordsPerPage'],
            );

            return ['records' => $records, 'pagination' => $pagination];
        }

        return ['records' => $records];
    }
}
