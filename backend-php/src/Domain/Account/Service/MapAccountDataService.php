<?php

namespace App\Domain\Account\Service;

use App\Domain\Account\Data\AccountStatus;
use App\Domain\Account\Data\AccountData;

final class MapAccountDataService
{
    public static function mapData(array $sanitizedData): AccountData
    {
        $data = new AccountData();
        $data->account_status = match (strtolower($sanitizedData['accountStatus'])) {
            'pending' => AccountStatus::pending,
            'cancelled' => AccountStatus::cancelled,
            'suspended' => AccountStatus::suspended,
            default => AccountStatus::active
        };
        $data->logo = $sanitizedData['logo'] ?? '';
        $data->registration_no = $sanitizedData['registrationNo'];
        $data->registered_name = $sanitizedData['registeredName'];
        $data->vat_no = $sanitizedData['vatNo'];
        $data->trading_name = $sanitizedData['tradingName'];
        $data->tel = $sanitizedData['tel'];
        $data->alt_tel = $sanitizedData['altTel'];
        $data->email = $sanitizedData['email'];
        $data->web = $sanitizedData['web'];
        $data->address = $sanitizedData['address'];

        return $data;
    }
}
