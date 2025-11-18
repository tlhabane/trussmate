<?php

namespace App\Domain\Account\Repository;

use App\Domain\Account\Data\AccountData;
use App\Domain\Account\Data\MapInsertUpdateAccountData;
use PDO;

final class AddAccountInfoRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addAccountInfo(AccountData $data): bool
    {
        $query = "INSERT INTO account_info SET 
                  account_no = :account_no,
                  logo = :logo,
                  registration_no = :registration_no,
                  registered_name = :registered_name,
                  vat_no = :vat_no,
                  trading_name = :trading_name,
                  tel = :tel,
                  alt_tel = :alt_tel,
                  email = :email,
                  web = :web,
                  address = :address";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapInsertUpdateAccountData::mapData($data);
        return $query_stmt->execute($query_data);
    }
}
