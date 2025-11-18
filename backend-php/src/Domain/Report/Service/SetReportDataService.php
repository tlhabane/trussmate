<?php

namespace App\Domain\Report\Service;

use App\Domain\Report\Data\ReportData;

final class SetReportDataService
{
    public static function set(array $data): ReportData
    {
        $reportData = new ReportData();
        $reportData->customer_id = $data['customerId'];
        $reportData->start_date = $data['startDate'];
        $reportData->end_date = $data['endDate'];

        return $reportData;
    }
}
