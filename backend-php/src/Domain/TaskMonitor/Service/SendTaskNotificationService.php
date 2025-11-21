<?php

namespace App\Domain\TaskMonitor\Service;

use App\Domain\User\Data\UserRole;
use App\Domain\SaleTask\Service\GetSaleTaskService;
use App\Domain\TaskMonitor\Data\TaskMonitorData;
use App\Domain\TaskMonitor\Repository\GetTaskNotificationRepository;
use App\Domain\TaskMonitor\Repository\AddTaskNotificationRepository;
use App\Domain\Messaging\Service\QueueTextMessage\QueueTextMessageService;
use App\Domain\User\Service\GetUserService;
use App\Exception\ValidationException;
use App\Util\Logger;
use Exception;
use PDO;

final class SendTaskNotificationService
{
    private GetSaleTaskService $getSaleTaskService;
    private GetTaskNotificationRepository $getTaskNotificationRepository;
    private AddTaskNotificationRepository $addTaskNotificationRepository;
    private QueueTextMessageService $queueTextMessageService;
    private GetUserService $getUserService;

    public function __construct(PDO $connection)
    {
        $this->getSaleTaskService = new GetSaleTaskService($connection);
        $this->getTaskNotificationRepository = new GetTaskNotificationRepository($connection);
        $this->addTaskNotificationRepository = new AddTaskNotificationRepository($connection);
        $this->queueTextMessageService = new QueueTextMessageService($connection);
        $this->getUserService = new GetUserService($connection);
    }

    public function sendNotification(array $data): void
    {
        try {
            $notifications = [];
            $from = 1;
            $to = 100;

            foreach (UserRole::cases() as $userRole) {
                // $data['sessionUserRole'] = $userRole->name;
                /*$data['page'] = $from;
                $data['recordsPerPage'] = $to;*/
                $tasks = $this->getSaleTaskService->getTask($data);
                if (count($tasks['records']) === 0) {
                    $from = 0;
                    $to = 100;
                    continue;
                }
                $assignedTasks = array_filter($tasks['records'], fn($task) => (
                    $task['assignedTo'] === $userRole->name && $task['taskEnabled'] > 0
                ));

                foreach ($assignedTasks as $task) {
                    $taskData = new TaskMonitorData();
                    $taskData->account_no = $data['accountNo'];
                    $taskData->sale_task_id = $task['saleTaskId'];
                    $taskData->task_notification_type = GetTaskNotificationTypeService::getNotificationType('notification');
                    $task_notifications = $this->getTaskNotificationRepository->getNotification($taskData);

                    $userData = [
                        'accountNo' => $data['accountNo'],
                        'sessionUsername' => $data['sessionUsername'],
                        'sessionUserRole' => $data['sessionUserRole'],
                        'userRole' => $userRole->name
                    ];
                    $recipients = [];
                    $users = $this->getUserService->getUser($userData);
                    foreach ($users['records'] as $user) {
                        if (empty($user['tel']) && $user['altTel']) {
                            continue;
                        }
                        $recipient_address = $user['tel'] ?? $user['altTel'];
                        $already_notified = false;
                        foreach ($task_notifications as $task_notification) {
                            if ($task_notification['recipient_address'] = $recipient_address) {
                                $already_notified = true;
                            }
                        }
                        if (!$already_notified) {
                            $recipients[] = [
                                'recipientName' => "{$user['firstName']} {$user['lastName']}",
                                'recipientAddress' => $user['tel'] ?? $user['altTel']
                            ];
                        }
                    }

                    if (count($recipients) > 0) {
                        $message = sprintf('New %s task for %s requires your attention, ref #%s.',
                            strtolower($task['taskName']),
                            $task['customerName'],
                            $task['saleNo']
                        );

                        $message_data = [
                            'accountNo' => $data['accountNo'],
                            'userId' => $data['sessionUsername'],
                            'sessionUserRole' => $data['sessionUserRole'],
                            'recipients' => $recipients,
                            'subject' => 'Task Notification',
                            'message' => $message,
                            'messagePriority' => 3,
                            'messageType' => 8
                        ];

                        $message_responses = $this->queueTextMessageService->queueMessage($message_data);
                        foreach ($message_responses['id'] as $response) {
                            $taskData->message_id = $response['messageId'];
                            if ($this->addTaskNotificationRepository->addNotification($taskData)) {
                                $notifications[] = $response['messageId'];
                            }
                        }
                    }
                }

                $from += $to;
                $to += $to;
            }

            Logger::addToLog(
                'task_notification_SUCCESS.log',
                sprintf('%s task notifications sent.', count($notifications))
            );
        } catch (Exception $exception) {
            Logger::addToLog(
                'task_notification_ERROR.log',
                sprintf('Error %s: %s', $exception->getCode(), $exception->getMessage()),
                true
            );
        }
    }
}
