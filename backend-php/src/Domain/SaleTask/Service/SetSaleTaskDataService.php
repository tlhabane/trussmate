<?php

namespace App\Domain\SaleTask\Service;

use App\Domain\Task\Service\GetTaskActionService;
use App\Domain\Task\Service\GetTaskPaymentTypeService;
use App\Domain\SaleTask\Data\SaleTaskData;

final class SetSaleTaskDataService
{
    public static function set(array $data): SaleTaskData
    {
        $saleTaskData = new SaleTaskData();
        $saleTaskData->sale_task_id = $data['saleTaskId'];
        $saleTaskData->sale_id = $data['saleId'];
        $saleTaskData->task_id = $data['taskId'];
        $saleTaskData->task_no = $data['taskNo'];
        $saleTaskData->task_status = GetSaleTaskStatusService::getStatus($data['taskStatus']);
        $saleTaskData->task_action = GetTaskActionService::getAction($data['taskAction']);
        $saleTaskData->task_payment = $data['taskPayment'];
        $saleTaskData->task_completion_date = $data['taskCompletionDate'];
        $saleTaskData->task_payment_type = GetTaskPaymentTypeService::getType($data['taskPaymentType']);
        $saleTaskData->task_days = $data['taskDays'];
        $saleTaskData->task_frequency = $data['taskFrequency'];
        $saleTaskData->comments = $data['comments'];
        $saleTaskData->customer_id = $data['customerId'];
        $saleTaskData->search = $data['search'];
        return $saleTaskData;
    }
}
