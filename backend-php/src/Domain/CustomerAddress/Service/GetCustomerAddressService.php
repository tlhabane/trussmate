<?php

namespace App\Domain\CustomerAddress\Service;

use App\Domain\CustomerAddress\Repository\GetCustomerAddressRepository;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetCustomerAddressService
{
    private GetCustomerAddressRepository $getCustomerAddressRepository;

    public function __construct(PDO $connection)
    {
        $this->getCustomerAddressRepository = new GetCustomerAddressRepository($connection);
    }

    public function getAddress(array $data): array
    {
        $sanitizedData = SanitizeCustomerAddressDataService::sanitizeData($data);
        $addressData = MapCustomerAddressDataService::map($sanitizedData);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );

        $records = [];
        $addresses = $this->getCustomerAddressRepository->getAddress(
            $addressData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );

        foreach ($addresses as $address) {
            $records[] = [
                'customerId' => $address['customer_id'],
                'addressId' => $address['address_id'],
                'billingAddress' => intval($address['billing_address']),
                'unitNo' => Utilities::decodeUTF8($address['unit_no']),
                'complexName' => Utilities::decodeUTF8($address['complex_name']),
                'fullAddress' => Utilities::decodeUTF8($address['full_address']),
                'country' => Utilities::decodeUTF8($address['country']),
                'province' => Utilities::decodeUTF8($address['province']),
                'city' => Utilities::decodeUTF8($address['city']),
                'suburb' => Utilities::decodeUTF8($address['suburb']),
                'streetAddress' => Utilities::decodeUTF8($address['street_address']),
                'postalCode' => Utilities::decodeUTF8($address['postal_code']),
                'placeId' => $address['place_id'],
                'latitude' => floatval($address['latitude']),
                'longitude' => floatval($address['longitude']),
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getCustomerAddressRepository->getAddress($addressData);
            $pagination = DataPagination::getPagingLinks(
                $sanitizedData['page'],
                $countRecords->rowCount(),
                $paginationConfig['recordsPerPage']
            );

            return ['records' => $records, 'pagination' => $pagination];
        }

        return ['records' => $records];
    }
}
