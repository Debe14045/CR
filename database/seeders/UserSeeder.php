<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'client@itpi.test'],
            [
                'name' => 'Totok Antok',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CLIENT,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pm@itpi.test'],
            [
                'name' => 'PM Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PM,
            ]
        );

        foreach ([
            ['pmh@itpi.test', 'PM Head Demo', User::ROLE_PMH],
            ['presales@itpi.test', 'Presales Demo', User::ROLE_PRESALES],
            ['finance@itpi.test', 'Finance Demo', User::ROLE_FINANCE],
            ['admin@itpi.test', 'Admin Demo', User::ROLE_ADMIN],
        ] as [$email, $name, $role]) {
            User::updateOrCreate(compact('email'), [
                'name' => $name,
                'password' => Hash::make('password'),
                'role' => $role,
            ]);
        }
    }
}
