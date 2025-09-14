<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::query()->first();
        $address = new Address([
            'street' => 'testStreet',
            'city' => 'testCity',
            'province' => 'testProvince',
            'country' => 'testCountry',
            'postal_code' => '123123',
            'contact_id' => $contact->id,
        ]);
        $address->save();


        $contact = Contact::query()->first();
        $address = new Address([
            'street' => 'testStreet 2',
            'city' => 'testCity 2',
            'province' => 'testProvince 2',
            'country' => 'testCountry 2',
            'postal_code' => '123123123',
            'contact_id' => $contact->id,
        ]);
        $address->save();
    }
}
