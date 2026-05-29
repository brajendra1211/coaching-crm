<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@coachingcrm.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@coachingcrm.com',
                'phone' => '9999999999',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'superadmin@coachingcrm.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@coachingcrm.com',
                'phone' => '9999999998',
                'password' => Hash::make('Super@123'),
                'role' => 'super_admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
