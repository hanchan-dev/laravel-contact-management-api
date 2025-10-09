<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'hanchan',
            'password' => 'rahasia',
            'name' => 'Handika'
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'hanchan',
                    'name' => 'Handika'
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => ''
        ])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'The username field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyTaken()
    {
        $this->testRegisterSuccess();

        $this->post('/api/users', [
            'username' => 'hanchan',
            'password' => 'rahasia',
            'name' => 'handika'
        ])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'Username is already taken'
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/users/login', [
            'username' => 'dummy',
            'password' => 'dummy',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'dummy',
                    'name' => 'dummy'
                ]
            ]);
        $user = User::query()->where('username', 'dummy')->first();
        self::assertNotNull($user->token);

    }

    public function testLoginFailed()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'dummy',
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => [
                        'username and password combination is incorrect'
                    ]
                ]
            ]);
    }



    public function testGetSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'dummy',
                    'name' => 'dummy'
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthenticated'
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current', [
            'Accept' => 'application/json',
            'Authorization' => 'salah'
        ])
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthenticated'
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed(UserSeeder::class);

        $oldUser = User::query()->where('username', 'dummy')->first();

        $this->patch('/api/users/current',
            [
                'name' => 'baru'
            ],
            [
                'Authorization' => 'test'
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'dummy',
                    'name' => 'baru'
                ]
            ]);

        $newUser = User::query()->where('username', 'dummy')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed(UserSeeder::class);

        $oldUser = User::query()->where('username', 'dummy')->first();

        $this->patch('/api/users/current',
            [
                'password' => 'baru'
            ],
            [
                'Authorization' => 'test'
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'dummy',
                    'name' => 'dummy'
                ]
            ]);

        $newUser = User::query()->where('username', 'dummy')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateFailed()
    {
        $this->seed(UserSeeder::class);


        $this->patch('/api/users/current',
            [
                'name' => 'barubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubaru'
            ],
            [
                'Authorization' => 'test'
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field must not be greater than 100 characters.'
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);

        $user = User::query()->where('username', 'dummy')->first();
        self::assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->seed(UserSeeder::class);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'salah'
        ])
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthenticated'
                    ]
                ]
            ]);
    }


}


