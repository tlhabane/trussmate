<?php

namespace App\Util;

use App\Exception\ValidationException;
use Exception;
use DateTime;

class DataSanitizer extends StringUtil
{
    /**
     * String sanitizer
     *
     * @param string|null $inputString
     * @return string
     */
    public static function sanitizeString(?string $inputString): string
    {
        if (is_null($inputString)) {
            return '';
        }

        $search = array(
            '@<script[^>]*?>.*?</script>@si',   /* strip out javascript */
            '@<[\/\!]*?[^<>]*?>@si',            /* strip out HTML tags */
            '@<style[^>]*?>.*?</style>@siU',    /* strip style tags properly */
            '@<![\s\S]*?--[ \t\n\r]*>@'         /* strip multi-line comments */
        );

        return preg_replace($search, '', $inputString);
    }

    /**
     * Email address sanitizer
     *
     * @param string|null $email
     * @return string
     */
    public static function sanitizeEmail(?string $email): string
    {
        if (!is_null($email)) {
            $email_address = trim(strtolower($email));
            if (filter_var($email_address, FILTER_SANITIZE_EMAIL) !== false) {
                return $email_address ;
            }
        }

        return '';
    }

    /**
     * Web address sanitizer
     *
     * @param string|null $url
     * @return string
     */
    public static function sanitizeUrl(?string $url): string
    {
        if (!is_null($url)) {
            $website_url = trim($url);
            if (filter_var($website_url, FILTER_VALIDATE_URL) !== false) {
                return $website_url;
            }
        }
        return '';
    }

    /**
     * @param string|null $dateOrTime
     * @param string $format
     * @return string
     * @throws ValidationException
     */
    public static function validateDateAndTime(?string $dateOrTime, string $format = 'Y-m-d'): string
    {
        if (empty($dateOrTime)) {
            return '';
        }
        $dateOrTime = urldecode($dateOrTime);
        try {
            $dateTimeObject = new DateTime($dateOrTime);
            return $dateTimeObject->format($format);
        } catch (Exception $exception) {
            throw new ValidationException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param string|null $dateOrTime
     * @param string $format
     * @return string
     * @throws ValidationException
     */
    public static function sanitizeDateAndTime(?string $dateOrTime, string $format = 'Y-m-d'): string
    {
        $sanitizedDateOrTime = self::sanitizeString($dateOrTime);
        return self::validateDateAndTime($sanitizedDateOrTime, $format);
    }
}
