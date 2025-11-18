<?php

namespace App\Domain\CustomerAddress\Service;

use App\Domain\CustomerAddress\Repository\AddressIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetAddressIdService
{
    private AddressIdExistsRepository $addressIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->addressIdExistsRepository = new AddressIdExistsRepository($connection);
    }

    public function getAddressId(int $length): string
    {
        do {
            $address_id = Utilities::generateToken($length);
        } while (empty($address_id) || $this->addressIdExistsRepository->idExists($address_id));

        return $address_id;
    }
}
