<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('DEMO_ACCOUNT_PASSWORD');

        if (! is_string($password) || strlen($password) < 6) {
            throw new RuntimeException('Set DEMO_ACCOUNT_PASSWORD to a password of at least 6 characters before running UserSeeder.');
        }

        $accounts = [
            ['Administrator', 'admin@itpi.test', User::ROLE_ADMIN, false],
            ['client', 'client@itpi.test', User::ROLE_CLIENT, false],
            ['PM', 'pm@itpi.test', User::ROLE_PM, false],
            ['PMH', 'pmh@itpi.test', User::ROLE_PMH, true],
            ['Presales', 'presales@itpi.test', User::ROLE_PRESALES, false],
        ];

        DB::transaction(function () use ($accounts, $password): void {
            foreach ($accounts as [$name, $email, $role, $canActAsPm]) {
                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make($password),
                        'role' => $role,
                        'can_act_as_pm' => $canActAsPm,
                    ]
                );
            }
        });
    }
}
