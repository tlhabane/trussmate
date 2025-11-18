<?php

namespace App\Domain\User\Service\UploadUsers;

use App\Exception\RuntimeException;

final class LoadUsersFromFileService
{
    /**
     * @throws RuntimeException
     */
    public static function loadCustomers(string $filepath): array
    {
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            throw new RuntimeException("Failed to open file.");
        }

        return self::getUsers($handle);
    }

    private static function normalizeString(string $text): string
    {
        return strtolower(str_replace(' ', '', $text));
    }

    private static function getUsers(mixed $stream): array
    {
        // Read the headers
        $headers = fgetcsv($stream);

        $users = [];

        while (($row = fgetcsv($stream)) !== false) {
            $data = array_combine($headers, $row);

            $users[] = [
                'userRole' => $data['user_role'] ?? 'user',
                'firstName' => $data['first_name'] ?? '',
                'lastName' => $data['last_name'] ?? '',
                'jobTitle' => $data['job_title'] ?? '',
                'tel' => self::normalizeString($data['tel'] ?? ''),
                'email' => self::normalizeString($data['email'] ?? ''),
                'password' => $data['password'] ?? '',
            ];
        }

        return $users;
    }
}
