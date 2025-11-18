<?php

namespace App\Domain\ContactPerson\Data;

class ContactPersonData
{
    /* FK: Contact info owner */
    public string $account_no;
    public string $customer_id;
    /* Contact info */
    public string $contact_id;
    public string $first_name;
    public string $last_name;
    public string $job_title;
    public string $tel;
    public string $alt_tel;
    public string $email;
    /* Full text search */
    public string $search;
}
