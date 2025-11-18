<?php

namespace App\Domain\Account\Data;


final class MapInsertUpdateAccountData
{
    public static function mapData(AccountData $data): array
    {
        return [
            'account_no' => $data->account_no,
            'logo' => $data->logo,
            'registration_no' => $data->registration_no,
            'registered_name' => $data->registered_name,
            'vat_no' => $data->vat_no,
            'trading_name' => $data->trading_name,
            'tel' => $data->tel,
            'alt_tel' => $data->alt_tel,
            'email' => $data->email,
            'web' => $data->web,
            'address' => $data->address
        ];
    }
}
