<?php

namespace App\Domain\TaskMonitor\Service;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\TaskMonitor\Repository\UpdateEscalationMonitorRepository;
use App\Domain\TaskMonitor\Repository\UpdateEscalationNotificationRepository;
use App\Domain\TaskMonitor\Repository\UpdateEscalationTaskRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use Exception;
use PDO;

final class UpdateTaskMonitorService
{
    private UpdateEscalationTaskRepository $updateEscalationTaskRepository;
    private UpdateEscalationMonitorRepository $updateEscalationMonitorRepository;
    private UpdateEscalationNotificationRepository $updateEscalationNotificationRepository;

    public function __construct(PDO $connection)
    {
        $this->updateEscalationTaskRepository = new UpdateEscalationTaskRepository($connection);
        $this->updateEscalationMonitorRepository = new UpdateEscalationMonitorRepository($connection);
        $this->updateEscalationNotificationRepository = new UpdateEscalationNotificationRepository($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateTaskMonitor(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);
        $sanitizedData = SanitizeTaskMonitorDataService::sanitize($data);
        $validatedData = ValidateAddUpdateTaskMonitorDataService::validate($sanitizedData);
        /* Additional update validation */
        if (empty($validatedData['escalationId'])) {
            throw new ValidationException('Invalid task monitoring & escalation process provided');
        }
        if (empty($validatedData['escalationTaskId'])) {
            throw new ValidationException('Invalid task provided');
        }

        try {
            $taskMonitorData = SetTaskMonitorDataService::set($validatedData);
            if ($this->updateEscalationMonitorRepository->updateMonitor($taskMonitorData)) {
                if ($this->updateEscalationNotificationRepository->updateNotification($taskMonitorData)) {
                    if ($this->updateEscalationTaskRepository->updateTask($taskMonitorData)) {
                        return [
                            'success' => 'Task monitoring & escalation process update',
                            'id' => $taskMonitorData->escalation_id
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
