<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['user_id' => 'superadmin'],
            [
                'name'           => 'Super Admin',
                'email'          => 'superadmin@gau-mitra.com',
                'password'       => Hash::make('Super@12345'),
                'status'         => 'active',
                'is_super_admin' => true,
            ]
        );
    }
}