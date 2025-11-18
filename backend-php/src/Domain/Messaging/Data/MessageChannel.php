<?php

namespace App\Domain\Messaging\Data;

use App\Exception\RuntimeException;

enum MessageChannel: int
{
    case push_notification = 1;
    case instant_messaging = 2;
    case sms = 3;
    case email = 4;

    /**
     * @throws RuntimeException
     */
    public static function getValueFromType(string $type): MessageChannel
    {
        foreach(self::cases() as $enum) {
            if ($enum->name === trim($type)) {
                return $enum;
            }
        }

        throw new RuntimeException('Invalid message channel type.', 422);
    }

    /**
     * @throws RuntimeException
     */
    public static function getTypeFromValue(int $value): MessageChannel
    {
        $enum = self::tryFrom($value);
        if (!$enum) {
            throw new RuntimeException('Invalid message channel value.', 422);
        }

        return $enum;
    }
}
