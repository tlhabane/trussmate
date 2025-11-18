<?php

namespace App\Domain\Sale\Service\AddSale;

use App\Domain\Workflow\Service\GetWorkflowService;
use App\Domain\SaleTask\Service\AddSaleTask\AddSaleTaskService;
use App\Domain\SaleTask\Service\DeleteAllSaleTasksService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use DateTime;
use PDO;

final class AddSaleWorkflowTasksService
{
    private GetWorkflowService $getWorkflowService;
    private AddSaleTaskService $addSaleTaskService;
    private DeleteAllSaleTasksService $deleteAllSaleTasksService;

    public function __construct(PDO $connection)
    {
        $this->getWorkflowService = new GetWorkflowService($connection);
        $this->addSaleTaskService = new AddSaleTaskService($connection);
        $this->deleteAllSaleTasksService = new DeleteAllSaleTasksService($connection);
    }


    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addTask(array $data): array
    {
        try {
            $tasks = [];
            $workflows = $this->getWorkflowService->getWorkflow($data);
            foreach ($workflows['records'] as $workflow) {
                $workflowTasks_copy = $workflow['tasks'];
                foreach ($workflow['tasks'] as $task) {
                    // Calculate total task (due) days
                    $filteredArray = array_filter($workflowTasks_copy, fn($item) => $item['taskNo'] <= $task['taskNo']);
                    $task_days = array_reduce($filteredArray, fn($total, $item) => $total + $item['taskDays'], 0);

                    $saleDate = new DateTime();
                    $saleDate->modify("+{$task_days} days");
                    $taskCompletionDate = $saleDate->format('Y-m-d');

                    $task_data = [
                        'accountNo' => $data['accountNo'],
                        'sessionUsername' => $data['sessionUsername'],
                        'saleId' => $data['saleId'],
                        'taskId' => $task['taskId'],
                        'taskNo' => $task['taskNo'],
                        'taskDays' => $task_days,
                        'taskCompletionDate' => $taskCompletionDate,
                        'taskFrequency' => $task['taskFrequency'],
                        'taskPayment' => $task['taskPayment'],
                        'taskPaymentType' => $task['taskPaymentType'],
                        'taskStatus' => 'pending',
                        'comments' => "New task: {$task['taskName']}"
                    ];

                    $response = $this->addSaleTaskService->addTask($task_data);
                    $tasks[] = $response['id'];
                }
            }
            return [
                'success' => sprintf("%s tasks added", count($tasks)),
                'id' => $tasks
            ];
        } catch (ValidationException|RuntimeException $exception) {
            $this->deleteAllSaleTasksService->deleteTasks($data);
            if ($exception instanceof ValidationException) {
                throw new ValidationException($exception->getMessage(), $exception->getCode(), $exception->getErrors());
            }
            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }
    }
}
