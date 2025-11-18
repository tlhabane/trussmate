<?php

namespace App\Domain\Messaging\Service;

use App\Domain\Messaging\Repository\MessageIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetMessageIdService
{
    private MessageIdExistsRepository $messageIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->messageIdExistsRepository = new MessageIdExistsRepository($connection);
    }

    public function getId(): string
    {
        do {
            $message_id = Utilities::generateToken(64);
        } while (empty($message_id) || $this->messageIdExistsRepository->messageIdExists($message_id));

        return $message_id;
    }
}
