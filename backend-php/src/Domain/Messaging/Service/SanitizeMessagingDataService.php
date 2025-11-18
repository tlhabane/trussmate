<?php

namespace App\Domain\Messaging\Service;

use App\Exception\ValidationException;
use App\Util\Utilities;

final class SanitizeMessagingDataService
{
    /**
     * @throws ValidationException
     */
    public static function sanitizedData(array $data): array
    {
        $attachments = [];
        if (isset($data['attachments']) && is_array($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                if (!empty(trim($attachment['fileName'])) && !empty(trim($attachment['fileSource']))) {
                    $attachments[] = [
                        'fileName' => Utilities::sanitizeString($attachment['fileName']),
                        'fileSource' => Utilities::sanitizeString($attachment['fileSource'])
                    ];
                }
            }
        }

        $recipients = [];
        if (isset($data['recipients']) && is_array($data['recipients'])) {
            foreach ($data['recipients'] as $recipient) {
                if (!empty(trim($recipient['recipientName'])) && !empty(trim($recipient['recipientAddress']))) {
                    $recipients[] = [
                        'recipientName' => Utilities::sanitizeString($recipient['recipientName']),
                        'recipientAddress' => Utilities::sanitizeString($recipient['recipientAddress'])
                    ];
                }
            }
        } else {
            $recipients[] = [
                'recipientAddress' => Utilities::sanitizeString($data['recipientAddress'] ?? ''),
                'recipientName' => Utilities::sanitizeAndEncodeString($data['recipientName'] ?? ''),
            ];
        }

        return [
            'recordId' => Utilities::sanitizeString($data['record'] ?? ''),
            'attachments' => $attachments,
            'messageId' => Utilities::sanitizeString($data['messageId'] ?? ''),
            'messageStatus' => intval(Utilities::sanitizeString($data['messageStatus'] ?? 0)),
            'messagePriority'=> intval(Utilities::sanitizeString($data['messagePriority'] ?? 0)),
            'messageType' => intval(Utilities::sanitizeString($data['messageType'] ?? 0)),
            'messageChannel' => intval(Utilities::sanitizeString($data['messageChannel'] ?? 0)),
            'spMessageId' => Utilities::sanitizeString($data['spMessageId'] ?? ''),
            'subject' => Utilities::sanitizeAndEncodeString($data['subject'] ?? ''),
            'message' => Utilities::encodeUTF8($data['message'] ?? ''),
            'channel' => intval(Utilities::sanitizeString($data['channel'] ?? 0)),
            'recipients' => $recipients,
            'startDate' => Utilities::sanitizeDateAndTime($data['startDate'] ?? ''),
            'endDate' => Utilities::sanitizeDateAndTime($data['endDate'] ?? ''),
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0))
        ];
    }
}
