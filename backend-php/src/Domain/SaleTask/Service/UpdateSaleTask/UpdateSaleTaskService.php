<?php

namespace App\Domain\SaleTask\Service\UpdateSaleTask;

use App\Domain\SaleTask\Service\SetSaleTaskDataService;
use App\Domain\SaleTask\Service\SanitizeSaleTaskDataService;
use App\Domain\SaleTask\Service\AddSaleTaskJobService;
use App\Domain\SaleTask\Service\AddSaleTaskDocumentService;
use App\Domain\SaleTask\Repository\GetSaleTaskBySaleIdRepository;
use App\Domain\SaleTask\Repository\GetSaleTaskBySaleTaskIdRepository;
use App\Domain\SaleTask\Repository\UpdateSaleTaskRepository;
use App\Domain\SaleTask\Repository\LogSaleTaskUpdateRepository;
use App\Domain\SaleTask\Service\GetSaleTaskStatusService;
use App\Domain\Task\Service\GetTaskPaymentTypeService;
use App\Exception\RuntimeException;
use DateTime;
use PDO;

final class UpdateSaleTaskService
{
    private AddSaleTaskJobService $addSaleTaskJobService;
    private AddSaleTaskDocumentService $addSaleTaskDocumentService;
    private GetSaleTaskBySaleTaskIdRepository $getSaleTaskBySaleTaskIdRepository;
    private GetSaleTaskBySaleIdRepository $getSaleTaskBySaleIdRepository;
    private UpdateSaleTaskRepository $updateSaleTaskRepository;
    private LogSaleTaskUpdateRepository $logSaleTaskUpdateRepository;

    public function __construct(PDO $connection)
    {
        $this->addSaleTaskJobService = new AddSaleTaskJobService($connection);
        $this->addSaleTaskDocumentService = new AddSaleTaskDocumentService($connection);
        $this->getSaleTaskBySaleIdRepository = new GetSaleTaskBySaleIdRepository($connection);
        $this->getSaleTaskBySaleTaskIdRepository = new GetSaleTaskBySaleTaskIdRepository($connection);
        $this->updateSaleTaskRepository = new UpdateSaleTaskRepository($connection);
        $this->logSaleTaskUpdateRepository = new LogSaleTaskUpdateRepository($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function updateTask(array $data): array
    {
        $sanitizedData = SanitizeSaleTaskDataService::sanitizeData($data);
        $validatedData = ValidateUpdateSaleTaskDataService::validateData($sanitizedData);

        $saleTaskData = SetSaleTaskDataService::set($validatedData);
        $saleTaskData->user_id = $data['sessionUsername'];
        $tasks = $this->getSaleTaskBySaleTaskIdRepository->getTaskBySaleId($saleTaskData->sale_task_id);

        foreach ($tasks as $task) {
            $current_completion_date = date_create_from_format('Y-m-d', $task['task_completion_date']);
            $updated_completion_date = date_create_from_format('Y-m-d', $saleTaskData->task_completion_date);
            if ($current_completion_date && $updated_completion_date) {
                $date_diff = date_diff($current_completion_date, $updated_completion_date);
                $difference = intval($date_diff->format('%r%a'));

                if ($difference !== 0) {
                    $all_tasks = $this->getSaleTaskBySaleIdRepository->getTaskBySaleId($task['sale_id']);
                    //$filteredArray = array_filter((array)$all_tasks, fn($item) => intval($item['task_no']) >= intval($task['task_no']));
                    // $affected_tasks = [];
                    foreach ($all_tasks as $item) {
                        if (intval($item['task_no']) < intval($task['task_no'])) {
                            continue;
                        }
                        // $affected_tasks[] = $item['task_name'];

                        $task_completion_date = date_create_from_format('Y-m-d', $item['task_completion_date']);
                        $task_completion_date->modify("{$difference} days");

                        $updated_task_completion_date = $task_completion_date->format('Y-m-d');
                        $updated_task_days = intval($item['task_days']) + $difference;

                        $updated_sale_task_data = $saleTaskData;
                        $updated_sale_task_data->task_id = $task['task_id'];
                        //$updated_sale_task_data->comments = $saleTaskData->comments;
                        $updated_sale_task_data->task_days = $updated_task_days;
                        $updated_sale_task_data->task_completion_date = $updated_task_completion_date;
                        if ($item['sale_task_id'] !== $saleTaskData->sale_task_id) {
                            $updated_sale_task_data->sale_task_id = $item['sale_task_id'];
                            $updated_sale_task_data->task_id = $item['task_id'];
                            $updated_sale_task_data->task_status = GetSaleTaskStatusService::getStatus($item['task_status']);
                            $updated_sale_task_data->task_frequency = intval($item['task_frequency']);
                            $updated_sale_task_data->task_payment = floatval($item['task_payment']);
                            $updated_sale_task_data->task_payment_type = GetTaskPaymentTypeService::getType($item['task_payment_type']);
                            //$updated_sale_task_data->comments = "{$task['task_name']} update: {$saleTaskData->comments}";
                        }

                        if ($this->updateSaleTaskRepository->updateTask($updated_sale_task_data)) {
                            // Log task state: Changes to sale task
                            $this->logSaleTaskUpdateRepository->logTaskState($updated_sale_task_data);
                        }
                    }

                    /*return [
                        'affectedTasks' => $affected_tasks
                    ];*/
                    // Add estimate (job) info
                    $this->addSaleTaskJobService->addJob($data, $saleTaskData);

                    // Add documents
                    $this->addSaleTaskDocumentService->addDocument($data, $saleTaskData);

                    return [
                        'success' => 'Sale task updated',
                        'id' => $saleTaskData->sale_task_id
                    ];
                }

                if ($this->updateSaleTaskRepository->updateTask($saleTaskData)) {
                    // Log task state: Changes to sale task
                    if ($this->logSaleTaskUpdateRepository->logTaskState($saleTaskData)) {
                        // Add estimate (job) info
                        $this->addSaleTaskJobService->addJob($data, $saleTaskData);

                        // Add documents
                        $this->addSaleTaskDocumentService->addDocument($data, $saleTaskData);

                        return [
                            'success' => 'Sale task updated',
                            'id' => $saleTaskData->sale_task_id
                        ];
                    }
                }
            }
        }

        /*// Add estimate (job) info
        $this->addSaleTaskJobService->addJob($data, $saleTaskData);

        // Add documents
        $this->addSaleTaskDocumentService->addDocument($data, $saleTaskData);

        if ($this->updateSaleTaskRepository->updateTask($saleTaskData)) {
            // Log task state: Changes to sale task
            if ($this->logSaleTaskUpdateRepository->logTaskState($saleTaskData)) {
                return [
                    'success' => 'Sale task updated',
                    'id' => $saleTaskData->sale_task_id
                ];
            }
        }*/

        throw new RuntimeException(
            'Oops! An error occurred while processing your request, please try again.'
        );
    }
}
