<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts',
            [
            'first_name' => 'handika',
            'last_name' => 'cp',
            'email' => 'handika@gmail.com',
            'phone' => '+62 812 2345 6789',
            ],
            [
                'Authorization' => 'test'
            ]
        )
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'handika',
                    'last_name' => 'cp',
                    'email' => 'handika@gmail.com',
                    'phone' => '+62 812 2345 6789',
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts',
            [
                'first_name' => '',
                'last_name' => 'cp',
                'email' => 'handika',
                'phone' => '+62 812 2345 6789',
            ],
            [
                'Authorization' => 'test'
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ]
                ]
            ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts',
            [
                'first_name' => '',
                'last_name' => 'cp',
                'email' => 'handika',
                'phone' => '+62 812 2345 6789',
            ]
        )
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthenticated'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();
        $this->get("/api/contacts/$contact->id", [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->assertJson([
               'data' => [
                   'id' => $contact->id,
                   'first_name' => $contact->first_name,
                   'last_name' => $contact->last_name,
                   'email' => $contact->email,
                   'phone' => $contact->phone,
               ]
            ]);
    }

    public function testGetFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $this->get("/api/contacts/123", [
            'Authorization' => 'test'
        ])
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ]);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();
        $this->get("/api/contacts/$contact->id", [
            'Authorization' => 'test2'
        ])
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();
        $this->put("/api/contacts/$contact->id",
            [
                'first_name' => 'test123',
                'last_name' => 'hehe',
                'email' => 'wkwk@gmail.com',
                'phone' => '+62 812 2345 6789',
            ],

            [
            'Authorization' => 'test'
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test123',
                    'last_name' => 'hehe',
                    'email' => 'wkwk@gmail.com',
                    'phone' => '+62 812 2345 6789',
                ]
            ]);
    }

    public function testUpdateFailedValidation()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();
        $this->put("/api/contacts/$contact->id",
            [
                'first_name' => '',
                'last_name' => 'hehe',
                'email' => 'wkwk@gmail.com',
                'phone' => '+62 812 2345 6789',
            ],

            [
                'Authorization' => 'test'
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();
        $this->delete(uri: "/api/contacts/$contact->id", headers: [
                'Authorization' => 'test'
            ])
            ->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();
        $this->delete(uri: "/api/contacts/" . $contact->id + 1, headers: [
            'Authorization' => 'test'
        ])
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ]);
    }

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(10, $response['data']);
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(10, $response['data']);
        self::assertEquals(20, $response['meta']['total']);
    }


    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(10, $response['data']);
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=+62', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(10, $response['data']);
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=tidakaDa', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(0, $response['data']);
        self::assertEquals(0, $response['meta']['total']);
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(5, $response['data']);
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }


}
