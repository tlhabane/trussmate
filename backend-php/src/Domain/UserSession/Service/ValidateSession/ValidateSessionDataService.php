<?php

namespace App\Domain\UserSession\Service\ValidateSession;

use App\Contract\DataValidationContract;
use App\Domain\UserSession\Repository\GetSessionByIdRepository;
use App\Domain\UserSession\Service\GetSessionStatusService;
use App\Domain\User\Service\GetUserByIdService;
use App\Exception\ValidationException;
use App\Util\Utilities;
use PDO;

final class ValidateSessionDataService implements DataValidationContract
{
    private GetSessionByIdRepository $getSessionByIdRepository;
    private GetUserByIdService $getUserByIdService;

    public function __construct(PDO $connection)
    {
        $this->getSessionByIdRepository = new GetSessionByIdRepository($connection);
        $this->getUserByIdService = new GetUserByIdService($connection);
    }

    public function validateData(array $data): array
    {
        $sessions = $this->getSessionByIdRepository->getSession($data['sessionId']);
        if (empty($data['sessionKey']) || empty($data['sessionId']) || $sessions->rowCount() !== 1) {
            throw new ValidationException('Invalid or expired session, please login again.', 401);
        }

        $sessionData = [];
        foreach ($sessions as $session) {
            $sessionValid = Utilities::validatePassword($data['sessionKey'], $session['session_hash']);
            $sessionPastExpiry = strtotime('now') >= strtotime($session['session_expiry']);
            $sessionStatus = GetSessionStatusService::getStatus($session['session_status']);
            if (!$sessionValid || $sessionStatus->value > 1 || $sessionPastExpiry) {
                throw new ValidationException('Invalid or expired session, please login again.', 401);
            }

            $users = $this->getUserByIdService->getUser($session['user_id']);
            if (count($users['records']) !== 1) {
                throw new ValidationException('Invalid or expired session, please login again.', 401);
            }

            foreach ($users['records'] as $user) {
                $sessionData = array_merge($data, [
                    'sessionHash' => $session['session_hash'],
                    'sessionStatus' => $session['session_status'],
                    'accountNo' => $session['account_no'],
                    'userId' => $session['user_id'],
                    'regionId' => $user['regionId'],
                    'regionName' => $user['regionName'],
                    'firstName' => $user['firstName'],
                    'userRole' => $session['user_role'],
                ]);
            }
        }

        return $sessionData;
    }
}
