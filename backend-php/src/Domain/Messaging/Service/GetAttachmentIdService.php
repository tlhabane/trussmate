<?php

namespace App\Domain\Messaging\Service;

use App\Domain\Messaging\Repository\AttachmentIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetAttachmentIdService
{
    private AttachmentIdExistsRepository $attachmentIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->attachmentIdExistsRepository = new AttachmentIdExistsRepository($connection);
    }

    public function getId(): string
    {
        do {
            $attachment_id = Utilities::generateToken(64);
        } while (empty($attachment_id) || $this->attachmentIdExistsRepository->attachmentIdExists($attachment_id));

        return $attachment_id;
    }
}
