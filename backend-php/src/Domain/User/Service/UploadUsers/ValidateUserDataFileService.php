<?php

namespace App\Domain\User\Service\UploadUsers;

use App\Contract\DataValidationContract;
use App\Exception\ValidationException;

final class ValidateUserDataFileService implements DataValidationContract
{
    public function validateData(array $data): array
    {
        // Common CSV MIME types
        $allowedMimeTypes = [
            'text/csv',
            'text/plain',
            'application/vnd.ms-excel', // legacy Excel export format
            'application/csv',
        ];

        $validatedFiles = [];

        foreach ($data as $file) {
            if (file_exists($file['source'])) {
                // Get file extension & mime type
                $extension = strtolower(pathinfo($file['source'], PATHINFO_EXTENSION));
                $mimeType = mime_content_type($file['source']);
                if (in_array($mimeType, $allowedMimeTypes, true)
                    && $extension === 'csv') {
                    $validatedFiles[] = $file['source'];
                } else {
                    throw new ValidationException('Only csv (*.csv) files are supported');
                }
            } else {
                throw new ValidationException('Invalid or missing customer data file');
            }
        }

        return $validatedFiles;
    }
}
