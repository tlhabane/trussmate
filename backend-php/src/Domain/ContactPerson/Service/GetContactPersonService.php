<?php

namespace App\Domain\ContactPerson\Service;

use App\Domain\ContactPerson\Repository\GetContactRepository;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetContactPersonService
{
    private GetContactRepository $getContactRepository;

    public function __construct(PDO $connection)
    {
        $this->getContactRepository = new GetContactRepository($connection);
    }

    public function getContact(array $data): array
    {
        $sanitizedData = SanitizeContactPersonDataService::sanitizeData($data);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );
        $contactData = MapContactPersonDataService::map($sanitizedData);
        $contactData->account_no = $data['accountNo'];

        $records = [];
        $contacts = $this->getContactRepository->getContact(
            $contactData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );

        foreach ($contacts as $contact) {
            $records[] = [
                'customerId' => $contact['customer_id'],
                'contactId' => $contact['contact_id'],
                'firstName' => Utilities::decodeUTF8($contact['first_name']),
                'lastName' => Utilities::decodeUTF8($contact['last_name']),
                'jobTitle' => Utilities::decodeUTF8($contact['job_title']),
                'tel' => $contact['tel'],
                'altTel' => $contact['alt_tel'],
                'email' => $contact['email'],
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getContactRepository->getContact($contactData);
            $pagination = DataPagination::getPagingLinks(
                $sanitizedData['page'],
                $countRecords->rowCount(),
                $paginationConfig['recordsPerPage']
            );

            return ['records' => $records, 'pagination' => $pagination];
        }

        return ['records' => $records];
    }
}
