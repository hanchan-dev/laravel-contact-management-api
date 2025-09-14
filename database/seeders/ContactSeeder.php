<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where('username', 'dummy')->first();
        $user->contacts()->create([
            'first_name' => 'dummy',
            'last_name' => 'dummy',
            'email' => 'dummy@dummy.com',
            'phone' => '+62 812 3456 6789',
        ]);
    }
}
