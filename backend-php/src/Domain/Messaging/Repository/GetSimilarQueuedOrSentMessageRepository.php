<?php

namespace App\Domain\Messaging\Repository;

use App\Domain\Messaging\Data\MessagingData;
use PDOStatement;
use PDO;

final class GetSimilarQueuedOrSentMessageRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getMessage(MessagingData $data): PDOStatement
    {
        $query = "SELECT 
                      mr.message_id, mr.recipient_id, mr.recipient_name, mr.recipient_address, mr.message_status,
                      m.user_id, m.record_id, m.sp_message_id, m.subject, m.message, m.channel, ua.account_no, 
                      a.account_status, IF(ai.trading_name IS NULL OR TRIM(ai.trading_name) = '', 
                          ai.registered_name,
                          ai.trading_name
                      ) AS `sender_name`, ai.email AS `sender_email`, ai.tel AS `sender_tel` 
                  FROM 
                      message_recipient mr 
                  LEFT JOIN 
                      message m on mr.message_id = m.message_id 
                  LEFT JOIN 
                      user_account ua ON ua.user_id = m.user_id 
                  LEFT JOIN 
                      account a on ua.account_no = a.account_no 
                  LEFT JOIN 
                      account_info ai on a.account_no = ua.account_no  
                  WHERE 
                      m.channel = :channel AND 
                      TRIM(LOWER(m.subject)) = :subject AND
                      TRIM(LOWER(mr.recipient_address)) = :recipient_address AND (
                          mr.message_status = 'queued' OR (
                              mr.message_status = 'sent' AND 
                              TIMESTAMPDIFF(HOUR, mr.modified, NOW()) <= 1
                          )
                      )";

        if ($data->channel->name !== 'email') {
            $query .= " AND TRIM(LOWER(m.message)) = :message";
        }

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'channel' => $data->channel->name,
            'subject' => strtolower(trim($data->subject)),
            'recipient_address' => strtolower(trim($data->recipient_address))
        ];
        if ($data->channel->name !== 'email') {
            $query_data['message'] = strtolower(trim($data->message));
        }

        $query_stmt->execute($query_data);
        return $query_stmt;
    }
}
