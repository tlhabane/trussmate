<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use PDOStatement;
use PDO;

final class GetUserByIdRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getUser(string $username): PDOStatement
    {
        $query = "SELECT
                      ua.account_no, ua.user_id, ua.user_status, ua.user_role, ua.user_hash, ui.region_id, 
                      ui.first_name, ui.last_name, ui.job_title, ui.tel, ui.alt_tel, ui.email, r.region_name
                  FROM 
                      user_account ua 
                  LEFT JOIN 
                      user_account_info ui ON ui.user_id = ua.user_id 
                  LEFT JOIN 
                      region r on ui.region_id = r.region_id
                  WHERE 
                      ua.user_id = :username OR 
                      ui.tel = :username OR 
                      ui.alt_tel = :username OR 
                      ui.email = :username";

        $query_stmt = $this->connection->prepare($query);
        $formattedUsername = trim($username);
        $query_stmt->bindParam(':username', $formattedUsername);

        $query_stmt->execute();
        return $query_stmt;
    }
}
