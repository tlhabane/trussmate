<?php

namespace App\Domain\Messaging\Service\QueueEmail;

use App\Domain\Messaging\Data\MessageChannel;
use App\Domain\Messaging\Data\MessageType;
use App\Domain\Messaging\Data\MessagePriority;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use App\Util\Utilities;

final class ValidateQueueEmailDataService
{
    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public static function validateData(array $sanitizedData): array
    {
        if (empty($sanitizedData['subject'])) {
            throw new ValidationException('Invalid email subject.');
        }
        if (empty($sanitizedData['message'])) {
            throw new ValidationException('Invalid email message.');
        }

        $attachments = [];
        foreach ($sanitizedData['attachments'] as $attachment) {
            if(file_exists($attachment['fileSource'])) {
                $attachments[] = $attachment;
            }
        }
        $sanitizedData['attachments'] = $attachments;

        $recipients = [];
        foreach ($sanitizedData['recipients'] as $recipient) {
            $email = Utilities::sanitizeEmail($recipient['recipientAddress']);
            if(!empty($email)) {
                $recipients[] = [
                    'recipientName' => trim($recipient['recipientName']),
                    'recipientAddress' => $email
                ];
            }
        }

        if (!(count($recipients) > 0)) {
            throw new ValidationException(
                'At least 1(one) valid email recipient is required to proceed.'
            );
        }
        $sanitizedData['recipients'] = $recipients;
        $sanitizedData['messageType'] = MessageType::getTypeFromValue($sanitizedData['messageType']);
        $sanitizedData['messagePriority'] = MessagePriority::getTypeFromValue($sanitizedData['messagePriority']);
        $sanitizedData['channel'] = MessageChannel::email;

        return $sanitizedData;
    }
}
