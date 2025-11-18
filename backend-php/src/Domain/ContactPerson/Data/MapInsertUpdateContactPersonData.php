<?php

namespace App\Domain\ContactPerson\Data;

final class MapInsertUpdateContactPersonData
{
    public static function map(ContactPersonData $data): array
    {
        return [
            'contact_id' => $data->contact_id,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'job_title' => $data->job_title,
            'tel' => $data->tel,
            'alt_tel' => $data->alt_tel,
            'email' => $data->email
        ];
    }
}
