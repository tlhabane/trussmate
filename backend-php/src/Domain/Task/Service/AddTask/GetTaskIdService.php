<?php

namespace App\Domain\Task\Service\AddTask;

use App\Domain\Task\Repository\TaskIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetTaskIdService
{
    private TaskIdExistsRepository $taskIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->taskIdExistsRepository = new TaskIdExistsRepository($connection);
    }

    public function getId(int $length): string
    {
        do {
            $task_id = Utilities::generateToken($length);
        } while (empty($task_id) || $this->taskIdExistsRepository->idExists($task_id));

        return $task_id;
    }
}
