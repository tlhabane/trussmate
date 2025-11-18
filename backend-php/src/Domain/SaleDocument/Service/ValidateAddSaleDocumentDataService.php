<?php

namespace App\Domain\SaleDocument\Service;

use App\Exception\ValidationException;

final class ValidateAddSaleDocumentDataService
{
    /**
     * @throws ValidationException
     */
    public static function validateData(array $data): array
    {
        if (empty($data['saleId'])) {
            throw new ValidationException('Invalid or missing sale details');
        }

        if (empty($data['files']) || !is_array($data['files'])) {
            throw new ValidationException('At least 1(one) valid file is required to proceed');
        }

        foreach ($data['files'] as $file) {
            if (!file_exists($file['source'])) {
                throw new ValidationException(sprintf(
                    'Oops! An error occurred while uploading %s, please try again.',
                    $file['filename'] ?? 'Unknown file'
                ));
            }
        }
        return $data;
    }
}
