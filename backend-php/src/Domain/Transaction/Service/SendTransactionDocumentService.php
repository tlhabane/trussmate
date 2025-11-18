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

final class SendTransactionDocumentService
{
    private CreateTransactionDocumentService $createTransactionDocumentService;
    private GetTransactionByIdService $getTransactionByIdService;
    private GetAccountInfoService $getAccountInfoService;
    private QueueMessageService $queueMessageService;

    public function __construct(PDO $connection)
    {
        $this->createTransactionDocumentService = new CreateTransactionDocumentService($connection);
        $this->getTransactionByIdService = new GetTransactionByIdService($connection);
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
    public function sendTransaction(array $data): array
    {
        $transaction = $this->getTransactionByIdService->getTransaction($data);
        if (empty($transaction)) {
            throw new ValidationException('Invalid or mission Transaction detail');
        }

        $document = $this->createTransactionDocumentService->createDocument($data, 'attachments');
        $emailAttachments[] = [
            'fileName' => $document['filename'],
            'fileSource' => $document['fileSource']
        ];
        $emailServiceData = [];

        $accounts = $this->getAccountInfoService->getAccount($data['accountNo']);

        foreach ($accounts['records'] as $account) {
            $senderName = $account['tradingName'] ?? $account['registeredName'];
            $recipientName = $transaction['customerName'];
            $recipientEmail = $transaction['customerEmail'];
            $recipientName = empty($transaction['firstName'])
                ? $recipientName
                : "{$transaction['firstName']} {$transaction['lastName']}";
            $recipientEmail = empty($transaction['contactEmail'])
                ? $recipientEmail
                : $transaction['contactEmail'];

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
            $custom_greeting .= "<td style='padding: 5px; vertical-align: middle;' class='inner contents'>Invoice Ref. No.</td>";
            $custom_greeting .= "<td style='padding: 5px; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
            $custom_greeting .= $transaction['invoiceNo'];
            $custom_greeting .= "</td>";
            $custom_greeting .= "</tr>";
            $custom_greeting .= "<tr>";
            $custom_greeting .= "<td style='padding: 5px; background-color: #f9f9f9; vertical-align: middle;' class='inner contents'>Transaction Date</td>";
            $custom_greeting .= "<td style='padding: 5px; background-color: #f9f9f9; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
            $custom_greeting .= date('Y/m/d', strtotime($transaction['transactionDate']));
            $custom_greeting .= "</td>";
            $custom_greeting .= "</tr>";
            $custom_greeting .= "<tr>";
            $custom_greeting .= "<td style='padding: 5px; vertical-align: middle;' class='inner contents'>Transaction Amount</td>";
            $custom_greeting .= "<td style='padding: 5px; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
            $custom_greeting .= $this->formatAmount($transaction['transactionAmount']);
            $custom_greeting .= "</td>";
            $custom_greeting .= "</tr>";
            $custom_greeting .= "<tr>";
            $custom_greeting .= "<td colspan='2' style='padding: 5px; border-top: 0.1mm solid #efefef;' class='inner contents'>&nbsp;</td>";
            $custom_greeting .= "</tr>";
            $custom_greeting .= '<tbody>';
            $custom_greeting .= "</table>";

            $payload = [
                'app_name' => APP_NAME ?? '',
                'app_tel' => APP_TEL ?? '',
                'app_email' => APP_EMAIL ?? '',
                'app_support_email' => APP_SUPPORT_EMAIL ?? '',
                'app_url' => APP_LINK,
                'app_address' => APP_ADDRESS,
                'recipient_name' => $recipientName,
                'recipient_email' => $recipientEmail,
                'email_title' => "Transaction Record",
                'invoice_number' => $transaction['invoiceNo'],
                'custom_greeting' => $custom_greeting,
                'service_provider_logo' => $account['logo'],
                'service_provider_name' => $senderName,
                'service_provider_tel' => $account['tel'] ?? '',
                'service_provider_email' => $account['email'] ?? '',
                'service_provider_website' => $account['web'] ?? ''
            ];

            $emailMessage = GetEmailTemplateResponse::invoke('invoice.twig', $payload);
            if ($emailMessage) {
                $emailServiceData = [
                    'subject' => 'Re: ' . $transaction['transactionDesc'],
                    'senderName' => $senderName,
                    'senderEmail' => $account['email'],
                    'recipientName' => $recipientName,
                    'recipientAddress' => $recipientEmail,
                    'message' => $emailMessage,
                    'userId' => $data['sessionUsername'],
                    'accountNo' => $data['accountNo'],
                    'recordId' => $transaction['saleId']
                ];
            }
        }

        if (count($emailServiceData) > 0) {
            $emailServiceData['attachments'] = $emailAttachments;
            $emailServiceData['messageType'] = MessageType::getValueFromType('invoice')->value;
            $emailServiceData['messagePriority'] = MessagePriority::getValueFromType('high')->value;
            return $this->queueMessageService->queueMessage($emailServiceData);
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
