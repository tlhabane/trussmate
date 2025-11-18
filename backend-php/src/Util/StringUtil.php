<?php

namespace App\Util;

class StringUtil
{
    /**
     * Removes extra spaces between strings & capitalizes 1st characters of word
     * @param string $text
     * @return string
     * @example normalizeString('John  doe') => 'John Doe'
     *
     */
    public static function normalizeString(string $text): string
    {
        $updatedString = implode(' ', array_filter(explode(' ', $text)));
        return ucwords(strtolower(trim($updatedString)));
    }

    /**
     * Removes extra spaces between comma seperated strings & capitalizes 1st characters of word
     * @param string $value
     * @return string[]
     * @example normalizeString('John  doe, jane doe ') => ['John Doe', 'Jane Doe']
     *
     */
    public static function normalizeStringArray(string $value): array
    {
        if (empty(trim($value))) {
            return [];
        }
        $values = explode(',', $value);
        $valuesArray = array_map(
            function ($item) {
                return self::normalizeString($item);
            },
            $values
        );

        return array_filter($valuesArray, fn($item) => !is_null(trim($item)) && trim($item) !== '');
    }

    /**
     * Format phone number
     *
     * @param string $raw
     * @return string
     */
    public static function formatPhoneNumber(string $raw): string
    {
        // Remove non-digit characters
        $digits = self::removeNonDigits($raw);

        // Ensure it's 10 digits
        if (strlen($digits) === 10) {
            return sprintf(
                '(%s) %s-%s',
                substr($digits, 0, 3),
                substr($digits, 3, 3),
                substr($digits, 6)
            );
        }

        // If not 10 digits, return original for manual review
        return $raw;
    }

    /**
     * Add padding to string|int|float
     *
     * @param mixed $candidate
     * @param mixed $prefix
     * @param int $length
     * @return string
     */
    public static function addPadding(mixed $candidate, mixed $prefix = '0', int $length = 5): string
    {
        do {
            $candidate = $prefix . $candidate;
        } while (strlen("{$candidate}") < $length);

        return "{$candidate}";
    }

    /**
     * Remove padding from string|int|float
     *
     * @param mixed $candidate
     * @param mixed $prefix
     * @return string
     */
    public static function removePadding(mixed $candidate, mixed $prefix = '0'): string
    {
        return ltrim("{$candidate}", "{$prefix}");
    }

    /**
     * Remove non-digit characters
     *
     * @param string $raw
     * @return string
     */
    public static function removeNonDigits(string $raw): string
    {
        return (string)preg_replace('/\D+/', '', $raw);
    }

    /* Random generators
    ---------------------------------------------------*/

    /**
     * Token string generator helper function
     *
     * @param int $min
     * @param int $max
     * @return int $token
     */
    private static function cryptoRandSecure(int $min, int $max): int
    {
        $range = $max - $min;

        if ($range < 0) {
            return $min;
        }

        // not so random...
        $log = log($range, 2);
        $bytes = (int)($log / 8) + 1; // length in bytes
        $bits = (int)$log + 1; // length in bits
        $filter = (int)(1 << $bits) - 1; // set all lower bits to 1

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);

        return $min + $rnd;
    }

    /**
     * Random String Generator
     *
     * @param int $length
     * @return string
     */
    public static function randomCharString(int $length = 10): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Token string generator
     *
     * @param int $length
     * @return string $token
     */
    public static function generateToken(int $length = 32): string
    {
        $token = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        do {
            $token .= (strlen($token) === 0) ?
                self::randomCharString(3) : $codeAlphabet[self::cryptoRandSecure(0, strlen($codeAlphabet))];
        } while (strlen($token) < $length);

        return $token;
    }

    /**
     * Password hash
     *
     * @param string $password
     * @param int $cost
     * @return string(delimited)
     */
    public static function passwordHash(string $password, int $cost = 12): string
    {
        $options = [
            'cost' => $cost
        ];
        return password_hash($password, PASSWORD_DEFAULT, $options);
    }

    /**
     * Password validation
     *
     * @param string $password
     * @param string $hash
     * @return boolean
     */
    public static function validatePassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function dataUriToImage(string $dataUri, string $uploadDir): string|bool
    {
        if (empty($dataUri)) {
            return false;
        }

        // Split data URI
        list($type, $data) = explode(';', $dataUri, 2);
        list(, $mimeType) = explode(':', $type, 2);

        // Decode base64 data
        $decodedData = base64_decode($data);
        if ($decodedData === false) {
            return false; // Decoding failed
        }

        // Determine file extension
        $extension = '';
        switch ($mimeType) {
            case 'image/jpeg':
                $extension = '.jpg';
                break;
            case 'image/png':
                $extension = '.png';
                break;
            // Add more cases as needed
            default:
                return false; // Unsupported MIME type
        }

        do {
            $filename = self::generateToken() . $extension;
        } while (empty($filename) || file_exists($uploadDir . $filename));

        // Save the image
        if (file_put_contents($uploadDir . $filename, $decodedData) === false) {
            return false; // Saving failed
        }

        return file_exists($uploadDir . $filename) ? $filename : false;
    }
}
