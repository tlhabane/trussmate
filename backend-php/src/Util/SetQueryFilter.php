<?php

namespace App\Util;

use PDOStatement;
use PDO;

final class SetQueryFilter
{
    public static function addQueryFilters(
        PDOStatement $query_stmt,
        mixed $data,
        int $record_start = 0,
        int $record_limit = 0
    ): PDOStatement {
        if (!empty($data->start_date)) {
            $query_stmt->bindParam(':start_date', $data->start_date);
        }
        if (!empty($data->end_date)) {
            $query_stmt->bindParam(':end_date', $data->end_date);
        }
        if (!empty($data->search)) {
            $data->search = strtolower(trim($data->search));
            $data->search = str_replace('%20', ' ', $data->search);
            $data->search = "%{$data->search}%";
            $query_stmt->bindParam(':search', $data->search);
        }
        if ($record_start > 0) {
            $query_stmt->bindParam(':record_start', $record_start, PDO::PARAM_INT);
        }
        if ($record_limit > 0) {
            $query_stmt->bindParam(':record_limit', $record_limit, PDO::PARAM_INT);
        }

        return $query_stmt;
    }

    public static function setQueryLimit(int $record_start = 0, int $record_limit = 0): string
    {
        $limit = '';
        if ($record_start > 0 || $record_limit > 0) {
            if ($record_start > 0 && $record_limit > 0) {
                $limit = " LIMIT :record_start, :record_limit";
            } else {
                $limit = " LIMIT :record_limit";
            }
        }
        return $limit;
    }
}
