<?php

namespace App\Domain\PDFTemplate\Service\Default;

class GetPDFSummarySalesItemsService
{
    public function getSaleItems(array $saleItems, string $additionalTerms = ''): string
    {
        $pdf_document = "";
        $bottom_thin = "0.1mm solid #efefef";
        $bottom_thick = "0.5mm solid #efefef";
        $bottom_thicker = "0.75mm solid #efefef";
        foreach ($saleItems as $index => $item) {
            $pdf_document .= "<table style='width:100%; margin: 15px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555; page-break-inside: avoid;'>";
            $pdf_document .= "<thead>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<th style='text-align: left; padding: 5px; border-bottom: {$bottom_thin}; width: 15%'>";
            $pdf_document .= "Qty";
            $pdf_document .= "</th>";
            $pdf_document .= "<th style='text-align: left; padding: 5px; border-bottom: {$bottom_thin};'>";
            $pdf_document .= "Description";
            $pdf_document .= "</th>";
            $pdf_document .= "<th style='text-align: right; padding: 5px; border-bottom: {$bottom_thin}; width: 15%'>";
            $pdf_document .= "Unit Price";
            $pdf_document .= "</th>";
            $pdf_document .= "<th style='text-align: right; padding: 5px; border-bottom: {$bottom_thin}; width: 15%'>";
            $pdf_document .= "Item Total";
            $pdf_document .= "</th>";
            $pdf_document .= "</tr>";
            $pdf_document .= "</thead>";
            $pdf_document .= "<tbody>";
            foreach ($item['lineItems'] as $key => $lineItem) {
                $bg_color = $key % 2 === 0 ? "#f3f3f4" : "";
                $pdf_document .= "<tr style='background-color: {$bg_color};'>";
                $pdf_document .= "<td style='text-align: left; padding: 3px 5px;'>1</td>";
                $pdf_document .= "<td style='text-align: left; padding: 3px 5px;'>";
                $pdf_document .= $lineItem['category'];
                $pdf_document .= "</td>";
                $pdf_document .= "<td style='text-align: right; padding: 3px 5px;'>";
                $pdf_document .= number_format($lineItem['amount'], 2);
                $pdf_document .= "</td>";
                $pdf_document .= "<td style='text-align: right; padding: 3px 5px;'>";
                $pdf_document .= number_format($lineItem['amount'], 2);
                $pdf_document .= "</td>";
                $pdf_document .= "</tr>";
            }
            $pdf_document .= "<tfoot>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<td colspan='3' style='border-bottom: {$bottom_thin}; border-top: {$bottom_thin}; text-align: right;'>Subtotal</td>";
            $pdf_document .= "<td style='border-bottom: {$bottom_thin}; border-top: {$bottom_thin}; padding: 10px 0; width: 15%; text-align: right;'>" . number_format($saleItems[$index]['subtotal'], 2) . "</td>";
            $pdf_document .= "</tr>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<td colspan='3' style='padding: 10px 0; text-align: right;'>VAT</td>";
            $pdf_document .= "<td style='padding: 10px 0; width: 15%; text-align: right;'>" . number_format($saleItems[$index]['vat'], 2) . "</td>";
            $pdf_document .= "</tr>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<th colspan='3' style='border-bottom: {$bottom_thin}; border-top: {$bottom_thin}; padding: 10px 0; text-align: right;'>";
            $pdf_document .= 'Total Due';
            $pdf_document .= "</th>";
            $pdf_document .= "<th style='border-bottom: {$bottom_thicker}; border-top: {$bottom_thicker}; padding: 10px 0; width: 15%; text-align: right;'>";
            $pdf_document .= number_format($saleItems[$index]['total'], 2);
            $pdf_document .= "</th>";
            $pdf_document .= "</tr>";
            $pdf_document .= "</tfoot>";
            $pdf_document .= "</table>";
        }

        return $pdf_document;
    }
}
