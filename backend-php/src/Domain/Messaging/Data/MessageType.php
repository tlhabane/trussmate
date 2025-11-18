<?php

namespace App\Domain\Messaging\Data;

use App\Exception\RuntimeException;

enum MessageType: int
{
    case quotation = 1;
    case order_confirmation = 2;
    case proforma_invoice = 3;
    case invoice = 4;
    case account_statement = 5;
    case password_reset = 6;
    case one_time_pin = 7;
    case other = 8;

    /**
     * @throws RuntimeException
     */
    public static function getValueFromType(string $type): MessageType
    {
        foreach (self::cases() as $enum) {
            if ($enum->name === trim($type)) {
                return $enum;
            }
        }

        throw new RuntimeException('Invalid message type.', 422);
    }

    /**
     * @throws RuntimeException
     */
    public static function getTypeFromValue(int $value): MessageType
    {
        $enum = self::tryFrom($value);
        if (!$enum) {
            throw new RuntimeException('Invalid message type.', 422);
        }

        return $enum;
    }
}
