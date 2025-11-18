<?php

namespace App\Domain\PDFTemplate\Service\Default;

// use App\Domain\Settings\AfterHours\Service\GetAfterHoursContactService;
use App\Exception\ValidationException;
use PDO;

final class GetPDFHeaderService
{
    // private GetAfterHoursContactService $getAfterHoursContactService;

    public function __construct(PDO $connection)
    {
        // $this->getAfterHoursContactService = new GetAfterHoursContactService($connection);
    }

    public function getHeaderAndFooter(array $documentProperties): array
    {
        $headerAndFooter = [];
        foreach ($documentProperties['accounts'] as $account) {
            // $afterHoursContact = $this->getAfterHoursContactService->getContact($account['accountNo']);
            $headerProperties = [
                'accounts' => $documentProperties['accounts'],
                'afterHoursContact' => $afterHoursContact['records'] ?? [],
                'documentTitle' => $documentProperties['documentTitle'],
                'documentRef' => $documentProperties['salesRef'] ?? '',
                'documentDate' => date('Y/m/d', strtotime($documentProperties['salesDate'])),
                'headerFields' => $documentProperties['additionalFields'] ?? []
            ];
            $headerAndFooter['header'] = $this->getHeader($headerProperties);
            $headerAndFooter['footer'] = $this->getFooter($documentProperties['accounts']);
        }

        return $headerAndFooter;
    }

    private function getHeader(array $headerProperties): string
    {

        $htmlPDF = '';

        foreach ($headerProperties['accounts'] as $account) {
            $companyName = $account['tradingName'] ?? $account['registeredName'];
            $htmlPDF .= "<table style='width:100%; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
            $htmlPDF .= '<tr>';
            $htmlPDF .= '<td style="padding: 5px 0; vertical-align: middle; border-bottom: 0.1mm solid #efefef;">';
            $htmlPDF .= '<p style="font-size:18px; font-weight: bold; line-height:22px;">';
            $htmlPDF .= $headerProperties['documentTitle'];
            $htmlPDF .= '</p>';
            $htmlPDF .= '</td>';
            $htmlPDF .= '<td style="padding: 5px 0; text-align: right; vertical-align: middle; border-bottom: 0.1mm solid #efefef;">';
            if (isset($headerProperties['documentRef']) && !empty($headerProperties['documentRef'])) {
                $htmlPDF .= "Ref #: <span style='text-transform: uppercase'>{$headerProperties['documentRef']}</span><br />";
            }
            $htmlPDF .= 'Date: ' . $headerProperties['documentDate'];
            if (count($headerProperties['headerFields']) > 0) {
                foreach ($headerProperties['headerFields'] as $fieldTitle => $headerField) {
                    if (!empty($headerField)) {
                        $htmlPDF .= '<br />';
                        $htmlPDF .= "{$fieldTitle}: {$headerField}";
                    }
                }
            }
            $htmlPDF .= '</td>';
            $htmlPDF .= '<tr>';
            $htmlPDF .= '</table>';
            $htmlPDF .= "<table style='width:100%; margin: 10px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
            $htmlPDF .= '<tr>';
            // company logo
            $htmlPDF .= '<td style="text-align: left; vertical-align: middle; width: 65%;">';
            $website_url = APP_LINK;
            if (!empty(trim($account['web']))) {
                $website_url = $account['web'];
            }
            $website = preg_replace("^[a-zA-Z0-9]+://^", "//", $website_url);
            $htmlPDF .= "<a href='{$website}' target='_blank' alt='{$companyName}'>";
            $htmlPDF .= "<img src='{$account['logo']}' width='150'>";
            $htmlPDF .= "</a>";
            $htmlPDF .= '</td>';

            $htmlPDF .= '<td style="text-align: right; vertical-align: top; padding: 0; width: 35%">';
            $htmlPDF .= $account['address'] . '<br />';
            $htmlPDF .= 'Tel: ' . $account['tel'];
            if (!empty($account['altTel'])) {
                $htmlPDF .= ' | ' . $account['altTel'];
            }
            $htmlPDF .= '<br />';
            $htmlPDF .= 'Email: ' . $account['email'] . '<br />';
            if (!empty($account['web'])) {
                $htmlPDF .= $account['web'] . '<br />';
            }

            foreach ($headerProperties['afterHoursContact'] as $afterHoursContact) {
                $htmlPDF .= '<p style="font-weight: bold;">After Hours</p>';
                $htmlPDF .= 'Tel: ' . $afterHoursContact['tel'];
                if (!empty($afterHoursContact['altTel'])) {
                    $htmlPDF .= ' | ' . $afterHoursContact['altTel'];
                }
                if (!empty($afterHoursContact['email'])) {
                    $htmlPDF .= '<br />';
                    $htmlPDF .= 'Email: ' . $afterHoursContact['email'];
                }
            }

            $htmlPDF .= '</td>';
            $htmlPDF .= '</tr>';
            $htmlPDF .= '</table>';
        }

        return $htmlPDF;
    }

    private function getFooter(array $accounts): string
    {
        $footerLinkStyle = 'style="color: #555555; text-decoration: none;"';
        $footer = "<table style='width:100%; border-spacing: 3px; border-collapse: separate; font-size: 10px; margin-top: 15px; color: #555555;'>";
        /*$footer .= "<tr>";
        $footer .= "<td style='padding: 5px 0; text-align: right;'>Page {PAGENO} of {nbpg}</td>";
        $footer .= "</tr>";*/
        $footer .= "<tr>";
        $footer .= "<td style='padding: 10px 0; border-top: 0.1mm solid #efefef; text-align: left'>";
        foreach ($accounts as $account) {
            $footer .= "<a {$footerLinkStyle} href='//{$account['web']}'>";
            $footer .= $account['tradingName'] ?? $account['registeredName'];
            $footer .= "</a>";
            $footer .= " | ";
        }
        $footer .= "Powered by <a {$footerLinkStyle} href='//" . APP_LINK . "'>" . APP_NAME . "</a>";
        $footer .= "</td>";
        $footer .= "<td style='padding: 10px 0; border-top: 0.1mm solid #efefef; text-align: right;'>Page {PAGENO} of {nbpg}</td>";
        $footer .= "</tr>";
        $footer .= "</table>";

        return $footer;
    }
}
