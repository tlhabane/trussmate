<?php

namespace App\Domain\Workflow\Service;

use App\Domain\Workflow\Repository\GetWorkflowRepository;
use App\Domain\Workflow\Repository\GetWorkflowTaskRepository;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetWorkflowService
{
    private GetWorkflowRepository $getWorkflowRepository;
    private GetWorkflowTaskRepository $getWorkflowTaskRepository;

    public function __construct(PDO $connection)
    {
        $this->getWorkflowRepository = new GetWorkflowRepository($connection);
        $this->getWorkflowTaskRepository = new GetWorkflowTaskRepository($connection);
    }

    public function getWorkflow(array $data): array
    {
        $sanitizedData = SanitizeWorkflowDataService::sanitizeData($data);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );

        $workflowData = MapWorkflowDataService::map($sanitizedData);
        $workflowData->account_no = $data['accountNo'];

        $records = [];
        $workflows = $this->getWorkflowRepository->getWorkflow(
            $workflowData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );

        foreach ($workflows as $workflow) {
            $workflow_tasks = $this->getWorkflowTaskRepository->getTask($workflow['workflow_id']);
            $tasks = [];
            foreach ($workflow_tasks as $task) {
                $tasks[] = [
                    'workflowTaskId' => $task['workflow_task_id'],
                    'taskId' => $task['task_id'],
                    'taskNo' => intval($task['task_no']),
                    'triggerType' => $task['trigger_type'],
                    'taskOptional' => intval($task['task_optional']),
                    'assignedTo' => $task['assigned_to'],
                    'assignmentNote' => Utilities::decodeUTF8($task['assignment_note']),
                    'taskName' => Utilities::decodeUTF8($task['task_name']),
                    'taskDescription' => Utilities::decodeUTF8($task['task_description']),
                    'taskDays' => intval($task['task_days']),
                    'taskFrequency' => $task['task_frequency'],
                    'taskPayment' => floatval($task['task_payment']),
                    'taskPaymentType' => $task['task_payment_type'],
                    'taskAction' => $task['task_action'],
                ];
            }

            $records[] = [
                'workflowId' => $workflow['workflow_id'],
                'workflowName' => Utilities::decodeUTF8($workflow['workflow_name']),
                'delivery' => $workflow['delivery_required'],
                'labour' => $workflow['labour_required'],
                'tasks' => $tasks,
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getWorkflowRepository->getWorkflow($workflowData);
            $pagination = DataPagination::getPagingLinks(
                $sanitizedData['page'],
                $countRecords->rowCount(),
                $paginationConfig['recordsPerPage']
            );

            return ['records' => $records, 'pagination' => $pagination];
        }

        return ['records' => $records];
    }
}
