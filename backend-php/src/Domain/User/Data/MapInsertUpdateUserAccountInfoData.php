<?php

namespace App\Domain\User\Data;

final class MapInsertUpdateUserAccountInfoData
{
    public static function mapData(UserData $data): array
    {
        return [
            'username' => $data->username,
            'region_id' => $data->region_id,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'job_title' => $data->job_title,
            'tel' => $data->tel,
            'alt_tel' => $data->alt_tel,
            'email' => $data->email
        ];
    }
}
