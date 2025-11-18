<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use App\Domain\User\Data\MapInsertUpdateUserAccountInfoData;
use PDO;

final class UpdateUserAccountInfoRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateInfo(UserData $data): bool
    {
        $query = "UPDATE user_account_info SET 
                      region_id = :region_id,
                      first_name = :first_name,
                      last_name = :last_name,
                      job_title = :job_title,
                      tel = :tel,
                      alt_tel = :alt_tel,
                      email = :email 
                  WHERE 
                      user_id = :username";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapInsertUpdateUserAccountInfoData::mapData($data);
        return $query_stmt->execute($query_data);
    }
}
