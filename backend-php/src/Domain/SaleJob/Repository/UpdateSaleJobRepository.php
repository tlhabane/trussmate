<?php

namespace App\Domain\SaleJob\Repository;

use App\Domain\SaleJob\Data\SaleJobData;
use App\Domain\SaleJob\Data\SetSaleJobQueryData;
use PDO;

final class UpdateSaleJobRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateSaleJob(SaleJobData $data): bool
    {
        $query = "UPDATE sale_job SET
                      job_no = :job_no,
                      job_description = :job_description,
                      design_info = :design_info,
                      line_items = :line_items,
                      subtotal = :subtotal,
                      vat = :vat,
                      total = :total 
                  WHERE 
                      sale_id = :sale_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = SetSaleJobQueryData::set($data);
        return $query_stmt->execute($query_data);
    }
}
