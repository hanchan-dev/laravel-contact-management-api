<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User([
            'username' => 'dummy',
            'password' => Hash::make('dummy'),
            'name' => 'dummy',
            'token' => 'test'
        ]);

        $user->save();

        $user = new User([
            'username' => 'dummy2',
            'password' => Hash::make('dumm2'),
            'name' => 'dummy2',
            'token' => 'test2'
        ]);

        $user->save();
    }
}
