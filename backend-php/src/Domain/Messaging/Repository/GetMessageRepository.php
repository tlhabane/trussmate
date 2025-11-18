<?php

namespace App\Domain\Messaging\Repository;

use App\Domain\Messaging\Data\MessagingData;
use PDOStatement;
use PDO;

final class GetMessageRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getMessage(MessagingData $data, $record_start = 0, $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      mr.message_id, mr.recipient_id, mr.recipient_name, mr.recipient_address, mr.message_status,
                      m.account_no, m.user_id, CONCAT_WS(' ', ui.first_name, ui.last_name) AS `agent_name`,
                      m.record_id, m.sp_message_id, m.message_type, m.message_priority, m.subject, m.message, m.channel,
                      a.account_status, IF(ad.trading_name IS NULL OR TRIM(ad.trading_name) = '', 
                          ad.registered_name,
                          ad.trading_name
                      ) AS `sender_name`, ad.email AS `sender_email`, ad.tel AS `sender_tel` 
                  FROM 
                      message_recipient mr 
                  LEFT JOIN 
                      message m on mr.message_id = m.message_id 
                  LEFT JOIN 
                      user_account_info ui ON ui.user_id = m.user_id
                  LEFT JOIN 
                      account a on a.account_no = m.account_no 
                  LEFT JOIN 
                      account_info ad on ad.account_no = m.account_no  
                  WHERE 
                      a.account_status = 'active' AND 
                      m.channel = :channel";

        $query .= empty($data->account_no) ? "" : " AND m.account_no = :account_no";
        $query .= empty($data->recipient_id) ? "" : " AND mr.recipient_id = :recipient_id";
        $query .= empty($data->message_status) ? "" : " AND mr.message_status = :message_status";
        $query .= empty($data->message_id) ? "" : " AND mr.message_id = :message_id";
        $query .= empty($data->message_type) ? "" : " AND m.message_type = :message_type";
        $query .= empty($data->message_priority) ? "" : " AND m.message_priority = :message_priority";
        $query .= empty($data->record_id) ? "" : " AND m.record_id = :record_id";
        $query .= empty($data->user_id) ? "" : " AND m.user_id = :user_id";

        if (!empty($data->start_date) || !empty($data->end_date)) {
            if (!empty($data->start_date) && !empty($data->end_date)) {
                $query .= " AND (
                    created BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND 
                    DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                )";
            } else {
                if (!empty($data->start_date) && empty($data->end_date)) {
                    $query .= " AND (
                        created BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND 
                        DATE_FORMAT(:start_date, '%Y-%m-%d 23:59:59')
                    )";
                }
                if (empty($data->start_date) && !empty($data->end_date)) {
                    $query .= " AND (
                        created BETWEEN DATE_FORMAT(:end_date, '%Y-%m-%d 00:00:00') AND 
                        DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                    )";
                }
            }
        }

        if (!empty($data->search_str)) {
            $query .= " AND (
                LOWER(mr.sender_name) LIKE :search_str OR LOWER(mr.sender_address) LIKE :search_str OR
                LOWER(mr.recipient_name) LIKE :search_str OR LOWER(mr.recipient_address) LIKE :search_str OR
                LOWER(m.record_id) LIKE :search_str OR LOWER(m.sp_message_id) LIKE :search_str OR 
                LOWER(m.subject) LIKE :search_str OR LOWER(m.message) LIKE :search_str 
            )";
        }

        $query .= " ORDER BY mr.created DESC";

        if ($record_start > 0 || $record_limit > 0) {
            if ($record_start > 0 && $record_limit > 0) {
                $query .= " LIMIT :record_start, :record_limit";
            } else {
                $query .= " LIMIT :record_limit";
            }
        }

        $query_stmt = $this->connection->prepare($query);
        $channel = $data->channel->name;
        $query_stmt->bindParam(':channel', $channel);
        if (!empty($data->account_no)) {
            $query_stmt->bindParam(':account_no', $data->account_no);
        }
        if (!empty($data->message_status)) {
            $message_status = $data->message_status->name;
            $query_stmt->bindParam(':message_status', $message_status);
        }
        if (!empty($data->recipient_id)) {
            $query_stmt->bindParam(':recipient_id', $data->recipient_id);
        }
        if (!empty($data->message_id)) {
            $query_stmt->bindParam(':message_id', $data->message_id);
        }
        if (!empty($data->message_type)) {
            $message_type = $data->message_type->name;
            $query_stmt->bindParam(':message_type', $message_type);
        }
        if (!empty($data->message_priority)) {
            $message_priority = $data->message_priority->name;
            $query_stmt->bindParam(':message_priority', $message_priority);
        }
        if (!empty($data->record_id)) {
            $query_stmt->bindParam(':record_id', $data->record_id);
        }
        if (!empty($data->user_id)) {
            $query_stmt->bindParam(':user_id', $data->user_id);
        }
        if (!empty($data->start_date)) {
            $query_stmt->bindParam(':start_date', $data->start_date);
        }
        if (!empty($data->end_date)) {
            $query_stmt->bindParam(':end_date', $data->end_date);
        }
        if (!empty($data->search_str)) {
            $data->search_str = strtolower(trim($data->search_str));
            $data->search_str = str_replace('%20', ' ', $data->search_str);
            $data->search_str = "%{$data->search_str}%";
            $query_stmt->bindParam(':search_str', $data->search_str);
        }
        if ($record_start > 0) {
            $query_stmt->bindParam(':record_start', $record_start, PDO::PARAM_INT);
        }
        if ($record_limit > 0) {
            $query_stmt->bindParam(':record_limit', $record_limit, PDO::PARAM_INT);
        }

        $query_stmt->execute();
        return $query_stmt;
    }
}
