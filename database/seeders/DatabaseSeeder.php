<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['user_id' => 'admin'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@123'),
                'status' => 1,
            ]
        );
    }
}