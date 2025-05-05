<?php

namespace Database\Seeders;

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
        $users = [
            [
                'first_name' => 'Liam Nicolo',
                'middle_name' => 'Navarro',
                'last_name' => 'Demafelix',
                'username' => 'liamdemafelix',
                'email' => 'hey@liam.ph',
                'password' => Hash::make('P@ssw0rd'),
                'is_moderator' => true,
            ]
        ];
        foreach ($users as $user) {
            if (!\App\Models\User::where('email', $user['email'])->exists()) {
                \App\Models\User::create($user);
            }
        }
    }
}
