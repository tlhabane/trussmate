<?php

namespace App\Domain\SaleTask\Service;

use App\Domain\Invoice\Service\SendInvoiceService;
use App\Domain\Sale\Service\SendSaleQuotationService;
use App\Domain\SaleDocument\Data\DocumentType;
use App\Domain\SaleDocument\Service\AddSaleDocumentService;
use App\Domain\SaleJob\Service\AddUpdateSaleJobService;
use App\Domain\SaleTask\Data\SaleTaskData;
use App\Domain\SaleTask\Data\TaskStatus;
use App\Domain\SaleTask\Repository\GetSaleTaskBySaleIdRepository;
use App\Domain\SaleTask\Repository\GetSaleTaskBySaleTaskIdRepository;
use App\Domain\SaleTask\Repository\LogSaleTaskUpdateRepository;
use App\Domain\SaleTask\Repository\UpdateSaleTaskStatusRepository;
use App\Exception\RuntimeException;
use App\Exception\ValidationException;
use PDO;
use Psr\Container\ContainerExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class UpdateSaleTaskStatusService
{
    private AddSaleDocumentService $addSaleDocumentService;
    private AddUpdateSaleJobService $addUpdateSaleJobService;

    private AddSaleTaskJobService $addSaleTaskJobService;
    private AddSaleTaskDocumentService $addSaleTaskDocumentService;
    private UpdateSaleTaskStatusRepository $updateSaleTaskStatusRepository;
    private LogSaleTaskUpdateRepository $logSaleTaskUpdateRepository;
    private GetSaleTaskBySaleTaskIdRepository $getSaleTaskBySaleTaskIdRepository;
    private GetSaleTaskBySaleIdRepository $getSaleTaskBySaleIdRepository;
    private SendSaleQuotationService $sendSaleQuotationService;
    private SendInvoiceService $sendInvoiceService;

    public function __construct(PDO $connection)
    {
        $this->addSaleDocumentService = new AddSaleDocumentService($connection);
        $this->addUpdateSaleJobService = new AddUpdateSaleJobService($connection);

        $this->addSaleTaskJobService = new AddSaleTaskJobService($connection);
        $this->addSaleTaskDocumentService = new AddSaleTaskDocumentService($connection);
        $this->updateSaleTaskStatusRepository = new UpdateSaleTaskStatusRepository($connection);
        $this->logSaleTaskUpdateRepository = new LogSaleTaskUpdateRepository($connection);
        $this->getSaleTaskBySaleTaskIdRepository = new GetSaleTaskBySaleTaskIdRepository($connection);
        $this->getSaleTaskBySaleIdRepository = new GetSaleTaskBySaleIdRepository($connection);
        $this->sendSaleQuotationService = new SendSaleQuotationService($connection);
        $this->sendInvoiceService = new SendInvoiceService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateStatus(array $data): array
    {
        $sanitizedData = SanitizeSaleTaskDataService::sanitizeData($data);
        if (empty($sanitizedData['saleTaskId'])) {
            throw new ValidationException('Invalid or missing task details');
        }
        if (empty($sanitizedData['taskStatus'])) {
            throw new ValidationException('Invalid task status provided');
        }

        $taskData = SetSaleTaskDataService::set($sanitizedData);
        $taskData->user_id = $data['sessionUsername'];

        /* Add estimate (job) info */
        $this->addSaleTaskJobService->addJob($data, $taskData);
        // $this->addJob($data, $taskData);

        /* Add files */
        $this->addSaleTaskDocumentService->addDocument($data, $taskData);
        // $this->addFiles($data, $taskData);

        /* Send Quotation or invoice */
        $tasks = $this->getSaleTaskBySaleTaskIdRepository->getTaskBySaleId($taskData->sale_task_id);
        foreach ($tasks as $task) {
            /*$action = $task['task_action'];
            $updatedTaskStatusData = [];
            if ($action === 'quotation' ||  $action === 'invoice' || $action === 'proforma_invoice') {
                $updatedTaskStatusData = $this->processTask($task, $data);
            }*/

            /* Process next automatic tasks */
            /*$completedTask = (
                !empty($updatedTaskStatusData) && $updatedTaskStatusData['taskStatus'] === TaskStatus::COMPLETED ||
                $taskData->task_status === TaskStatus::COMPLETED
            );
            if ($taskData->task_status === TaskStatus::COMPLETED) {
                $this->processAutomatedTasks($task['sale_id'], $task['task_no'], $taskData, $data);
            }*/

            $sanitizedData['taskDays'] = $task['task_days'];
            $sanitizedData['taskFrequency'] = $task['task_frequency'];
            $sanitizedData['taskPayment'] = $task['task_payment'];
            $sanitizedData['taskPaymentType'] = $task['task_payment_type'];
            $sanitizedData['comments'] = 'Task status update';
            $sanitizedData['taskCompletionDate'] = $task['task_completion_date'];

            $taskData = SetSaleTaskDataService::set($sanitizedData);
            $taskData->user_id = $data['sessionUsername'];
            $taskData->task_status = $updatedTaskStatusData['taskStatus'] ?? $taskData->task_status;

            if ($this->updateSaleTaskStatusRepository->updateStatus($taskData)) {
                if ($this->logSaleTaskUpdateRepository->logTaskState($taskData)) {
                    return [
                        'success' => 'Task status updated.',
                        'id' => $taskData->sale_task_id
                    ];
                }
            }
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing your request, please try again.'
        );
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    private function addJob(array $data, SaleTaskData $taskData): void
    {
        if (isset($data['job'])) {
            $job = json_decode($data['job'], true);
            $this->addUpdateSaleJobService->addUpdateSale([
                'accountNo' => $data['accountNo'],
                'sessionUsername' => $taskData->user_id,
                'saleId' => $taskData->sale_id,
                'jobNo' => $job['jobNo'] ?? '',
                'jobDescription' => $job['jobDescription'] ?? '',
                'designInfo' => $job['designInfo'] ?? [],
                'lineItems' => $job['lineItems'] ?? [],
                'subtotal' => $job['subtotal'] ?? 0,
                'vat' => $job['vat'] ?? 0,
                'total' => $job['total'] ?? 0
            ]);
        }
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    private function addFiles(array $data, SaleTaskData $taskData): void
    {
        if (isset($data['files']) && is_array($data['files']) && count($data['files']) > 0) {
            $doc_type = isset($data['job']) ? DocumentType::ESTIMATE : DocumentType::OTHER;
            $this->addSaleDocumentService->addDocument([
                'accountNo' => $data['accountNo'],
                'sessionUsername' => $taskData->user_id,
                'saleId' => $taskData->sale_id,
                'saleTaskId' => $taskData->sale_task_id,
                'files' => $data['files'],
                'docType' => $doc_type->value
            ]);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws RuntimeException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    private function processAutomatedTasks(string $sale_id, int $task_no, SaleTaskData $taskData, array $data): void
    {
        $nextTasks = $this->getSaleTaskBySaleIdRepository->getTaskBySaleId($sale_id, $task_no + 1);
        foreach ($nextTasks as $task) {
            if ($task['trigger_type'] === 'automatic') {
                $updateTaskStatusData = $this->processTask($task, $data);
                if (!empty($updateTaskStatusData)) {
                    $taskData->sale_task_id = $task['sale_task_id'];
                    $taskData->task_status = $updateTaskStatusData['taskStatus'];

                    $this->updateSaleTaskStatusRepository->updateStatus($taskData);
                    $this->logSaleTaskUpdateRepository->logTaskState($taskData);
                }
            } else {
                break;
            }

            $this->processAutomatedTasks($sale_id, $task['task_no'], $taskData, $data);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws RuntimeException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    private function processTask(mixed $task, array $data): array
    {
        // If the task action is to send a quotation or invoice, trigger the respective service
        $completeTask = false;
        $saleSendingData = [
            'accountNo' => $data['accountNo'],
            'sessionUsername' => $data['sessionUsername'],
            'sessionUserRole' => $data['sessionUserRole'],
            'saleId' => $task['sale_id'],
            'saleTaskId' => $task['sale_task_id']
        ];
        if ($task['task_action'] === 'quotation') {
            $this->sendSaleQuotationService->sendQuotation($saleSendingData);
            /*$completeTask = true;*/
        } elseif ($task['task_action'] === 'invoice' || $task['task_action'] === 'proforma_invoice') {
            $this->sendInvoiceService->sendInvoice($saleSendingData);
            $completeTask = true;
        }

        // Update task status to completed or started based on action
        return [
            'taskStatus' => $completeTask ? TaskStatus::COMPLETED : TaskStatus::STARTED,
            'accountNo' => $data['accountNo'],
            'sessionUsername' => $data['sessionUsername'],
        ];
    }
}
