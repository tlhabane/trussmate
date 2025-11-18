<?php

namespace App\Domain\Messaging\Service\QueueEmail;

use App\Domain\Messaging\Service\SanitizeMessagingDataService;
use App\Domain\Messaging\Data\MessagingData;
use App\Domain\Messaging\Service\GetMessageIdService;
use App\Domain\Messaging\Service\GetAttachmentIdService;
use App\Domain\Messaging\Service\GetRecipientIdService;
use App\Domain\Messaging\Repository\AddMessageRepository;
use App\Domain\Messaging\Repository\AddAttachmentRepository;
use App\Domain\Messaging\Repository\AddRecipientRepository;
use App\Domain\Messaging\Repository\DeleteMessageRepository;
use App\Domain\Messaging\Repository\GetSimilarQueuedOrSentMessageRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use Exception;
use PDO;

final class QueueMessageService
{
    private GetMessageIdService $getMessageIdService;
    private GetAttachmentIdService $getAttachmentIdService;
    private GetRecipientIdService $getRecipientIdService;
    private AddMessageRepository $addMessageRepository;
    private AddAttachmentRepository $addAttachmentRepository;
    private AddRecipientRepository $addRecipientRepository;
    private DeleteMessageRepository $deleteMessageRepository;
    private GetSimilarQueuedOrSentMessageRepository $getSimilarQueuedOrSentMessageRepository;

    public function __construct(PDO $connection)
    {
        $this->getMessageIdService = new GetMessageIdService($connection);
        $this->getAttachmentIdService = new GetAttachmentIdService($connection);
        $this->getRecipientIdService = new GetRecipientIdService($connection);
        $this->addMessageRepository = new AddMessageRepository($connection);
        $this->addAttachmentRepository = new AddAttachmentRepository($connection);
        $this->addRecipientRepository = new AddRecipientRepository($connection);
        $this->deleteMessageRepository = new DeleteMessageRepository($connection);
        $this->getSimilarQueuedOrSentMessageRepository = new GetSimilarQueuedOrSentMessageRepository($connection);
    }

    /**
     * Queue email messages in the outbox for sending.
     *
     * @param array data - Email message properties.
     * @param array data[].recipients - Email recipient array containing a {string} recipientName & {string} recipientAddress properties.
     * @param array [data[].attachments] - Email attachment files containing a {string} filename & {string} fileSource properties.
     * @param string data[].subject - Email subject
     * @param string data[].message - Email body in HTML format
     * @return array
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function queueMessage(array $data): array
    {
        $sanitizedData = SanitizeMessagingDataService::sanitizedData($data);
        $validatedData = ValidateQueueEmailDataService::validateData($sanitizedData);

        $messagingData = new MessagingData();
        $messagingData->account_no = $data['accountNo'];
        $messagingData->user_id = $data['userId'];
        $messagingData->record_id = $validatedData['recordId'];
        $messagingData->channel = $validatedData['channel'];
        $messagingData->subject = $validatedData['subject'];
        $messagingData->message = $validatedData['message'];
        $messagingData->message_type = $validatedData['messageType'];
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
                        $messages[] = $queued_message['message_id'];
                    }
                    continue;
                }

                if ($this->addMessageRepository->addMessage($messagingData)) {
                    $messages[] = $messagingData->message_id;
                    foreach ($validatedData['attachments'] as $attachment) {
                        $messagingData->attachment_id = $this->getAttachmentIdService->getId();
                        $messagingData->filename = $attachment['fileName'];
                        $messagingData->file_source = $attachment['fileSource'];

                        $this->addAttachmentRepository->addAttachment($messagingData);
                    }


                    $this->addRecipientRepository->addRecipient($messagingData);
                }
            }
        } catch (Exception $exception) {
            foreach ($messages as $message) {
                $this->deleteMessageRepository->deleteMessage($message);
            }

            throw new RuntimeException($exception->getMessage());
        }

        return [
            'success' => sprintf('%s email message(s) sent.', count($messages)),
            'id' => $messages
        ];
    }
}
