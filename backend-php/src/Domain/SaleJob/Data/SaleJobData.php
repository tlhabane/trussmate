<?php

namespace App\Domain\SaleJob\Data;

final class SaleJobData
{
    public string $sale_id;
    public string $job_no;
    public string $job_description;
    public string $design_info;
    public string $line_items;
    public float $subtotal;
    public float $vat;
    public float $total;
}
