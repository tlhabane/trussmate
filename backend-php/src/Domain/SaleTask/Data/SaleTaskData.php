<?php

namespace App\Domain\SaleTask\Data;

use App\Domain\User\Data\UserRole;
use App\Domain\Task\Data\TaskAction;
use App\Domain\Task\Data\TaskPaymentType;

final class SaleTaskData
{
    public string $account_no;
    public string $user_id;
    public UserRole $user_role;
    public string $customer_id;
    public string $sale_task_id;
    public string $sale_id;
    public string $task_id;
    public int $task_no;
    public TaskStatus $task_status;
    public TaskAction $task_action;
    public float $task_payment;
    public TaskPaymentType $task_payment_type;
    public int $task_days;
    public string $task_completion_date;
    public int $task_frequency;
    public string $comments;

    public string $search;
}
