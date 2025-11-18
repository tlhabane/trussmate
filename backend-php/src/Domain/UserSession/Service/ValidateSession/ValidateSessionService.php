<?php

namespace App\Domain\UserSession\Service\ValidateSession;

use App\Domain\UserSession\Repository\UpdateSessionExpiryRepository;
use App\Domain\UserSession\Service\SanitizeSessionDataService;
use App\Domain\UserSession\Service\MapSessionDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use DateTime;
use PDO;

final class ValidateSessionService
{
    private UpdateSessionExpiryRepository $updateSessionExpiryRepository;
    private ValidateSessionDataService $validateSessionDataService;

    public function __construct(PDO $connection)
    {
        $this->updateSessionExpiryRepository = new UpdateSessionExpiryRepository($connection);
        $this->validateSessionDataService = new ValidateSessionDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function validateSession(array $data): array
    {
        $sanitizedData = SanitizeSessionDataService::sanitizeData($data);
        $validatedData = $this->validateSessionDataService->validateData($sanitizedData);
        $date_time = new DateTime('now');
        $validatedData['sessionExpiry'] = $date_time
            ->modify('+24 hours')
            ->format('Y-m-d H:i:s');

        $sessionData = MapSessionDataService::mapData($validatedData);
        $sessionData->session_id = $validatedData['sessionId'];

        if ($this->updateSessionExpiryRepository->updateExpiry($sessionData)) {
            return [
                'token' => $validatedData['sessionKey'] . $sessionData->session_id,
                'sessionId' => $sessionData->session_id,
                'accountNo' => $validatedData['accountNo'],
                'sessionUsername' => $validatedData['userId'],
                'sessionUserRegionId' => $validatedData['regionId'],
                'sessionUserRegionName' => $validatedData['regionName'],
                'sessionUserFirstname' => $validatedData['firstName'],
                'sessionUserRole' => $validatedData['userRole'],
            ];
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
