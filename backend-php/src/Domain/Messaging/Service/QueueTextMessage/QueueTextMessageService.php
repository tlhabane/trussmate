<?php

namespace App\Domain\Messaging\Service\QueueTextMessage;

use App\Domain\Messaging\Service\SanitizeMessagingDataService;
use App\Domain\Messaging\Data\MessagingData;
use App\Domain\Messaging\Service\GetMessageIdService;
use App\Domain\Messaging\Service\GetRecipientIdService;
use App\Domain\Messaging\Repository\AddMessageRepository;
use App\Domain\Messaging\Repository\AddRecipientRepository;
use App\Domain\Messaging\Repository\DeleteMessageRepository;
use App\Domain\Messaging\Repository\GetSimilarQueuedOrSentMessageRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use Exception;
use PDO;

final class QueueTextMessageService
{
    private GetMessageIdService $getMessageIdService;
    private GetRecipientIdService $getRecipientIdService;
    private AddMessageRepository $addMessageRepository;
    private AddRecipientRepository $addRecipientRepository;
    private DeleteMessageRepository $deleteMessageRepository;
    private GetSimilarQueuedOrSentMessageRepository $getSimilarQueuedOrSentMessageRepository;

    public function __construct(PDO $connection)
    {
        $this->getMessageIdService = new GetMessageIdService($connection);
        $this->getRecipientIdService = new GetRecipientIdService($connection);
        $this->addMessageRepository = new AddMessageRepository($connection);
        $this->addRecipientRepository = new AddRecipientRepository($connection);
        $this->deleteMessageRepository = new DeleteMessageRepository($connection);
        $this->getSimilarQueuedOrSentMessageRepository = new GetSimilarQueuedOrSentMessageRepository($connection);
    }

    /**
     * Queue text messages in the outbox for sending.
     *
     * @param array data - Text message properties.
     * @param array data[].recipients - Email recipient array containing a {string} recipientName & {string} recipientAddress properties.
     * @param string data[].subject - Text message subject
     * @param string data[].message - Text message body
     * @return array
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function queueMessage(array $data): array
    {
        $sanitizedData = SanitizeMessagingDataService::sanitizedData($data);
        $validatedData = ValidateQueueTextMessageDataService::validateData($sanitizedData);

        $messagingData = new MessagingData();
        $messagingData->account_no = $data['accountNo'];
        $messagingData->user_id = $data['userId'];
        $messagingData->record_id = $validatedData['recordId'];
        $messagingData->channel = $validatedData['channel'];
        $messagingData->subject = $validatedData['subject'];
        $messagingData->message = $validatedData['message'];
        $messagingData->message_priority = $validatedData['messagePriority'];

        $messages = [];
        try {
            foreach ($validatedData['recipients'] as $recipient) {
                $messagingData->message_id = $this->getMessageIdService->getId();
                $messagingData->recipient_id = $this->getRecipientIdService->getId();
                $messagingData->recipient_name = $recipient['recipientName'];
                $messagingData->recipient_address = $recipient['recipientAddress'];

                $queued_messages = $this->getSimilarQueuedOrSentMessageRepository->getMessage($messagingData);
                if ($queued_messages->rowCount() > 0) {
                    foreach ($queued_messages as $queued_message) {
                        $messages[] = [
                            'messageId' => $queued_message['message_id'],
                            'recipientAddress' => $recipient['recipientAddress']
                        ];
                    }
                    continue;
                }

                if ($this->addMessageRepository->addMessage($messagingData)) {
                    if ($this->addRecipientRepository->addRecipient($messagingData)) {
                        $messages[] = [
                            'messageId' => $messagingData->message_id,
                            'recipientAddress' => $recipient['recipientAddress']
                        ];    
                    } else {
                        $this->deleteMessageRepository->deleteMessage($messagingData->message_id);
                    }
                }
            }
        } catch (Exception $exception) {
            foreach ($messages as $message) {
                $this->deleteMessageRepository->deleteMessage($message['messageId']);
            }

            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }

        return [
            'success' => sprintf('%s text message(s) sent.', count($messages)),
            'id' => $messages
        ];
    }
}
