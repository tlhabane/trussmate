<?php

namespace App\Domain\PDFTemplate\Service\Default;

final class GetPDFSalesItemsService
{
    public function getSaleItems(array $saleItems, string $additionalTerms = ''): string
    {
        $pdf_document = "";
        foreach ($saleItems as $index => $item) {
            foreach ($item['lineItems'] as $lineItem) {
                $pdf_document .= "<table style='width:100%; margin: 15px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555; page-break-inside: avoid;'>";
                $pdf_document .= "<thead>";
                $pdf_document .= "<tr>";
                $pdf_document .= "<th colspan='4' style='text-align: left; padding: 10px 5px; border-bottom: 0.1mm solid #efefef; font-size: 12px;'>";
                $pdf_document .= $lineItem['category'];
                $pdf_document .= "</th>";
                /* $pdf_document .= "<th style='text-align: right; padding: 10px 0; border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef;'>";
                 $pdf_document .= number_format($lineItem['amount']);
                 $pdf_document .= "</th>";*/
                $bottom_border = count($lineItem['items']) > 0 ? "0.1mm solid #efefef" : "none";
                $pdf_document .= "</tr>";
                $pdf_document .= "<tr>";
                $pdf_document .= "<th style='text-align: left; width: 15%; padding: 5px; border-bottom: {$bottom_border};'>";
                $pdf_document .= "Qty";
                $pdf_document .= "</th>";
                $pdf_document .= "<th style='text-align: left; padding: 5px; border-bottom: {$bottom_border};'>";
                $pdf_document .= "Description";
                $pdf_document .= "</th>";
                $pdf_document .= "<th style='text-align: right; width: 17.5%; padding: 5px; border-bottom: {$bottom_border};'>";
                $pdf_document .= "Unit Price";
                $pdf_document .= "</th>";
                $pdf_document .= "<th style='text-align: right; width: 17.5%; padding: 5px; border-bottom: {$bottom_border};'>";
                $pdf_document .= "Item Total";
                $pdf_document .= "</th>";
                $pdf_document .= "</tr>";
                $pdf_document .= "</thead>";
                $pdf_document .= "<tbody>";
                foreach ($lineItem['items'] as $key => $item) {
                    $bg_color = $key % 2 === 0 ? "#f3f3f4" : "";
                    $pdf_document .= "<tr style='background-color: {$bg_color};'>";
                    $pdf_document .= "<td style='text-align: left; padding: 3px 5px;'>";
                    $pdf_document .= $item['quantity'];
                    $pdf_document .= "</td>";
                    $pdf_document .= "<td style='text-align: left; padding: 3px 5px;'>";
                    $pdf_document .= $item['name'];
                    $pdf_document .= "</td>";
                    $pdf_document .= "<td style='text-align: right; padding: 3px 5px;'>";
                    $pdf_document .= "-";
                    $pdf_document .= "</td>";
                    $pdf_document .= "<td style='text-align: right; padding: 3px 5px;'>";
                    $pdf_document .= "-";
                    $pdf_document .= "</td>";
                    $pdf_document .= "</tr>";
                }
                $pdf_document .= "</tbody>";
                $pdf_document .= "<thead>";
                $pdf_document .= "<tr>";
                $pdf_document .= "<th colspan='3' style='text-align: right; padding: 10px 5px; border-bottom: 0.5mm solid #efefef; border-top: 0.5mm solid #efefef;'>";
                $pdf_document .= "Subtotal ({$lineItem['category']})";
                $pdf_document .= "</th>";
                $pdf_document .= "<th style='text-align: right;  padding: 10px 5px; border-bottom: 0.5mm solid #efefef; border-top: 0.5mm solid #efefef;'>";
                $pdf_document .= number_format($lineItem['amount'], 2);
                $pdf_document .= "</th>";
                $pdf_document .= "</tr>";
                $pdf_document .= "<thead>";
                $pdf_document .= "</table>";
            }
            $pdf_document .= "<table style='width:100%; margin: 15px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555; page-break-inside: avoid;'>";
            $pdf_document .= "<tfoot>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<td style='text-align: right;'>Subtotal</td>";
            $pdf_document .= "<td style='padding: 10px 0; width: 15%; text-align: right;'>" . number_format($saleItems[$index]['subtotal'], 2) . "</td>";
            $pdf_document .= "</tr>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<td style='padding: 10px 0; text-align: right;'>VAT</td>";
            $pdf_document .= "<td style='padding: 10px 0; width: 15%; text-align: right;'>" . number_format($saleItems[$index]['vat'], 2) . "</td>";
            $pdf_document .= "</tr>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; text-align: right;'>";
            $pdf_document .= 'Total Due';
            $pdf_document .= "</th>";
            $pdf_document .= "<th style='border-bottom: 0.75mm solid #efefef; border-top: 0.75mm solid #efefef; padding: 10px 0; width: 15%; text-align: right;'>";
            $pdf_document .= number_format($saleItems[$index]['total'], 2);
            $pdf_document .= "</th>";
            $pdf_document .= "</tr>";
            $pdf_document .= "</tfoot>";
            $pdf_document .= "</table>";
        }
        /*foreach ($saleItems as $booking) {
            $totalStops = array_reduce($booking['trips'], function ($total, $trip) {
                return $total + (count($trip['destinations']) - 2);
            }, 0);
            $totalDistance = (float)array_reduce($booking['trips'], function ($total, $trip) {
                return $total + $trip['tripDistance'];
            }, 0);
            $depositAmount = 0;
            if ($booking['saleStage'] === 'quotation') {
                $depositAmount = $booking['deposit'] > 0 ? $booking['deposit'] / 100 * $booking['saleTotal'] : 0;
            }

            $summary = "<table style='width:100%; margin: 15px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
                $summary.= "<thead>";
                    $summary.= "<tr>";
                        $summary.= "<th colspan='2' style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; text-align: left;'>Summary</th>";
                    $summary.= "</tr>";
                $summary.= "</thead>";
                $summary.= "<tbody>";
                    $summary.= "<tr>";
                        $summary.= "<td style='width: 35%; padding: 5px; vertical-align: middle;' class='inner contents'>Total Amount</td>";
                        $summary.= "<td style='padding: 5px; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
                            $summary.= number_format($booking['saleTotal'], 2);
                        $summary.= "</td>";
                    $summary.= "</tr>";
                    $summary.= "<tr>";
                        $summary.= "<td style='padding: 5px; background-color: #f9f9f9; vertical-align: middle;' class='inner contents'>Vehicle</td>";
                        $summary.= "<td style='padding: 5px; background-color: #f9f9f9; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
                            $summary.= $booking['className'] ?? 'TBA';
            if (!empty($booking['classDesc'])) {
                $summary.= "<p style='line-height: 12px; font-size: 10px;'>{$booking['classDesc']}</p>";
            }
                        $summary.= "</td>";
                    $summary.= "</tr>";
                    $summary.= "<tr>";
                        $summary.= "<td style='padding: 5px; vertical-align: middle;' class='inner contents'>Trip(s)</td>";
                        $summary.= "<td style='padding: 5px; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
                            $summary.= count($booking['trips']) . " Trip(s), " . $totalStops . " Stop(s), " . number_format($totalDistance, 2) . "km";
                        $summary.= "</td>";
                    $summary.= "</tr>";
                    $summary.= "<tr>";
                        $summary.= "<td colspan='2' style='padding: 5px; border-top: 0.1mm solid #efefef;' class='inner contents'>&nbsp;</td>";
                    $summary.= "</tr>";
                $summary.= "</tbody>";
            $summary.= "</table>";

            if (trim($additionalTerms) !== '') {
                $pdf_document.= "<table style='width:100%; margin: 15px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
                    $pdf_document.= "<tbody>";
                        $pdf_document.= "<tr style='vertical-align: top;'>";
                            $pdf_document.= "<td style='width: 50%; padding: 0 5px 0 0; vertical-align: top;' class='inner contents'>{$summary}</td>";
                            $pdf_document.= "<td style='width: 50%; padding: 0 0 0 5px; vertical-align: top;' class='inner contents'>{$additionalTerms}</td>";
                        $pdf_document.= "</tr>";
                    $pdf_document.= "</tbody>";
                $pdf_document.= "</table>";
            } else {
                $pdf_document.= $summary;
            }

            $pdf_document.= "<table style='width:100%; margin: 10px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
                 $pdf_document.= "<thead>";
                    $pdf_document.= '<tr>';
                        $pdf_document.= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; text-align: left;'>Description</th>";
                        $pdf_document.= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; width: 15%; text-align: right;'>Distance[KM]</th>";
                        $pdf_document.= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; width: 15%; text-align: right;'>Amount[ZAR]</th>";
                    $pdf_document.= "</tr>";
                $pdf_document.= "</thead>";
                $pdf_document.= "<tbody>";
            foreach ($booking['trips'] as $tripIndex => $trip) {
                $pdf_document.= ($tripIndex % 2) ? "<tr style='background-color: #f9f9f9;'>" : "<tr>";
                    $pdf_document.= "<td>";
                        $voucher = empty($trip['tripVoucher']) ? "None" : $trip['tripVoucher'];
                        $map_pin = IMAGES_DIR_LINK . "location-pin.png";
                        $pdf_document.= "<table style='width:100%; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
                foreach ($trip['destinations'] as $destinationIndex => $destination) {
                    $pdf_document.= "<tr>";
                    $pdf_document.= "<td style='padding: 5px; width: 25px; vertical-align: middle;'>";
                        $pdf_document.= "<img alt='{$destinationIndex}' style='height: 14px; width: 14px;' src='{$map_pin}' />";
                    $pdf_document.= "</td>";
                    $pdf_document.= "<td style='padding: 5px; vertical-align: middle'>";
                    if ($destinationIndex === 0) {
                        $pdf_document.= "<p style='line-height: 12px; font-size: 10px;'>{$trip['tripDate']} @ {$trip['tripTime']}, {$trip['tripPax']} Pax, Voucher: {$voucher}</p>";
                    }
                        $pdf_document.= "<p style='line-height: 14px; font-size: 11px;padding: 2px 0;'>{$destination['fullAddress']}</p>";
                    if ($destinationIndex === 0 && $trip['tripType'] === 2) {
                                    $flight = $trip['tripFlight'];
                                    $pdf_document.= "<p style='line-height: 12px; font-size: 10px;'>Flight No. {$flight['flightNo']} @ Terminal {$flight['terminalNo']}, {$flight['flightTime']}</p>";
                    }
                                    $pdf_document.= "</td>";
                                    $pdf_document.= "</tr>";
                }
                        $pdf_document.= "</table>";
                    $pdf_document.= "</td>";

                    $pdf_document.= "<td style='padding: 5px; text-align: right; vertical-align: top;'>";
                        $pdf_document.= number_format($trip['tripDistance'], 2);
                    $pdf_document.= "</td>";

                    $pdf_document.= "<td style='padding: 5px; text-align: right; vertical-align: top;'>";
                        $pdf_document.= $trip['tripRate'] > 0 ? number_format($trip['tripRate'], 2) : '-';
                    $pdf_document.= "</td>";
                    $pdf_document.= "</tr>";
            }
                $pdf_document.= "</tbody>";
                $pdf_document.= "<tfoot>";
                    $pdf_document.= "<tr>";
                        $pdf_document.= "<td style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; text-align: right;'>Sub-Total</td>";
                        $pdf_document.= "<td style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; width: 15%; text-align: right;'>" . number_format($totalDistance, 2) . "</td>";
                        $pdf_document.= "<td style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; width: 15%; text-align: right;'>" . number_format($booking['saleTotal'], 2) . "</td>";
                    $pdf_document.= "</tr>";

                    $invoice_due_date = $booking['invoiceDueDate'];
            if (empty(trim($invoice_due_date))) {
                $invoice_due = $booking['invoiceDue'] ?? 30;
                $last_trip = $booking['trips'][count($booking['trips']) - 1];
                if ($last_trip) {
                    $dateTimeObject = date_create(implode('-', explode('/', $last_trip['tripDate'])));
                    $interval = DateInterval::createFromDateString("{$invoice_due} day");
                    $dateTimeObject->add($interval);
                    $invoice_due_date = $dateTimeObject->format('Y/m/d');
                }
            }

            if ($booking['saleStage'] === 'quotation' && $depositAmount > 0 && $booking['totalPaid'] < $depositAmount) {
                $dateTimeObject = date_create(implode('-', explode('/', $booking['bookingDate'])));
                $interval = DateInterval::createFromDateString("{$booking['depositPayable']} day");
                $dateTimeObject->add($interval);

                $pdf_document.= "<tr>";
                    $pdf_document.= "<td style='padding: 10px 0; text-align: right;'>";
                        $pdf_document.= "Deposit(" . number_format($booking['deposit'], 2) . "%)";
                    $pdf_document.= "</td>";
                    $pdf_document.= "<td style='padding: 10px 0; width: 15%; text-align: right;'>";
                        $pdf_document.= $dateTimeObject->format('Y/m/d');
                    $pdf_document.= "</td>";
                    $pdf_document.= "<td style='padding: 10px 0; width: 15%; text-align: right;'>";
                        $pdf_document.= number_format($depositAmount, 2);
                    $pdf_document.= "</td>";
                $pdf_document.= "</tr>";
                $pdf_document.= "<tr>";
                    $pdf_document.= "<td style='padding: 10px 0; text-align: right;'>";
                        $pdf_document.= 'Balance';
                    $pdf_document.= "</td>";
                    $pdf_document.= "<td style='padding: 10px 0; width: 15%; text-align: right;'>";
                        $pdf_document.= $invoice_due_date;
                    $pdf_document.= "</td>";
                    $pdf_document.= "<td style='padding: 10px 0; width: 15%; text-align: right;'>";
                        $pdf_document.= number_format($booking['saleTotal'] - $depositAmount, 2);
                    $pdf_document.= "</td>";
                $pdf_document.= "</tr>";
            }

                    $pdf_document.= "<tr>";
                        $pdf_document.= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; text-align: right;'>";
                            $pdf_document.= 'Total Due';
                        $pdf_document.= "</th>";
                        $pdf_document.= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; width: 15%; text-align: right;'>";
                            $pdf_document.= $invoice_due_date;
                        $pdf_document.= "</th>";
                        $pdf_document.= "<th style='border-bottom: 0.5mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 0; width: 15%; text-align: right;'>";
                            $pdf_document.= number_format($booking['saleTotal'], 2);
                        $pdf_document.= "</th>";
                    $pdf_document.= "</tr>";
                $pdf_document.= "</tfoot>";
            $pdf_document.= "</table>";
        }*/

        return $pdf_document;
    }
}
