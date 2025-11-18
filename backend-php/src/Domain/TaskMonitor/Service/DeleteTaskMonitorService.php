<?php

namespace App\Domain\TaskMonitor\Service;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\TaskMonitor\Repository\DeleteEscalationMonitorRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class DeleteTaskMonitorService
{
    private DeleteEscalationMonitorRepository $deleteEscalationMonitorRepository;

    public function __construct(PDO $connection)
    {
        $this->deleteEscalationMonitorRepository = new DeleteEscalationMonitorRepository($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function deleteTaskMonitor(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);
        $sanitizedData = SanitizeTaskMonitorDataService::sanitize($data);
        if ($this->deleteEscalationMonitorRepository->deleteTask($sanitizedData['escalationId'])) {
            return [
                'success' => 'Monitoring task deleted',
                'id' => $sanitizedData['escalationId']
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
