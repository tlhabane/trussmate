<?php

namespace App\Domain\UserSession\Service\TerminateSession;

use App\Domain\UserSession\Repository\UpdateSessionStatusRepository;
use App\Domain\UserSession\Service\SanitizeSessionDataService;
use App\Domain\UserSession\Service\MapSessionDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class TerminateSessionService
{
    private UpdateSessionStatusRepository $updateSessionStatusRepository;
    private ValidateTerminateSessionDataService $validateTerminateSessionDataService;

    public function __construct(PDO $connection)
    {
        $this->updateSessionStatusRepository = new UpdateSessionStatusRepository($connection);
        $this->validateTerminateSessionDataService = new ValidateTerminateSessionDataService();
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function terminateSession(array $data): array
    {
        $sanitizedData = SanitizeSessionDataService::sanitizeData($data);
        $validatedData = $this->validateTerminateSessionDataService->validateData($sanitizedData);

        $sessionData = MapSessionDataService::mapData($validatedData);
        $sessionData->session_id = $validatedData['sessionId'];

        if ($this->updateSessionStatusRepository->updateStatus($sessionData)) {
            return [
                'success' => 'Session ended',
                'id' => $sessionData->session_id
            ];
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
