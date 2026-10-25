<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun DEMO saja (bukan kredensial asli). Password di-hash otomatis oleh cast 'hashed'.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@tumbler.test'],
            ['name' => 'Owner Tumbler', 'password' => 'password123', 'role' => User::ROLE_ADMIN]
        );

        User::firstOrCreate(
            ['email' => 'staff@tumbler.test'],
            ['name' => 'Staff Kasir', 'password' => 'password123', 'role' => User::ROLE_STAFF]
        );
    }
}
