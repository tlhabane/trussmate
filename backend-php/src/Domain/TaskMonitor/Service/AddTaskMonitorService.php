<?php

namespace App\Domain\TaskMonitor\Service;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\TaskMonitor\Repository\AddEscalationMonitorRepository;
use App\Domain\TaskMonitor\Repository\AddEscalationNotificationRepository;
use App\Domain\TaskMonitor\Repository\AddEscalationTaskRepository;
use App\Domain\TaskMonitor\Repository\DeleteEscalationMonitorRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use Exception;
use PDO;

final class AddTaskMonitorService
{
    private AddEscalationTaskRepository $addEscalationTaskRepository;
    private AddEscalationMonitorRepository $addEscalationMonitorRepository;
    private AddEscalationNotificationRepository $addEscalationNotificationRepository;
    private DeleteEscalationMonitorRepository $deleteEscalationMonitorRepository;
    private GetEscalationTaskIdService $getEscalationTaskIdService;
    private GetEscalationIdService $getEscalationIdService;

    public function __construct(PDO $connection)
    {
        $this->addEscalationTaskRepository = new AddEscalationTaskRepository($connection);
        $this->addEscalationMonitorRepository = new AddEscalationMonitorRepository($connection);
        $this->addEscalationNotificationRepository = new AddEscalationNotificationRepository($connection);
        $this->deleteEscalationMonitorRepository = new DeleteEscalationMonitorRepository($connection);
        $this->getEscalationTaskIdService = new GetEscalationTaskIdService($connection);
        $this->getEscalationIdService = new GetEscalationIdService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addTaskMonitor(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);
        $sanitizedData = SanitizeTaskMonitorDataService::sanitize($data);
        $validatedData = ValidateAddUpdateTaskMonitorDataService::validate($sanitizedData);

        $taskMonitorData = SetTaskMonitorDataService::set($validatedData);
        $taskMonitorData->account_no = $data['accountNo'];
        $taskMonitorData->escalation_id = $this->getEscalationIdService->getId();

        try {
            if ($this->addEscalationMonitorRepository->addMonitor($taskMonitorData)) {
                if ($this->addEscalationNotificationRepository->addNotification($taskMonitorData)) {
                    $taskMonitorData->escalation_task_id = $this->getEscalationTaskIdService->getId();
                    if ($this->addEscalationTaskRepository->addTask($taskMonitorData)) {
                        return [
                            'success' => 'Task monitoring & escalation process saved',
                            'id' => $taskMonitorData->escalation_id
                        ];
                    }
                }
            }
            /* Rollback */
            $this->deleteEscalationMonitorRepository->deleteTask($taskMonitorData->escalation_id);
        } catch (Exception $e) {
            /* Rollback */
            $this->deleteEscalationMonitorRepository->deleteTask($taskMonitorData->escalation_id);
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }
        /*try {
            if ($this->addEscalationMonitorRepository->addMonitor($taskMonitorData)) {
                if ($this->addEscalationNotificationRepository->addNotification($taskMonitorData)) {
                    foreach ($taskMonitorData->tasks as $task) {
                        $taskMonitorData->task_id = $task;
                        $taskMonitorData->escalation_task_id = $this->getEscalationTaskIdService->getId();
                        $this->addEscalationTaskRepository->addTask($taskMonitorData);
                    }

                    return [
                        'success' => 'Task monitoring & escalation process saved',
                        'id' => $taskMonitorData->escalation_id
                    ];
                }
            }
        } catch (Exception $e) {
            $this->deleteEscalationMonitorRepository->deleteTask($taskMonitorData->escalation_id);
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }*/

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
