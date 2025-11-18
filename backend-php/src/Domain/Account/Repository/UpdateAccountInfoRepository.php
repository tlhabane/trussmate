<?php

namespace App\Domain\Account\Repository;

use App\Domain\Account\Data\AccountData;
use App\Domain\Account\Data\MapInsertUpdateAccountData;
use PDO;

final class UpdateAccountInfoRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateAccountInfo(AccountData $data): bool
    {
        $query = "UPDATE account_info SET 
                      logo = :logo,
                      registration_no = :registration_no,
                      registered_name = :registered_name,
                      vat_no = :vat_no,
                      trading_name = :trading_name,
                      tel = :tel,
                      alt_tel = :alt_tel,
                      email = :email,
                      web = :web,
                      address = :address
                  WHERE 
                      account_no = :account_no";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapInsertUpdateAccountData::mapData($data);
        return $query_stmt->execute($query_data);
    }
}
