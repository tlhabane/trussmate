<?php

namespace App\Domain\Transaction\Service;

use App\Domain\Transaction\Data\TransactionType;
use App\Domain\Transaction\Repository\AddTransactionRepository;
use App\Domain\SaleTask\Service\UpdateSaleTaskStatusService;
use App\Domain\Invoice\Service\GetInvoiceByTaskIdService;
use App\Domain\SaleTask\Service\GetSaleTaskService;
use App\Domain\Invoice\Service\AddInvoiceService;
use Psr\Container\ContainerExceptionInterface;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use Twig\Error\RuntimeError;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use App\Util\Utilities;
use PDO;

final class AddTransactionService
{
    private UpdateSaleTaskStatusService $updateSaleTaskStatusService;
    private GetInvoiceByTaskIdService $getInvoiceByTaskIdService;
    private AddTransactionRepository $addTransactionRepository;
    private GetTransactionIdService $getTransactionIdService;
    private GetSaleTaskService $getSaleTaskService;
    private AddInvoiceService $addInvoiceService;

    public function __construct(PDO $connection)
    {
        $this->updateSaleTaskStatusService = new UpdateSaleTaskStatusService($connection);
        $this->getInvoiceByTaskIdService = new GetInvoiceByTaskIdService($connection);
        $this->addTransactionRepository = new AddTransactionRepository($connection);
        $this->getTransactionIdService = new GetTransactionIdService($connection);
        $this->getSaleTaskService = new GetSaleTaskService($connection);
        $this->addInvoiceService = new AddInvoiceService($connection);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws RuntimeException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function addTransaction(array $data): array
    {
        $sanitizedData = SanitizeTransactionDataService::sanitizeData($data);
        $validatedData = ValidateAddTransactionDataService::validateData($sanitizedData);

        $invoiceBalance = 0.0;
        $invoices = $this->getInvoiceByTaskIdService->getInvoice($validatedData);
        foreach ($invoices['records'] as $invoice) {
            // If no invoice exists for the task, create one
            if (empty($invoice['saleId']) || empty($invoice['saleTaskId'])) {
                $tasks = $this->getSaleTaskService->getTask(array_merge($data, [
                    'saleTaskId' => $validatedData['saleTaskId']
                ]));
                foreach ($tasks['records'] as $task) {
                    $this->addInvoiceService->addInvoice(array_merge($data, [
                        'invoiceType' => $task['taskAction'],
                        'saleTaskId' => $task['saleTaskId'],
                    ]));
                    $invoices = $this->getInvoiceByTaskIdService->getInvoice($validatedData);
                    break;
                }

            }
        }
        if (count($invoices['records']) > 0) {
            foreach ($invoices['records'] as $invoice) {
                if (floatval($invoice['invoiceBalance']) <= 0.01) {
                    throw new ValidationException(sprintf(
                        'Transaction amount %s exceeds invoice #%s balance of %s.',
                        number_format($validatedData['transactionAmount'], 2),
                        $invoice['invoiceNo'],
                        number_format($invoice['invoiceBalance'], 2)
                    ));
                }
                $invoiceBalance += floatval($invoice['invoiceBalance']);
                $validatedData['invoiceNo'] = intval($invoice['invoiceNo']);
            }
        }

        $transactionData = SetTransactionDataService::set($validatedData);
        $transactionData->account_no = $data['accountNo'];
        $transactionData->user_id = $data['sessionUsername'];
        $transactionData->transaction_id = $this->getTransactionIdService->getId();
        if ($transactionData->transaction_type !== TransactionType::DEBIT_MEMO) {
            $transactionData->transaction_amount = -1 * $transactionData->transaction_amount;
        };

        if ($this->addTransactionRepository->addTransaction($transactionData)) {
            // If the transaction amount pays off the invoice, mark the task as completed
            if (($transactionData->transaction_amount + $invoiceBalance) < 0.01) {
                $this->updateSaleTaskStatusService->updateStatus(array_merge($data, ['taskStatus' => 'completed']));
            }
            $invoiceNo = Utilities::addPadding($transactionData->invoice_no, 0);
            $amount = number_format(abs($transactionData->transaction_amount), 2);
            return [
                'success' => sprintf('%s paid to invoice #%s', $amount, $invoiceNo),
                'id' => $transactionData->transaction_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
