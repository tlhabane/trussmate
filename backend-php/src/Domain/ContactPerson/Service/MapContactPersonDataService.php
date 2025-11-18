<?php

namespace App\Domain\ContactPerson\Service;

use App\Domain\ContactPerson\Data\ContactPersonData;

final class MapContactPersonDataService
{
    public static function map(array $data): ContactPersonData
    {
        $contact = new ContactPersonData();
        $contact->customer_id = $data['customerId'];
        $contact->contact_id = $data['contactId'];
        $contact->first_name = $data['firstName'];
        $contact->last_name = $data['lastName'];
        $contact->job_title = $data['jobTitle'];
        $contact->tel = $data['tel'];
        $contact->alt_tel = $data['altTel'];
        $contact->email = $data['email'];
        $contact->search = $data['search'];

        return $contact;
    }
}
