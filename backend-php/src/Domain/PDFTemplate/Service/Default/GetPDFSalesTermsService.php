<?php

namespace App\Domain\PDFTemplate\Service\Default;

use App\Domain\SaleTask\Service\GetSaleTaskListService;
use PDO;

final class GetPDFSalesTermsService
{
    private GetSaleTaskListService $getSaleTaskListService;

    public function __construct(PDO $connection)
    {
        $this->getSaleTaskListService = new GetSaleTaskListService($connection);
    }

    public function getSalesTerms(array $data): string
    {
        $pdf_document = '';
        $salesTerms = $this->getSaleTaskListService->getTask($data);
        if (isset($salesTerms['records']) && count($salesTerms['records']) > 0) {
            $pdf_document .= "<table style='width: 100%; border-spacing: 3px; border-collapse: separate; font-size: 10px; color: #555555; margin: 30px 0; padding-bottom: 10px; border-bottom: 0.1mm solid #efefef; page-break-inside: avoid;'>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<td colspan='2' style='border-top: 0.1mm solid #efefef; border-bottom: 0.1mm solid #efefef; font-weight: bold; padding: 10px 5px; margin-bottom: 10px;'>";
            $pdf_document .= "Terms";
            $pdf_document .= "</td>";
            $pdf_document .= "</tr>";
            $terms = [];
            foreach ($salesTerms['records'] as $salesTerms) {
                $terms_parts = explode("<br />", nl2br($salesTerms['assignmentNote']));
                foreach ($terms_parts as $part) {
                    $terms[] = $part;
                }
            }
            foreach ($terms as $index => $term) {
                $line_no = $index + 1;
                $pdf_document .= "<tr>";
                $pdf_document .= "<td style='padding: 0.005rem 5px 0 5px; vertical-align: top;'>{$line_no}</td>";
                $pdf_document .= "<td style='padding: 0.005rem 5px 0 5px; vertical-align: top;'>{$term}</td>";
                $pdf_document .= "</tr>";
            }
            $pdf_document .= "</table>";
        }

        return $pdf_document;
    }
}
