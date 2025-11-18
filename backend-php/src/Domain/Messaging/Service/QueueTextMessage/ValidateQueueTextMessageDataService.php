<?php

namespace App\Domain\Messaging\Service\QueueTextMessage;

use App\Domain\Messaging\Data\MessageChannel;
use App\Domain\Messaging\Data\MessageType;
use App\Domain\Messaging\Data\MessagePriority;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;

final class ValidateQueueTextMessageDataService
{
    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public static function validateData(array $sanitizedData): array
    {
        if (!(count($sanitizedData['recipients']) > 0)) {
            throw new ValidationException(
                'At least 1(one) valid text message recipient is required to proceed.'
            );
        }
        if (empty($sanitizedData['subject'])) {
            throw new ValidationException('Invalid text message subject.');
        }
        if (empty($sanitizedData['message'])) {
            throw new ValidationException('Invalid text message.');
        }

        $sanitizedData['channel'] = MessageChannel::sms;
        $sanitizedData['messageType'] = MessageType::getTypeFromValue($sanitizedData['messageType']);
        $sanitizedData['messagePriority'] = MessagePriority::getTypeFromValue($sanitizedData['messagePriority']);

        return $sanitizedData;
    }
}
