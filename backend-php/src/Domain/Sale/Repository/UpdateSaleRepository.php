<?php

namespace App\Domain\Sale\Repository;

use App\Domain\Sale\Data\SaleData;
use App\Domain\Sale\Data\MapSaleQueryData;
use PDO;

final class UpdateSaleRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateSale(SaleData $data): bool
    {
        $query = "UPDATE sale SET 
                      sale_status = :sale_status,
                      customer_id = :customer_id,
                      contact_id = :contact_id,
                      billing_address_id = :billing_address_id,
                      delivery_address_id = :delivery_address_id,
                      workflow_id = :workflow_id,
                      delivery_required = :delivery_required,
                      labour_required = :labour_required 
                  WHERE 
                      sale_id = :sale_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapSaleQueryData::map($data);
        $query_data['sale_status'] = $data->sale_status->value;

        return $query_stmt->execute($query_data);
    }
}
