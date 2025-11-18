<?php

namespace App\Domain\Sale\Service;

use App\Domain\Sale\Data\SaleStatus;

final class GetSaleStatusService
{
    public static function getStatus(string $sale_status): SaleStatus
    {
        return match (trim(strtolower($sale_status))) {
            'pending' => SaleStatus::PENDING,
            'started' => SaleStatus::STARTED,
            'cancelled' => SaleStatus::CANCELLED,
            'completed' => SaleStatus::COMPLETED,
            default => SaleStatus::TENTATIVE
        };
    }
}
