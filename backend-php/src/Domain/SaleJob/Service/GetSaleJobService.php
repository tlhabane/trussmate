<?php

namespace App\Domain\SaleJob\Service;

use App\Domain\SaleJob\Repository\GetSaleJobRepository;
use App\Util\Utilities;
use PDO;

final class GetSaleJobService
{
    private GetSaleJobRepository $getSaleJobRepository;

    public function __construct(PDO $connection)
    {
        $this->getSaleJobRepository = new GetSaleJobRepository($connection);
    }

    public function getSaleJob(array $data): array
    {
        $sanitizedData = SanitizeSaleJobDataService::sanitizeData($data);

        $records = [];
        $jobs = $this->getSaleJobRepository->getSaleJob($sanitizedData['saleId']);
        foreach ($jobs as $job) {
            $records[] = [
                'jobNo' => $job['job_no'],
                'jobDescription' => Utilities::decodeUTF8($job['job_description']),
                'designInfo' => json_decode($job['design_info'], true),
                'lineItems' => json_decode($job['line_items'], true),
                'subtotal' => floatval($job['subtotal']),
                'vat' => floatval($job['vat']),
                'total' => floatval($job['total'])
            ];
        }

        return ['records' => $records];
    }
}
