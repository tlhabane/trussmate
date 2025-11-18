<?php

namespace App\Domain\Invoice\Repository;

use App\Domain\Invoice\Data\InvoiceData;
use PDO;

final class AddInvoiceRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addInvoice(InvoiceData $data): int
    {
        $query = "INSERT INTO invoice SET
                  sale_task_id = :sale_task_id,
                  invoice_type = :invoice_type";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':sale_task_id', $data->sale_task_id);
        $invoice_type = $data->invoice_type->value;
        $query_stmt->bindParam('invoice_type', $invoice_type);
        $query_stmt->execute();
        return (int)$this->connection->lastInsertId('invoice_no');
    }
}
