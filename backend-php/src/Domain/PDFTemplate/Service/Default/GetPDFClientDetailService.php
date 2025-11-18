<?php

namespace App\Domain\PDFTemplate\Service\Default;

use App\Domain\ContactPerson\Service\GetContactPersonService;
use App\Domain\Customer\Service\GetCustomerService;
use App\Domain\CustomerAddress\Service\GetCustomerAddressService;
use PDO;

final class GetPDFClientDetailService
{
    private GetCustomerService $getCustomerService;
    private GetContactPersonService $getContactPersonService;
    private GetCustomerAddressService $getCustomerAddressService;

    public function __construct(PDO $connection)
    {
        $this->getCustomerService = new GetCustomerService($connection);
        $this->getContactPersonService = new GetContactPersonService($connection);
        $this->getCustomerAddressService = new GetCustomerAddressService($connection);
    }

    public function getClientDetail(string $accountNo, string $customerId, string $consultantId = ''): string
    {
        $htmlPDF = '';
        $customers = $this->getCustomerService->getCustomer([
            'accountNo' => $accountNo,
            'customerId' => $customerId
        ]);
        $addresses = $this->getCustomerAddressService->getAddress([
            'accountNo' => $accountNo,
            'customerId' => $customerId,
            'billingAddress' => '1'
        ]);

        $customer_address = '';
        foreach ($addresses['records'] as $address) {
            $customer_address = $address['fullAddress'];
        }
        foreach ($customers['records'] as $customer) {
            $companyName = $customer['customerName'];
            $tel = $customer['tel'];
            $altTel = $customer['altTel'];
            $email = $customer['email'];

            $attention = '';
            if (!empty($consultantId)) {
                $consultants = $this->getContactPersonService->getContact([
                    'accountNo' => $accountNo,
                    'customerId' => $customerId,
                    'contactId' => $consultantId
                ]);
                foreach ($consultants['records'] as $consultant) {
                    $tel = empty($consultant['tel']) ? $tel : $consultant['tel'];
                    $altTel = empty($consultant['altTel']) ? $altTel : $consultant['altTel'];
                    $email = empty($consultant['email']) ? $tel : $consultant['email'];
                    $attention = "{$consultant['firstName']} {$consultant['lastName']}";
                }
            }

            $borderTopAndBottom = 'border-top: 0.1mm solid #efefef;border-bottom: 0.1mm solid #efefef;';
            $thStyle = 'width: 10%; text-align: left;';
            $htmlPDF .= "<table style='width:100%;  border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
            $htmlPDF .= "<thead>";
            $htmlPDF .= "<tr>";
            $htmlPDF .= "<th colspan='3' style='{$borderTopAndBottom} padding: 10px 0; text-align: left;'>Due To</th>";
            $htmlPDF .= "</tr>";
            $htmlPDF .= "</thead>";

            $htmlPDF .= "</tbody>";
            $htmlPDF .= "<tr>";
            $htmlPDF .= "<td style='width: 15%;'>Attention</td>";
            $htmlPDF .= "<td style='width: 50%;'>{$attention}</td>";
            $htmlPDF .= "<td rowspan='3' style='text-align: right;'>";
            if (!empty($customer_address)) {
                $htmlPDF .= "{$customer_address}<br />";
            }
            $htmlPDF .= "Tel: {$tel}";
            $htmlPDF .= empty($altTel) ? "" : " | {$altTel}";
            if (!empty($customer['email'])) {
                $htmlPDF .= "<br />Email: {$customer['email']}";
            }
            if (!empty($customer['web'])) {
                $htmlPDF .= "<br />{$customer['web']}";
            }
            $htmlPDF .= "</td>";
            $htmlPDF .= "</tr>";
            $htmlPDF .= "<tr>";
            $htmlPDF .= "<td>Company</td>";
            $htmlPDF .= "<td>{$companyName}</td>";
            $htmlPDF .= "</tr>";
            $htmlPDF .= "<tr>";
            $htmlPDF .= "<td>VAT No.</td>";
            $htmlPDF .= "<td>{$customer['vatNo']}</td>";
            $htmlPDF .= "</tr>";
            $htmlPDF .= "</tbody>";
            $htmlPDF .= "</table>";
        }

        return $htmlPDF;
    }
}
