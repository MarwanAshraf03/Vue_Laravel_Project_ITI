<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Creates the default admin account:
     *   email:    admin@admin.com
     *   password: password
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Platform Admin',
                'password' => 'password',
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        $admin->profile()->firstOrCreate(['user_id' => $admin->id]);
    }
}
