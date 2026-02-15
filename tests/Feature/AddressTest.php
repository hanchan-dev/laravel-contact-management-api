<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use function Symfony\Component\Translation\t;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

        $contact = Contact::first();

        $this->postJson("/api/contacts/$contact->id/addresses",
            [
                'street' => 'Test Street',
                'city' => 'Test City',
                'province' => 'Test Province',
                'country' => 'Test Country',
                'postal_code' => '123123',
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
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

        $contact = Contact::first();

        $this->postJson("/api/contacts/$contact->id/addresses",
            [
                'street' => 'Test Street',
                'city' => 'Test City',
                'province' => 'Test Province',
                'country' => '',
                'postal_code' => '123123',
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
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

//        $contact = Contact::first();

        $this->post("/api/contacts/123123/addresses",
            [
                'street' => 'Test Street',
                'city' => 'Test City',
                'province' => 'Test Province',
                'country' => 'Test Country',
                'postal_code' => '123123',
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
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

        $address = Address::query()->first();

        $this->getJson("/api/contacts/$address->contact_id/addresses/$address->id")
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
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

        $address = Address::query()->first();

        $this->getJson("/api/contacts/$address->contact_id/addresses/123123")
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
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

        $address = Address::query()->first();

        $this->putJson("/api/contacts/$address->contact_id/addresses/$address->id",
            [
                'street' => 'new street',
                'city' => 'new City',
                'province' => 'new Province',
                'country' => 'new Country',
                'postal_code' => '456456',
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
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

        $address = Address::query()->first();

        $this->putJson("/api/contacts/$address->contact_id/addresses/$address->id",
            [
                'street' => 'new street',
                'city' => 'new City',
                'province' => 'new Province',
                'country' => '',
                'postal_code' => '456456',
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
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

        $address = Address::query()->first();

        $this->deleteJson(uri: "/api/contacts/$address->contact_id/addresses/$address->id")
            ->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

        $address = Address::query()->first();

        $this->deleteJson(uri: "/api/contacts/$address->contact_id/addresses/123123")
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
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

        $contact = Contact::query()->first();

        $this->getJson(uri: "/api/contacts/$contact->id/addresses/")
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
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

        $contact = Contact::query()->first();

        $this->getJson(uri: "/api/contacts/$contact->id/addresses/")
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
        $user = User::where('username', 'dummy')->first();
        Sanctum::actingAs($user);

//        $contact = Contact::query()->first();

        $this->getJson(uri: "/api/contacts/123123/addresses/")
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
