<?php

namespace App\Domain\Invoice\Service;

use App\Domain\Invoice\Repository\GetInvoiceByTaskIdRepository;
use App\Domain\Invoice\Repository\AddInvoiceRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use App\Util\Utilities;
use Exception;
use PDO;

final class AddInvoiceService
{
    private GetInvoiceByTaskIdRepository $getInvoiceByTaskIdRepository;
    private AddInvoiceRepository $addInvoiceRepository;

    public function __construct(PDO $connection)
    {
        $this->getInvoiceByTaskIdRepository = new GetInvoiceByTaskIdRepository($connection);
        $this->addInvoiceRepository = new AddInvoiceRepository($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addInvoice(array $data): array
    {
        $sanitizedData = SanitizeInvoiceDataService::sanitizedData($data);
        $validatedData = ValidateAddInvoiceDataService::validateData($sanitizedData);
        $invoiceData = SetInvoiceDataService::set($validatedData);

        try {
            $invoices = $this->getInvoiceByTaskIdRepository->getInvoice($invoiceData->sale_task_id);
            if ($invoices->rowCount() > 0) {
                foreach ($invoices as $invoice) {
                    if (empty($invoice['invoice_no'])) {
                        continue;
                    }
                    return [
                        'success' => sprintf(
                            'Invoice #%s already exists',
                            Utilities::addPadding($invoice['invoice_no'], 0)
                        ),
                        'id' => $invoice['invoice_no'],
                        'invoice' => $invoice
                    ];
                }
            }
            $invoiceNo = $this->addInvoiceRepository->addInvoice($invoiceData);
            return [
                'success' => sprintf('Invoice #%s created', Utilities::addPadding($invoiceNo, 0)),
                'id' => $invoiceNo
            ];
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }
    }
}
