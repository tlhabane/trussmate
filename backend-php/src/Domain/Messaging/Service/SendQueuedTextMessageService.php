<?php

namespace App\Domain\Messaging\Service;

use App\Domain\Messaging\Data\MessageChannel;
use App\Domain\Messaging\Data\MessageStatus;
use App\Domain\Messaging\Data\MessagePriority;
use App\Domain\Messaging\Data\MessagingData;
use App\Domain\Messaging\Repository\GetMessageRepository;
use App\Domain\Messaging\Repository\AddServiceProviderMessageIdRepository;
use App\Domain\Messaging\Repository\UpdateMessageStatusRepository;
use App\Exception\RuntimeException;
use App\Util\Utilities;
use App\Util\Logger;
use PDO;

final class SendQueuedTextMessageService
{
    private static string $textMessageClientId = 'babde13e-6467-46ef-9cc6-ea2408c237fd';
    private static string $textMessageAPISecret = '1TYd17RyCO0DW8PQClyfJI86dzCUI6p3';
    private static string $textMessageAuthUrl = 'https://rest.smsportal.com/Authentication';
    private static string $textMessageUrl = 'https://rest.smsportal.com/bulkmessages';

    private GetMessageRepository $getMessageRepository;
    private AddServiceProviderMessageIdRepository $addServiceProviderMessageIdRepository;
    private UpdateMessageStatusRepository $updateMessageStatusRepository;

    public function __construct(PDO $connection)
    {
        $this->getMessageRepository = new GetMessageRepository($connection);
        $this->addServiceProviderMessageIdRepository = new AddServiceProviderMessageIdRepository($connection);
        $this->updateMessageStatusRepository = new UpdateMessageStatusRepository($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function sendTextMessage($message_priority = 'medium'): void
    {
        $messagingData = new MessagingData();
        $messagingData->channel = MessageChannel::sms;
        $messagingData->message_status = MessageStatus::queued;
        $messagingData->message_priority = MessagePriority::getValueFromType($message_priority);

        $messages = $this->getMessageRepository->getMessage($messagingData, 0, 100);
        if ($messages->rowCount()) {
            $text_messages = [];
            $text_message_id = [];

            foreach ($messages as $message) {
                $text_message_id[] = [
                    'messageId' => $message['message_id'],
                    'recipientId' => $message['recipient_id']
                ];
                $text_messages[] = [
                    'destination' => $message['recipient_address'],
                    'content' => Utilities::decodeUTF8($message['message'])
                ];
            }

            $service_provider_id = SendTextMessageService::sendTextMessage($text_messages);
            foreach ($text_message_id as $item) {
                $messagingData->sp_message_id = $service_provider_id;
                $messagingData->message_id = $item['messageId'];
                if ($this->addServiceProviderMessageIdRepository->addMessageId($messagingData)) {
                    $messagingData->message_status = MessageStatus::sent;
                    $messagingData->recipient_id = $item['recipientId'];
                    $this->updateMessageStatusRepository->updateMessageStatus($messagingData);
                }
            }

            if (count($text_messages) > 0) {
                Logger::addToLog(
                    'text_message.log',
                    sprintf(
                        '%s %s priority text message(s) sent successfully.',
                        count($text_messages),
                        $message_priority
                    )
                );
            }
        }
    }
}
