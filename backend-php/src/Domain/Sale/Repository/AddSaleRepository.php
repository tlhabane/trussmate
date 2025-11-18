<?php

namespace App\Domain\Sale\Repository;

use App\Domain\Sale\Data\SaleData;
use App\Domain\Sale\Data\MapSaleQueryData;
use PDO;

final class AddSaleRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addSale(SaleData $data): bool
    {
        $query = "INSERT INTO sale SET 
                  account_no = :account_no,
                  sale_id = :sale_id,
                  customer_id = :customer_id,
                  contact_id = :contact_id,
                  billing_address_id = :billing_address_id,
                  delivery_address_id = :delivery_address_id,
                  workflow_id = :workflow_id,
                  delivery_required = :delivery_required,
                  labour_required = :labour_required";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapSaleQueryData::map($data);
        $query_data['account_no'] = $data->account_no;

        return $query_stmt->execute($query_data);
    }
}
