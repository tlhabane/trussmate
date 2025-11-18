<?php

namespace App\Domain\PDFTemplate\Service\Default;

use App\Domain\BankAccount\Service\GetBankAccountService;
use PDO;

final class GetPDFBankDetailService
{
    private GetBankAccountService $getBankAccountService;

    public function __construct(PDO $connection)
    {
        $this->getBankAccountService = new GetBankAccountService($connection);
    }

    public function getBankAccount(string $accountNo): string
    {
        $pdf_document = '';

        $bankAccounts = $this->getBankAccountService->getBankAccount([
            'accountNo' => $accountNo,
        ]);
        if (isset($bankAccounts['records']) && count($bankAccounts['records']) > 0) {
            $colSpan = count($bankAccounts['records']) + 1;
            $pdf_document .= "<table style='width:100%; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555; page-break-inside: avoid;'>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<td colspan='{$colSpan}' style='border-top: 0.1mm solid #efefef; border-bottom: 0.1mm solid #efefef; font-weight: bold; padding: 10px 5px;'>";
            $pdf_document .= 'Banking Details';
            $pdf_document .= "</td>";
            $pdf_document .= "</tr>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<td style='width: 20%; padding: 5px;'>Bank Name</td>";
            foreach ($bankAccounts['records'] as $bankAccount) {
                $pdf_document .= "<td>{$bankAccount['bankName']}</td>";
            }
            $pdf_document .= "</tr>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<td style='width: 20%; padding: 5px;'>Account No.</td>";
            foreach ($bankAccounts['records'] as $bankAccount) {
                $pdf_document .= "<td>{$bankAccount['bankAccountNo']}</td>";
            }
            $pdf_document .= "</tr>";
            $pdf_document .= "<tr>";
            $pdf_document .= '<td style="width: 20%; padding: 5px;">Branch Code</td>';
            foreach ($bankAccounts['records'] as $bankAccount) {
                $pdf_document .= "<td style=''>{$bankAccount['branchCode']}</td>";
            }
            $pdf_document .= "</tr>";
            $pdf_document .= "</table>";
        }

        return $pdf_document;
    }
}
