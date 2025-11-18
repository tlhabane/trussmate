<?php

namespace App\Domain\Transaction\Service;

use App\Domain\Account\Service\GetAccountInfoService;
use App\Domain\Messaging\Service\QueueEmail\QueueMessageService;
use App\Domain\Messaging\Data\MessageType;
use App\Domain\Messaging\Data\MessagePriority;
use Psr\Container\ContainerExceptionInterface;
use App\Responder\GetEmailTemplateResponse;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;
use PDO;

final class SendTransactionStatementService
{
    private CreateTransactionStatementService $createTransactionStatementService;
    private GetTransactionService $getTransactionService;
    private GetAccountInfoService $getAccountInfoService;
    private QueueMessageService $queueMessageService;

    public function __construct(PDO $connection)
    {
        $this->createTransactionStatementService = new CreateTransactionStatementService($connection);
        $this->getTransactionService = new GetTransactionService($connection);
        $this->getAccountInfoService = new GetAccountInfoService($connection);
        $this->queueMessageService = new QueueMessageService($connection);
    }

    private function formatAmount(float $amount): string
    {
        $formattedAmount = number_format(abs($amount), 2);
        return $amount < 0 ? "({$formattedAmount})" : $formattedAmount;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws RuntimeException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function sendStatement(array $data): array
    {
        $transactions = $this->getTransactionService->getTransaction($data);
        $recipientName = '';
        $recipientEmail = '';
        $sanitizedData = SanitizeTransactionDataService::sanitizeData($data);
        foreach ($transactions['records'] as $transaction) {
            if ($sanitizedData['customerId'] === $transaction['customerId']) {
                $recipientName = empty($transaction['customerName'])
                    ? "{$transaction['firstName']} {$transaction['lastName']}"
                    : $transaction['customerName'];
                $recipientEmail = empty($transaction['customerEmail'])
                    ? $transaction['contactEmail']
                    : $transaction['customerEmail'];
            }
        }
        if (empty($recipientEmail)) {
            throw new ValidationException('No email address found for the selected customer.');
        }

        $current_balance = 0;
        $recent_transaction_date = date('Y/m/d');

        if (!empty($sanitizedData['startDate'])) {
            $endDate = date_create($sanitizedData['startDate'])->modify('-1 day')->format('Y-m-d');
            $startDate = "2025-01-01";
            $transactionData = array_merge($sanitizedData, ['startDate' => $startDate, 'endDate' => $endDate]);
            $openingBalanceData = $this->getTransactionService->getTransaction($transactionData);
            foreach ($openingBalanceData['records'] as $transaction) {
                if ($sanitizedData['customerId'] === $transaction['customerId']) {
                    $recent_transaction_date = date('Y/m/d', strtotime($transaction['transactionDate']));
                    $current_balance += $transaction['transactionAmount'];
                }
            }
            foreach ($transactions['records'] as $transaction) {
                if ($sanitizedData['customerId'] === $transaction['customerId']) {
                    $current_balance += $transaction['transactionAmount'];
                    $recent_transaction_date = date('Y/m/d', strtotime($transaction['transactionDate']));
                }
            }
        }

        /* Get statement document */
        $document = $this->createTransactionStatementService->createStatement($data, 'attachments');
        $emailAttachments[] = [
            'fileName' => $document['filename'],
            'fileSource' => $document['fileSource']
        ];
        $emailServiceData = [];

        $accounts = $this->getAccountInfoService->getAccount($data['accountNo']);

        foreach ($accounts['records'] as $account) {
            $senderName = $account['tradingName'] ?? $account['registeredName'];

            $custom_greeting = "<table style='width:100%; margin: 15px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
            $custom_greeting .= "<tr>";
            $custom_greeting .= "<td style='text-align: center; background-color: #e6e7e8; padding: 45px; 15px; color: #414042; font-size: 13px;' class='inner contents'>";
            $custom_greeting .= "<p>Hi {$recipientName}, thank you for choosing {$senderName}.<br />";
            $custom_greeting .= "Attached you will find your transaction record:</p>";
            $custom_greeting .= "</td>";
            $custom_greeting .= "</tr>";
            $custom_greeting .= "</table>";

            $custom_greeting .= "<table style='width:100%; margin: 15px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
            $custom_greeting .= '<thead>';
            $custom_greeting .= '<tr>';
            $custom_greeting .= '<th colspan="2" style="border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; text-align: left;">Summary</th>';
            $custom_greeting .= '</tr>';
            $custom_greeting .= '</thead>';
            $custom_greeting .= '<tbody>';
            $custom_greeting .= "<tr>";
            $custom_greeting .= "<td style='padding: 5px; vertical-align: middle;' class='inner contents'>";
            $custom_greeting .= "Balance as at ${recent_transaction_date}";
            $custom_greeting .= "</td>";
            $custom_greeting .= "<td style='padding: 5px; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
            $custom_greeting .= $this->formatAmount($current_balance);
            $custom_greeting .= "</td>";
            $custom_greeting .= "</tr>";
            $custom_greeting .= "<tr>";
            $custom_greeting .= "<td colspan='2' style='padding: 5px; border-top: 0.1mm solid #efefef;' class='inner contents'>&nbsp;</td>";
            $custom_greeting .= "</tr>";
            $custom_greeting .= '<tbody>';
            $custom_greeting .= "</table>";

            $statement_period = '';
            if (!empty($sanitizedData['startDate']) && !empty($sanitizedData['endDate'])) {
                $statement_period = date('Y/m/d', strtotime($sanitizedData['startDate'])) . ' to ' .
                    date('Y/m/d', strtotime($sanitizedData['endDate']));
            } elseif (!empty($sanitizedData['startDate']) && empty($sanitizedData['endDate'])) {
                $statement_period = date('Y/m/d', strtotime($sanitizedData['startDate']));
            } elseif (empty($sanitizedData['startDate']) && !empty($sanitizedData['endDate'])) {
                $statement_period = date('Y/m/d', strtotime($sanitizedData['endDate']));
            }

            $payload = [
                'app_name' => APP_NAME ?? '',
                'app_tel' => APP_TEL ?? '',
                'app_email' => APP_EMAIL ?? '',
                'app_support_email' => APP_SUPPORT_EMAIL ?? '',
                'app_url' => APP_LINK,
                'app_address' => APP_ADDRESS,
                'recipient_name' => $recipientName,
                'recipient_email' => $recipientEmail,
                'statement_period' => $statement_period,
                'custom_greeting' => $custom_greeting,
                'service_provider_logo' => $account['logo'],
                'service_provider_name' => $senderName,
                'service_provider_tel' => $account['tel'] ?? '',
                'service_provider_email' => $account['email'] ?? '',
                'service_provider_website' => $account['web'] ?? ''
            ];

            $emailMessage = GetEmailTemplateResponse::invoke('statement.twig', $payload);
            if ($emailMessage) {
                $emailServiceData = [
                    'subject' => 'Re: ' . $senderName . ' - Account Statement',
                    'senderName' => $senderName,
                    'senderEmail' => $account['email'],
                    'recipientName' => $recipientName,
                    'recipientAddress' => $recipientEmail,
                    'message' => $emailMessage,
                    'userId' => $data['sessionUsername'],
                    'accountNo' => $data['accountNo'],
                    'recordId' => $sanitizedData['customerId']
                ];
            }
        }

        if (count($emailServiceData) > 0) {
            $emailServiceData['attachments'] = $emailAttachments;
            $emailServiceData['messageType'] = MessageType::getValueFromType('account_statement')->value;
            $emailServiceData['messagePriority'] = MessagePriority::getValueFromType('high')->value;
            return $this->queueMessageService->queueMessage($emailServiceData);
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
