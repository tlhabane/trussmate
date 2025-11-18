<?php

namespace App\Domain\Sale\Service;

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

final class SendSaleQuotationService
{
    private CreateSaleQuotationService $createSaleQuotationService;
    private GetAccountInfoService $getAccountInfoService;
    private QueueMessageService $queueMessageService;
    private GetSaleService $getSaleService;

    public function __construct(PDO $connection)
    {
        $this->createSaleQuotationService = new CreateSaleQuotationService($connection);
        $this->getAccountInfoService = new GetAccountInfoService($connection);
        $this->queueMessageService = new QueueMessageService($connection);
        $this->getSaleService = new GetSaleService($connection);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws RuntimeException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function sendQuotation(array $data): array
    {
        $quotation = $this->createSaleQuotationService->getQuotation($data, 'attachments');
        $emailAttachments[] = [
            'fileName' => $quotation['filename'],
            'fileSource' => $quotation['fileSource']
        ];
        $emailServiceData = [];

        $accounts = $this->getAccountInfoService->getAccount($data['accountNo']);
        $sales = $this->getSaleService->getSale($data);

        foreach ($sales['records'] as $sale) {
            foreach ($accounts['records'] as $account) {
                $senderName = $account['tradingName'] ?? $account['registeredName'];
                $recipientName = $sale['customer']['customerName'];
                $recipientEmail = $sale['customer']['email'];
                if ($sale['contact'] && count($sale['contact']) > 0) {
                    $recipientName = empty($sale['contact']['firstName'])
                        ? $recipientName
                        : "{$sale['contact']['firstName']} {$sale['contact']['lastName']}";
                    $recipientEmail = empty($sale['contact']['email'])
                        ? $recipientEmail
                        : $sale['contact']['email'];
                }

                $custom_greeting = "<table style='width:100%; margin: 15px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
                $custom_greeting .= "<tr>";
                $custom_greeting .= "<td style='text-align: center; background-color: #e6e7e8; padding: 45px; 15px; color: #414042; font-size: 13px;' class='inner contents'>";
                $custom_greeting .= "<p>Hi {$recipientName}, thank you for choosing {$senderName}.<br />";
                $custom_greeting .= "Attached you will find your quotation:</p>";
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
                $custom_greeting .= "<td style='padding: 5px; vertical-align: middle;' class='inner contents'>Quotation Ref. No.</td>";
                $custom_greeting .= "<td style='padding: 5px; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
                $custom_greeting .= $sale['saleNo'];
                $custom_greeting .= "</td>";
                $custom_greeting .= "</tr>";
                $custom_greeting .= "<tr>";
                $custom_greeting .= "<td style='padding: 5px; background-color: #f9f9f9; vertical-align: middle;' class='inner contents'>Total Amount</td>";
                $custom_greeting .= "<td style='padding: 5px; background-color: #f9f9f9; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
                $custom_greeting .= number_format($sale['saleTotal'], 2);
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
                    'quotation_number' => $sale['saleNo'],
                    'custom_greeting' => $custom_greeting,
                    'service_provider_logo' => $account['logo'],
                    'service_provider_name' => $senderName,
                    'service_provider_tel' => $account['tel'] ?? '',
                    'service_provider_email' => $account['email'] ?? '',
                    'service_provider_website' => $account['web'] ?? ''
                ];

                $emailMessage = GetEmailTemplateResponse::invoke('quotation.twig', $payload);
                if ($emailMessage) {
                    $emailServiceData = [
                        'subject' => 'Re: Quotation #' . $sale['saleNo'],
                        'senderName' => $senderName,
                        'senderEmail' => $account['email'],
                        'recipientName' => $recipientName,
                        'recipientAddress' => $recipientEmail,
                        'message' => $emailMessage,
                        'userId' => $data['sessionUsername'],
                        'accountNo' => $data['accountNo'],
                        'recordId' => $sale['saleId']
                    ];
                }
            }
        }

        if (count($emailServiceData) > 0) {
            $emailServiceData['attachments'] = $emailAttachments;
            $emailServiceData['messageType'] = MessageType::getValueFromType('quotation')->value;
            $emailServiceData['messagePriority'] = MessagePriority::getValueFromType('high')->value;
            return $this->queueMessageService->queueMessage($emailServiceData);
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
