<?php

namespace App\Domain\User\Service\UpdateUserHash;

use App\Domain\User\Repository\UpdateUserHashRepository;
use App\Domain\User\Service\SanitizeUserDataService;
use App\Domain\User\Service\MapUserDataService;
use App\Exception\RuntimeException;
use PDO;

final class UpdateUserHashService
{
    private UpdateUserHashRepository $updateUserHashRepository;
    private ValidateUpdateUserHashDataService $validateUpdateUserHashDataService;

    public function __construct(PDO $connection)
    {
        $this->updateUserHashRepository = new UpdateUserHashRepository($connection);
        $this->validateUpdateUserHashDataService = new ValidateUpdateUserHashDataService();
    }

    /**
     * @throws RuntimeException
     */
    public function updateHash(array $data): array
    {
        $sanitizedData = SanitizeUserDataService::sanitizeData($data);
        $validatedData = $this->validateUpdateUserHashDataService->validateData($sanitizedData);
        $userData = MapUserDataService::mapData($validatedData);
        if ($this->updateUserHashRepository->updateHash($userData)) {
            return [
                'success' => 'Password updated successfully.',
                'id' => $userData->username
            ];
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
