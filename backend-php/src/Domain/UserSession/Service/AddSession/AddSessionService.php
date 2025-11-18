<?php

namespace App\Domain\UserSession\Service\AddSession;

use App\Domain\UserSession\Repository\AddNewSessionRepository;
use App\Domain\UserSession\Service\EndAllUserSessionsService;
use App\Domain\UserSession\Service\SanitizeSessionDataService;
use App\Domain\UserSession\Service\MapSessionDataService;
use App\Domain\UserSession\Service\GetSessionIdService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use App\Util\Utilities;
use DateTime;
use PDO;

final class AddSessionService
{
    private AddNewSessionRepository $addNewSessionRepository;
    private EndAllUserSessionsService $endAllUserSessionsService;
    private ValidateAddSessionDataService $validateAddSessionDataService;
    private GetSessionIdService $getSessionIdService;

    public function __construct(PDO $connection)
    {
        $this->addNewSessionRepository = new AddNewSessionRepository($connection);
        $this->endAllUserSessionsService = new EndAllUserSessionsService($connection);
        $this->validateAddSessionDataService = new ValidateAddSessionDataService();
        $this->getSessionIdService = new GetSessionIdService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addSession(array $data): array
    {
        $sanitizedData = SanitizeSessionDataService::sanitizeData($data);
        $validatedData = $this->validateAddSessionDataService->validateData($sanitizedData);

        $session_key = $this->getSessionIdService->getUniqueId();
        $validatedData['sessionHash'] = Utilities::passwordHash($session_key);
        $validatedData['sessionStatus'] = 'active';

        $date_time = new DateTime('now');
        $validatedData['sessionExpiry'] = $date_time
            ->modify('+24 hours')
            ->format('Y-m-d H:i:s');

        $sessionData = MapSessionDataService::mapData($validatedData);
        $sessionData->session_id = $this->getSessionIdService->getUniqueId();

        if ($this->endAllUserSessionsService->endSession($sessionData->user_id)) {
            if ($this->addNewSessionRepository->addSession($sessionData)) {
                return [
                    'token' => $session_key . $sessionData->session_id
                ];
            }
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
