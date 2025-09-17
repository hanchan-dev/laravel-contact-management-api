<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function Symfony\Component\Translation\t;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();

        $this->post("/api/contacts/$contact->id/addresses",
            [
                'street' => 'Test Street',
                'city' => 'Test City',
                'province' => 'Test Province',
                'country' => 'Test Country',
                'postal_code' => '123123',
            ],
            [
                'Authorization' => "test"
            ]
        )->assertStatus(201)
        ->assertJson([
            'data' => [
                'street' => 'Test Street',
                'city' => 'Test City',
                'province' => 'Test Province',
                'country' => 'Test Country',
                'postal_code' => '123123',
            ]
        ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();

        $this->post("/api/contacts/$contact->id/addresses",
            [
                'street' => 'Test Street',
                'city' => 'Test City',
                'province' => 'Test Province',
                'country' => '',
                'postal_code' => '123123',
            ],
            [
                'Authorization' => "test"
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => [
                        'The country field is required.'
                    ]
                ]
            ]);
    }

    public function testCreateButContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::first();

        $this->post("/api/contacts/123123/addresses",
            [
                'street' => 'Test Street',
                'city' => 'Test City',
                'province' => 'Test Province',
                'country' => 'Test Country',
                'postal_code' => '123123',
            ],
            [
                'Authorization' => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->first();

        $this->get("/api/contacts/$address->contact_id/addresses/$address->id", [
            'Authorization' => "test"
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $address->id,
                    'street' => $address->street,
                    'city' => $address->city,
                    'province' => $address->province,
                    'country' => $address->country,
                    'postal_code' => $address->postal_code,
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->first();

        $this->get("/api/contacts/$address->contact_id/addresses/123123", [
            'Authorization' => "test"
        ])
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Address not found'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->first();

        $this->put("/api/contacts/$address->contact_id/addresses/$address->id",
            [
                'street' => 'new street',
                'city' => 'new City',
                'province' => 'new Province',
                'country' => 'new Country',
                'postal_code' => '456456',
            ],
            [
            'Authorization' => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $address->id,
                    'street' => 'new street',
                    'city' => 'new City',
                    'province' => 'new Province',
                    'country' => 'new Country',
                    'postal_code' => '456456',
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->first();

        $this->put("/api/contacts/$address->contact_id/addresses/$address->id",
            [
                'street' => 'new street',
                'city' => 'new City',
                'province' => 'new Province',
                'country' => '',
                'postal_code' => '456456',
            ],
            [
                'Authorization' => "test"
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => [
                        'The country field is required.'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->first();

        $this->delete(uri: "/api/contacts/$address->contact_id/addresses/$address->id",
            headers: [
                'Authorization' => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->first();

        $this->delete(uri: "/api/contacts/$address->contact_id/addresses/123123",
            headers: [
                'Authorization' => "test"
            ]
        )
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Address not found'
                    ]
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->first();

        $this->get(uri: "/api/contacts/$contact->id/addresses/",
            headers: [
                'Authorization' => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'street' => 'testStreet',
                        'province' => 'testProvince',
                        'city' => 'testCity',
                        'country' => 'testCountry',
                        'postal_code' => '123123',
                    ]
                ]
            ]);
    }

    public function testListEmpty()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();

        $this->get(uri: "/api/contacts/$contact->id/addresses/",
            headers: [
                'Authorization' => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'data' => [],
                'description' => [
                    'No addresses'
                ]
            ]);
    }

    public function testListNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();

        $this->get(uri: "/api/contacts/123123/addresses/",
            headers: [
                'Authorization' => "test"
            ]
        )
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ]);
    }


}
