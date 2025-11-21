<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetUserRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getUser(UserData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT
                      ua.user_id, ua.user_status, ua.user_role, ua.user_hash, ui.region_id, ui.first_name, ui.last_name,
                      ui.job_title, ui.tel, ui.alt_tel, ui.email, r.region_name
                  FROM 
                      user_account ua 
                  LEFT JOIN 
                      user_account_info ui ON ui.user_id = ua.user_id 
                  LEFT JOIN 
                      region r on ui.region_id = r.region_id
                  WHERE 
                      ua.account_no = :account_no";

        $query .= empty($data->username) ? "" : " AND ua.user_id = :username";
        $query .= empty($data->user_status->value) ? "" : " AND ua.user_status = :user_status";
        $query .= empty($data->user_role->value) ? "" : " AND ua.user_role = :user_role";
        $query .= empty($data->region_id) ? "" : " AND ui.region_id = :region_id";

        if (!empty($data->search)) {
            $query .= " AND (
                LOWER(ui.first_name) LIKE :search OR LOWER(ui.last_name) LIKE :search OR 
                LOWER(ui.job_title) LIKE :search OR ui.tel LIKE :search OR ui.alt_tel LIKE :search OR 
                LOWER(ui.email) LIKE :search OR (r.region_name) LIKE :search
            )";
        }

        $query .= " ORDER BY ui.last_name, ui.first_name";
        $query .= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);

        $query_stmt->bindParam(':account_no', $data->account_no);
        if (!empty($data->username)) {
            $query_stmt->bindParam(':username', $data->username);
        }
        if (!empty($data->user_status->value)) {
            $statusValue = $data->user_status->value;
            $query_stmt->bindParam(':user_status', $statusValue);
        }
        if (!empty($data->user_role->value)) {
            $userRoleValue = $data->user_role->name;
            $query_stmt->bindParam(':user_role', $userRoleValue);
        }
        if (!empty($data->region_id)) {
            $query_stmt->bindParam(':region_id', $data->region_id);
        }

        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
