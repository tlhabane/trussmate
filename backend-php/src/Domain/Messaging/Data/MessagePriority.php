<?php

namespace App\Domain\Messaging\Data;

use App\Exception\RuntimeException;

enum MessagePriority: int
{
    case low = 1;
    case medium = 2;
    case high = 3;


    /**
     * @throws RuntimeException
     */
    public static function getValueFromType(string $type): MessagePriority
    {
        foreach(self::cases() as $enum) {
            if ($enum->name === trim($type)) {
                return $enum;
            }
        }

        throw new RuntimeException('Invalid message priority type.', 422);
    }

    /**
     * @throws RuntimeException
     */
    public static function getTypeFromValue(int $value): MessagePriority
    {
        $enum = self::tryFrom($value);
        if (!$enum) {
            throw new RuntimeException('Invalid message priority value.', 422);
        }

        return $enum;
    }
}
