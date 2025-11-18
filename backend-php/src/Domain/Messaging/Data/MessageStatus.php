<?php

namespace App\Domain\Messaging\Data;

use App\Exception\RuntimeException;

enum MessageStatus: int
{
    case queued = 1;
    case sent = 2;
    case failed = 3;

    /**
     * @throws RuntimeException
     */
    public static function getValueFromType(string $type): MessageStatus
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
    public static function getTypeFromValue(int $value): MessageStatus
    {
        $enum = self::tryFrom($value);
        if (!$enum) {
            throw new RuntimeException('Invalid message channel value.', 422);
        }

        return $enum;
    }
}
