<?php

namespace App\Domain\BankAccount\Service;

use App\Domain\BankAccount\Data\BankAccountData;

final class MapBankAccountDataService
{
    public static function getData(array $validatedData): BankAccountData
    {
        $accountData = new BankAccountData();
        $accountData->bank_id = $validatedData['bankId'];
        $accountData->bank_name = $validatedData['bankName'];
        $accountData->bank_account_name = $validatedData['bankAccountName'];
        $accountData->bank_account_no = $validatedData['bankAccountNo'];
        $accountData->branch_code = $validatedData['branchCode'];
        $accountData->search_str = $validatedData['search'];

        return $accountData;
    }
}
