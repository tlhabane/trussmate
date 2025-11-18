<?php

namespace App\Domain\Messaging\Service;

use App\Domain\Messaging\Data\MessageChannel;
use App\Domain\Messaging\Data\MessageStatus;
use App\Domain\Messaging\Data\MessagePriority;
use App\Domain\Messaging\Data\MessagingData;
use App\Domain\Messaging\Repository\GetMessageRepository;
use App\Domain\Messaging\Repository\GetMessageAttachmentRepository;
use App\Domain\Messaging\Repository\UpdateMessageStatusRepository;
use App\Exception\RuntimeException;
use App\Util\Utilities;
use App\Util\Logger;
use PDO;

final class SendQueuedEmailMessageService
{
    private GetMessageRepository $getMessageRepository;
    private GetMessageAttachmentRepository $getMessageAttachmentRepository;
    private UpdateMessageStatusRepository $updateMessageStatusRepository;

    public function __construct(PDO $connection)
    {
        $this->getMessageRepository = new GetMessageRepository($connection);
        $this->getMessageAttachmentRepository = new GetMessageAttachmentRepository($connection);
        $this->updateMessageStatusRepository = new UpdateMessageStatusRepository($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function sendEmail($message_priority = 'medium'): void
    {
        $messagingData = new MessagingData();
        $messagingData->channel = MessageChannel::email;
        $messagingData->message_status = MessageStatus::queued;
        $messagingData->message_priority = MessagePriority::getValueFromType($message_priority);

        $messages = $this->getMessageRepository->getMessage($messagingData, 0, 99);
        echo $messages->rowCount();
        if ($messages->rowCount()) {
            $sent_messages = 0;

            foreach ($messages as $message) {
                $emailSubject = Utilities::decodeUTF8($message['subject']);
                $recipientName = Utilities::decodeUTF8($message['recipient_name']);
                $recipientAddress = $message['recipient_address'];
                $recipientAttachments = [];
                $attachments = $this->getMessageAttachmentRepository->getAttachment($message['message_id']);
                foreach ($attachments as $attachment) {
                    $recipientAttachments[] = [
                        'fileSource' => $attachment['file_source'],
                        'fileName' => $attachment['filename']
                    ];
                }

                $emailServiceData = [
                    'subject' => $emailSubject,
                    'messageBody' => Utilities::decodeUTF8($message['message']),
                    'recipientEmail' => $recipientAddress,
                    'recipientName' => $recipientName,
                    'attachments' => $recipientAttachments,
                    'senderName' => Utilities::decodeUTF8($message['sender_name']),
                    'senderEmail' => $message['sender_email']
                ];

                SendEmailMessageService::sendEmail($emailServiceData);
                $messagingData->recipient_id = $message['recipient_id'];
                $messagingData->message_status = MessageStatus::sent;
                if ($this->updateMessageStatusRepository->updateMessageStatus($messagingData)) {
                    $sent_messages += 1;
                }
            }

            Logger::addToLog(
                'mail.log',
                sprintf('%s %s priority emails sent successfully.', $sent_messages, $message_priority)
            );
        }
    }
}
