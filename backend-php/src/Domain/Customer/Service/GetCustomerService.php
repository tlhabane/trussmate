<?php

namespace App\Domain\Customer\Service;

use App\Domain\ContactPerson\Service\GetContactPersonService;
use App\Domain\CustomerAddress\Service\GetCustomerAddressService;
use App\Domain\Customer\Repository\GetCustomerRepository;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetCustomerService
{
    private GetCustomerAddressService $getCustomerAddressService;
    private GetContactPersonService $getContactPersonService;
    private GetCustomerRepository $getCustomerRepository;

    public function __construct(PDO $connection)
    {
        $this->getCustomerAddressService = new GetCustomerAddressService($connection);
        $this->getContactPersonService = new GetContactPersonService($connection);
        $this->getCustomerRepository = new GetCustomerRepository($connection);
    }

    public function getCustomer(array $data): array
    {
        $sanitizedData = SanitizeCustomerDataService::sanitizeData($data);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );
        $customerData = MapCustomerDataService::map($sanitizedData);
        $customerData->account_no = $data['accountNo'];

        $records = [];
        $customers = $this->getCustomerRepository->getCustomer(
            $customerData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );

        foreach ($customers as $customer) {
            $addresses = $this->getCustomerAddressService->getAddress([
                'accountNo' => $data['accountNo'],
                'customerId' => $customer['customer_id'],
            ]);

            $contacts = $this->getContactPersonService->getContact([
                'accountNo' => $data['accountNo'],
                'customerId' => $customer['customer_id'],
            ]);

            $records[] = [
                'customerId' => $customer['customer_id'],
                'customerType' => $customer['customer_type'],
                'customerName' => Utilities::decodeUTF8($customer['customer_name']),
                'registrationNo' => Utilities::decodeUTF8($customer['registration_no']),
                'vatNo' => $customer['vat_no'],
                'tel' => $customer['tel'],
                'altTel' => $customer['alt_tel'],
                'email' => $customer['email'],
                'web' => $customer['web'],
                'contacts' => $contacts['records'] ?? [],
                'addresses' => $addresses['records'] ?? []
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getCustomerRepository->getCustomer($customerData);
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
