<?php

namespace App\Domain\Sale\Service\DeleteSale;

use App\Domain\Sale\Data\SaleStatus;
use App\Domain\Sale\Service\GetSaleStatusService;
use App\Domain\Sale\Service\GetSaleService;
use App\Domain\Invoice\Service\GetInvoiceService;
use App\Exception\ValidationException;
use PDO;

final class ValidateDeleteSaleDataService
{
    private GetSaleService $getSaleService;
    private GetInvoiceService $getInvoiceService;

    public function __construct(PDO $connection)
    {
        $this->getSaleService = new GetSaleService($connection);
        $this->getInvoiceService = new GetInvoiceService($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        $service_data = [
            'accountNo' => $data['accountNo'],
            'sessionUsername' => $data['sessionUsername'],
            'sessionUserRole' => $data['sessionUserRole'],
            'saleId' => $data['saleId']
        ];

        $sales = $this->getSaleService->getSale($service_data);

        if (empty($data['saleId']) || count($sales['records']) !== 1) {
            throw new ValidationException('Invalid or missing sale details', 422, $sales['records']);
        }

        foreach ($sales['records'] as $sale) {
            $sale_status = GetSaleStatusService::getStatus($sale['saleStatus']);
            if ($sale_status === SaleStatus::COMPLETED || $sale_status === SaleStatus::STARTED) {
                throw new ValidationException('Sale is already in progress or has been completed');
            }

            $invoices = $this->getInvoiceService->getInvoice($service_data);

            $payment_total = 0;
            $invoice_balance = 0;
            foreach ($invoices['records'] as $invoice) {
                $payment_total += $invoice['paymentTotal'];
                $invoice_balance += $invoice['invoiceBalance'];
            }

            if (($sale['saleTotal'] > 0 && ($sale['saleTotal'] - $invoice_balance) <= 0.01) || abs($payment_total) >= 0.01) {
                throw new ValidationException('Sales already paid for cannot be deleted');
            }
        }

        return $data;
    }
}
