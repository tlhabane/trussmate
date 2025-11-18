<?php

namespace App\Domain\Task\Data;

final class TaskData
{
    public string $account_no;
    public string $task_id;
    public string $task_name;
    public string $task_description;
    public float $task_payment;
    public TaskPaymentType $task_payment_type;
    public int $task_days;
    public int $task_frequency;
    public int $task_document;
    public TaskAction $task_action;
    public string $search;
}
