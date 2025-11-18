<?php

namespace App\Domain\SaleDocument\Service;

use App\Domain\SaleDocument\Repository\GetSaleDocumentRepository;
use App\Util\PathConverter;
use App\Util\Utilities;
use PDO;

final class GetSaleDocumentService
{
    private GetSaleDocumentRepository $getSaleDocumentRepository;

    public function __construct(PDO $connection)
    {
        $this->getSaleDocumentRepository = new GetSaleDocumentRepository($connection);
    }

    public function getSaleDocument(array $data): array
    {
        $sanitizedData = SanitizeSaleDocumentDataService::sanitizeData($data);
        $documentData = SetSaleDocumentDataService::set($sanitizedData);
        $documentData->account_no = $data['accountNo'];

        $records = [];
        $path = new PathConverter();
        $documents = $this->getSaleDocumentRepository->getDocument($documentData);
        foreach ($documents as $document) {
            $records[] = [
                'docId' => $document['doc_id'],
                'docType' => $document['doc_type'],
                'docSrc' => $path->getFileUrl($document['doc_src']),
                'docName' => Utilities::decodeUTF8($document['doc_name']),
                'firstName' => Utilities::decodeUTF8($document['first_name']),
                'lastName' => Utilities::decodeUTF8($document['last_name']),
                'docDate' => date_format(date_create($document['created']), 'Y/m/d')
            ];
        }

        return ['records' => $records];
    }
}
