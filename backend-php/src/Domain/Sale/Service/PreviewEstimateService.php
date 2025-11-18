<?php

namespace App\Domain\Sale\Service;

use App\Domain\SaleJob\Service\SanitizeSaleJobDataService;
use App\Domain\SaleJob\Service\ValidateSaleJobDataService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Exception\ValidationException;
use App\Util\Utilities;
use Exception;
use PDO;

final class PreviewEstimateService
{
    private GetSaleService $getSaleService;
    private CreateSaleQuotationDocumentService $createSaleQuotationDocumentService;

    public function __construct(PDO $connection)
    {
        $this->getSaleService = new GetSaleService($connection);
        $this->createSaleQuotationDocumentService = new CreateSaleQuotationDocumentService($connection);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws Exception
     */
    public function preview(array $data, string $directory = 'downloads'): array
    {
        if (isset($data['job'])) {
            $job = json_decode($data['job'], true);
            $jobData = [
                'accountNo' => $data['accountNo'],
                'sessionUsername' => $data['sessionUsername'],
                'sessionUserRole' => $data['sessionUserRole'],
                'saleId' => $data['saleId'],
                'jobNo' => $job['jobNo'] ?? '',
                'jobDescription' => $job['jobDescription'] ?? '',
                'designInfo' => $job['designInfo'] ?? [],
                'lineItems' => $job['lineItems'] ?? [],
                'subtotal' => $job['subtotal'] ?? 0,
                'vat' => $job['vat'] ?? 0,
                'total' => $job['total'] ?? 0
            ];
            $sanitizedJobData = SanitizeSaleJobDataService::sanitizeData($jobData);
            $validatedJobData = ValidateSaleJobDataService::validateData($sanitizedJobData);
            $sales = $this->getSaleService->getSale($data);

            foreach ($sales['records'] as $sale) {
                $sale['jobs'] = [];
                $validatedJobData['jobDescription'] = Utilities::decodeUTF8($validatedJobData['jobDescription']);
                $validatedJobData['designInfo'] = json_decode($validatedJobData['designInfo'], true);
                $validatedJobData['lineItems'] = json_decode($validatedJobData['lineItems'], true);
                $sale['jobs'][] = $validatedJobData;
                $sale['saleTotal'] = $sale['saleTotal'] + $validatedJobData['total'];

                return $this->createSaleQuotationDocumentService->getDocument($data, $sale, $directory);
            }
        }

        throw new ValidationException('Invalid or missing sale details');
    }
}
