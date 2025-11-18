<?php

namespace App\Domain\Messaging\Data;

final class MessagingData
{
    public string $user_id;
    public string $account_no;
    public string $record_id;
    public string $message_id;
    public string $sp_message_id;
    public string $subject;
    public string $message;
    public MessageChannel $channel;
    public MessageType $message_type;
    public MessagePriority $message_priority;

    public string $attachment_id;
    public string $filename;
    public string $file_source;

    public string $recipient_id;
    public string $sender_name;
    public string $sender_address;
    public string $recipient_name;
    public string $recipient_address;
    public MessageStatus $message_status;

    public string $search_str;
    public string $start_date;
    public string $end_date;
}
