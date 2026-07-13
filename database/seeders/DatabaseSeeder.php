<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles
        $roles = [
            ['id' => 1, 'name' => 'superadmin', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'hardwareadmin', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'chm', 'guard_name' => 'web'],
            ['id' => 4, 'name' => 'programmer', 'guard_name' => 'web'],
            ['id' => 5, 'name' => 'cowd', 'guard_name' => 'web'],
            ['id' => 6, 'name' => 'admin', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['id' => $role['id']],
                ['name' => $role['name'], 'guard_name' => $role['guard_name']]
            );
        }

        // Seed superadmin user
        User::updateOrCreate(
            ['email' => 'superadmin@kla.com'],
            [
                'name' => 'superadmin',
                'username' => 'superadmin',
                'password' => Hash::make('superadmin'),
                'role_id' => 1,
            ]
        );
    }
}
