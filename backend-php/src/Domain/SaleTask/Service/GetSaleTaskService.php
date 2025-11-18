<?php

namespace App\Domain\SaleTask\Service;

use App\Domain\User\Data\UserRole;
use App\Domain\SaleTask\Data\TaskStatus;
use App\Domain\User\Service\GetUserRoleService;
use App\Domain\SaleTask\Repository\GetSaleTaskRepository;
use App\Domain\SaleTask\Repository\GetSaleTaskBySaleIdRepository;
use App\Domain\SaleJob\Service\GetSaleJobService;
use App\Exception\ValidationException;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetSaleTaskService
{
    private GetSaleTaskBySaleIdRepository $getSaleTaskBySaleIdRepository;
    private GetSaleTaskLogService $getSaleTaskLogService;
    private GetSaleTaskRepository $getSaleTaskRepository;
    private GetSaleJobService $getSaleJobService;

    public function __construct(PDO $connection)
    {
        $this->getSaleTaskBySaleIdRepository = new GetSaleTaskBySaleIdRepository($connection);
        $this->getSaleTaskLogService = new GetSaleTaskLogService($connection);
        $this->getSaleTaskRepository = new GetSaleTaskRepository($connection);
        $this->getSaleJobService = new GetSaleJobService($connection);
    }

    /**
     * @throws ValidationException
     */
    public function getTask(array $data): array
    {
        $sanitizedData = SanitizeSaleTaskDataService::sanitizeData($data);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );

        $saleTaskData = SetSaleTaskDataService::set($sanitizedData);
        $saleTaskData->account_no = $data['accountNo'];
        $user_role = GetUserRoleService::getUserRole($data['sessionUserRole']);
        $saleTaskData->user_role = UserRole::none;
        if ($user_role !== UserRole::admin && $user_role !== UserRole::super_admin && $user_role !== UserRole::system) {
            $saleTaskData->user_role = $user_role;
        }

        $records = [];
        $tasks = $this->getSaleTaskRepository->getTask(
            $saleTaskData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );


        foreach ($tasks as $task) {
            $task_logs = $this->getSaleTaskLogService->getLog($task['sale_task_id']);

            $jobs = $this->getSaleJobService->getSaleJob([
                'accountNo' => $data['accountNo'],
                'saleId' => $task['sale_id']
            ]);

            $total = array_reduce(($jobs['records'] ?? []), function (float $acc, $job) {
                $acc += $job['total'];
                return $acc;
            }, 0);

            $total_payable = $task['task_payment'];
            if ($task['task_payment_type'] == 'percentage') {
                $total_payable = ($task['task_payment'] / 100) * $total;
            }

            $task_enabled = 1;
            $prev_task_no = intval($task['task_no']) - 1;
            if ($prev_task_no > 0) {
                $prev_tasks = $this->getSaleTaskBySaleIdRepository->getTaskBySaleId($task['sale_id'], $prev_task_no);
                foreach ($prev_tasks as $prev_task) {
                    $prev_task_status = GetSaleTaskStatusService::getStatus($prev_task['task_status']);
                    $task_enabled = intval($prev_task_status === TaskStatus::COMPLETED);
                }
            }

            $records[] = [
                'saleTaskId' => $task['sale_task_id'],
                'saleId' => $task['sale_id'],
                'saleNo' => Utilities::addPadding($task['sale_no'], 0),
                'invoiceNo' => Utilities::addPadding($data['invoice_no'] ?? '', 0),
                'saleTotal' => $total,
                'customerId' => $task['customer_id'],
                'customerName' => Utilities::decodeUTF8($task['customer_name']),
                'contactId' => $task['contact_id'],
                'contactFirstName' => Utilities::decodeUTF8($task['first_name'] ?? ''),
                'contactLastName' => Utilities::decodeUTF8($task['last_name'] ?? ''),
                'taskId' => $task['task_id'],
                'taskNo' => intval($task['task_no']),
                'triggerType' => $task['trigger_type'],
                'taskOptional' => intval($task['task_optional']),
                'taskStatus' => $task['task_status'],
                'taskLogs' => $task_logs['records'] ?? [],
                'taskName' => Utilities::decodeUTF8($task['task_name']),
                'taskDescription' => Utilities::decodeUTF8($task['task_description']),
                'taskAction' => $task['task_action'],
                'taskEnabled' => $task_enabled,
                'taskCompletionDate' => date('c', strtotime($task['task_completion_date'])),
                'taskPayment' => floatval($task['task_payment']),
                'taskPaymentType' => $task['task_payment_type'],
                'taskDays' => intval($task['task_days']),
                'taskFrequency' => intval($task['task_frequency']),
                'taskDocument' => intval($task['task_document']),
                'assignedTo' => $task['assigned_to'],
                'assignmentNote' => Utilities::decodeUTF8($task['assignment_note']),
                'totalPayable' => $total_payable,
                'totalPaid' => floatval($task['total_paid']),
                'balanceDue' => $total_payable + floatval($task['total_paid']),
                'taskDate' => date('c', strtotime($task['created']))
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getSaleTaskRepository->getTask($saleTaskData);
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
