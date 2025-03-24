<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'role_name' => 'organisateur'
            ],
            [
                'id' => 3,
                'role_name' => 'admin'
            ],
            [
                'id' => 2,
                'role_name' => 'user'
            ],
            [
                'id' => 4,
                'role_name' => 'participant'
            ],
            [
                'id' => 5,
                'role_name' => 'jury'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        User::create([
            'name' => 'ayoub',
            'email' => 'ayoub@email.com',
            'password' => Hash::make('password'),
            'role_id' => 1
        ]);
    }
}
