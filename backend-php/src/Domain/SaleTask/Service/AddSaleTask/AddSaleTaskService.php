<?php

namespace App\Domain\SaleTask\Service\AddSaleTask;

use App\Domain\SaleTask\Service\SetSaleTaskDataService;
use App\Domain\SaleTask\Service\SanitizeSaleTaskDataService;
use App\Domain\SaleTask\Repository\AddSaleTaskRepository;
use App\Domain\SaleTask\Repository\DeleteSaleTaskRepository;
use App\Domain\SaleTask\Repository\LogSaleTaskUpdateRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddSaleTaskService
{
    private GetSaleTaskIdService $getSaleTaskIdService;
    private AddSaleTaskRepository $addSaleTaskRepository;
    private DeleteSaleTaskRepository $deleteSaleTaskRepository;
    private LogSaleTaskUpdateRepository $logSaleTaskUpdateRepository;

    public function __construct(PDO $connection)
    {
        $this->getSaleTaskIdService = new GetSaleTaskIdService($connection);
        $this->addSaleTaskRepository = new AddSaleTaskRepository($connection);
        $this->deleteSaleTaskRepository = new DeleteSaleTaskRepository($connection);
        $this->logSaleTaskUpdateRepository = new LogSaleTaskUpdateRepository($connection);
    }

    /**
     * @throws RuntimeException
     * @throws ValidationException
     */
    public function addTask(array $data): array
    {
        $sanitizedData = SanitizeSaleTaskDataService::sanitizeData($data);
        $validatedData = ValidateAddSaleTaskDataService::validateData($sanitizedData);

        $saleTaskData = SetSaleTaskDataService::set($validatedData);
        $saleTaskData->sale_task_id = $this->getSaleTaskIdService->getId(64);

        if ($this->addSaleTaskRepository->addTask($saleTaskData)) {
            // Log task state: New sale task added
            $saleTaskData->user_id = $data['sessionUsername'];
            if ($this->logSaleTaskUpdateRepository->logTaskState($saleTaskData)) {
                return [
                    'success' => 'Sale task added',
                    'id' => $saleTaskData->sale_task_id
                ];
            }

            // Rollback
            $this->deleteSaleTaskRepository->deleteTask($saleTaskData->sale_task_id);
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing your request, please try again.'
        );
    }
}
