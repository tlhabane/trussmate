<?php

namespace App\Util;

class DataUTF8Util extends DataSanitizer
{
    /**
     *  Encode string
     *
     * @param string|null $inputString
     * @return string - ut8 encoded
     */
    public static function encodeUTF8(?string $inputString) : string
    {
        if (is_null($inputString)) {
            return '';
        }
        return (htmlentities($inputString, ENT_QUOTES, 'UTF-8'));
    }

    /**
     *  Decode string
     *
     * @param string|null $inputString - utf-8 encoded
     * @return string - utf-8 decoded
     */
    public static function decodeUTF8(?string $inputString) : string
    {
        if (is_null($inputString)) {
            return  '';
        }
        return (html_entity_decode($inputString, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Sanitize string and UTF-8 encoding
     *
     * @param string|null $inputString
     * @return string
     */
    public static function sanitizeAndEncodeString(?string $inputString): string
    {
        return self::encodeUTF8(self::sanitizeString($inputString));
    }
}
