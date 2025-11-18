<?php

namespace App\Domain\Sale\Service;

use App\Domain\SaleDocument\Service\GetSaleDocumentService;
use App\Domain\CustomerAddress\Service\GetCustomerAddressService;
use App\Domain\ContactPerson\Service\GetContactPersonService;
use App\Domain\Customer\Service\GetCustomerService;
use App\Domain\SaleTask\Service\GetSaleTaskService;
use App\Domain\SaleJob\Service\GetSaleJobService;
use App\Domain\Sale\Repository\GetSaleRepository;
use App\Exception\ValidationException;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetSaleService
{
    private GetCustomerAddressService $getCustomerAddressService;
    private GetContactPersonService $getContactPersonService;
    private GetSaleDocumentService $getSaleDocumentService;
    private GetCustomerService $getCustomerService;
    private GetSaleTaskService $getSaleTaskService;
    private GetSaleJobService $getSaleJobService;
    private GetSaleRepository $getSaleRepository;

    public function __construct(PDO $connection)
    {
        $this->getCustomerAddressService = new GetCustomerAddressService($connection);
        $this->getContactPersonService = new GetContactPersonService($connection);
        $this->getSaleDocumentService = new GetSaleDocumentService($connection);
        $this->getCustomerService = new GetCustomerService($connection);
        $this->getSaleTaskService = new GetSaleTaskService($connection);
        $this->getSaleJobService = new GetSaleJobService($connection);
        $this->getSaleRepository = new GetSaleRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function getSale(array $data): array
    {
        $sanitizedData = SanitizeSaleDataService::sanitizeData($data);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );

        $salesData = SetSaleDataService::set($sanitizedData);
        $salesData->account_no = $data['accountNo'];

        $records = [];
        $sales = $this->getSaleRepository->getSale(
            $salesData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );

        foreach ($sales as $sale) {
            $addresses = $this->getCustomerAddressService->getAddress([
                'accountNo' => $data['accountNo'],
                'sessionUserRole' => $data['sessionUserRole'],
                'customerId' => $sale['customer_id']
            ]);

            $customer_addresses = $addresses['records'] ?? [];

            $tasks = $this->getSaleTaskService->getTask([
                'accountNo' => $data['accountNo'],
                'sessionUserRole' => $data['sessionUserRole'],
                'saleId' => $sale['sale_id']
            ]);

            $sale_tasks = $tasks['records'] ?? [];

            $documents = $this->getSaleDocumentService->getSaleDocument([
                'accountNo' => $data['accountNo'],
                'sessionUserRole' => $data['sessionUserRole'],
                'saleId' => $sale['sale_id']
            ]);

            $sale_documents = $documents['records'] ?? [];

            $jobs = $this->getSaleJobService->getSaleJob([
                'accountNo' => $data['accountNo'],
                'sessionUserRole' => $data['sessionUserRole'],
                'saleId' => $sale['sale_id']
            ]);
            $sale_jobs = $jobs['records'] ?? [];
            $total = array_reduce($sale_jobs, function (float $acc, $job) {
                $acc += $job['total'];
                return $acc;
            }, 0);

            $records[] = [
                'saleId' => $sale['sale_id'],
                'saleNo' => Utilities::addPadding($sale['sale_no'], 0),
                'saleTotal' => $total,
                'jobs' => $sale_jobs,
                'tasks' => $sale_tasks,
                'documents' => $sale_documents,
                'saleStatus' => $sale['sale_status'],
                'customer' => $this->getCustomer($data['accountNo'], $sale['customer_id']),
                'contact' => $this->getContact($data['accountNo'], $sale['contact_id']),
                'billingAddress' => $this->getAddress($sale['billing_address_id'], $customer_addresses),
                'deliveryAddress' => $this->getAddress($sale['delivery_address_id'], $customer_addresses),
                'delivery' => $sale['delivery_required'],
                'labour' => $sale['labour_required'],
                'saleDate' => date_format(date_create($sale['created']), 'c')
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getSaleRepository->getSale($salesData);
            $pagination = DataPagination::getPagingLinks(
                $sanitizedData['page'],
                $countRecords->rowCount(),
                $paginationConfig['recordsPerPage']
            );

            return ['records' => $records, 'pagination' => $pagination];
        }

        return ['records' => $records];
    }

    private function getAddress(string $address_id, array $customer_addresses): array
    {
        foreach ($customer_addresses as $address) {
            if ($address['addressId'] === $address_id) {
                return [
                    'addressId' => $address['addressId'],
                    'unitNo' => $address['unitNo'],
                    'complexName' => $address['complexName'],
                    'fullAddress' => $address['fullAddress'],
                    'country' => $address['country'],
                    'province' => $address['province'],
                    'city' => $address['city'],
                    'suburb' => $address['suburb'],
                    'streetAddress' => $address['streetAddress'],
                    'postalCode' => $address['postalCode'],
                    'placeId' => $address['placeId'],
                    'latitude' => $address['latitude'],
                    'longitude' => $address['longitude'],
                ];
            }
        }

        return [
            'addressId' => '',
            'unitNo' => '',
            'complexName' => '',
            'fullAddress' => '',
            'country' => '',
            'province' => '',
            'city' => '',
            'suburb' => '',
            'streetAddress' => '',
            'postalCode' => '',
            'placeId' => '',
            'latitude' => 0,
            'longitude' => 0,
        ];
    }

    private function getCustomer(string $account_no, string $customer_id): array
    {
        $customers = $this->getCustomerService->getCustomer([
            'accountNo' => $account_no,
            'customerId' => $customer_id
        ]);

        foreach ($customers['records'] as $customer) {
            return [
                'customerId' => $customer['customerId'],
                'customerType' => $customer['customerType'],
                'customerName' => $customer['customerName'],
                'registrationNo' => $customer['registrationNo'],
                'vatNo' => $customer['vatNo'],
                'tel' => $customer['tel'],
                'altTel' => $customer['altTel'],
                'email' => $customer['email'],
                'web' => $customer['web'],
            ];
        }

        return [
            'customerId' => $customer_id,
            'customerType' => '',
            'customerName' => '',
            'registrationNo' => '',
            'vatNo' => '',
            'tel' => '',
            'altTel' => '',
            'email' => '',
            'web' => '',
        ];
    }

    private function getContact(string $account_no, string $contact_id): array
    {
        if (!empty($contact_id)) {
            $contacts = $this->getContactPersonService->getContact([
                'accountNo' => $account_no,
                'contactId' => $contact_id
            ]);

            foreach ($contacts['records'] as $contact) {
                return [
                    'contactId' => $contact['contactId'],
                    'firstName' => $contact['firstName'],
                    'lastName' => $contact['lastName'],
                    'jobTitle' => $contact['jobTitle'],
                    'tel' => $contact['tel'],
                    'altTel' => $contact['altTel'],
                    'email' => $contact['email'],
                ];
            }
        }

        return [
            'contactId' => $contact_id,
            'firstName' => '',
            'lastName' => '',
            'jobTitle' => '',
            'tel' => '',
            'altTel' => '',
            'email' => '',
        ];
    }
}
