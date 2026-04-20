<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['user_id' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Super@123'),
                'status' => 'active',
                'is_super_admin' => 1,
            ]
        );
    }
}