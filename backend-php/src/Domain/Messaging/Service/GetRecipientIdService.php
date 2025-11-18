<?php

namespace App\Domain\Messaging\Service;

use App\Domain\Messaging\Repository\RecipientIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetRecipientIdService
{
    private RecipientIdExistsRepository $recipientIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->recipientIdExistsRepository = new RecipientIdExistsRepository($connection);
    }

    public function getId(): string
    {
        do {
            $recipient_id = Utilities::generateToken(64);
        } while (empty($recipient_id) || $this->recipientIdExistsRepository->recipientIdExists($recipient_id));

        return $recipient_id;
    }
}
