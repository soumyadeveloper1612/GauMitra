<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'dashboard.view',    'label' => 'View Dashboard',          'module' => 'Dashboard'],
            ['name' => 'users.view',        'label' => 'View Users',              'module' => 'Users'],
            ['name' => 'gaushala.create',   'label' => 'Create Gaushala',         'module' => 'Gaushala'],
            ['name' => 'gaushala.view',     'label' => 'Manage Gaushala',         'module' => 'Gaushala'],
            ['name' => 'report_cases.view', 'label' => 'View Report Cases',       'module' => 'Reports'],
            ['name' => 'news_notice.create','label' => 'Create News & Notice',    'module' => 'News & Notices'],
            ['name' => 'news_notice.view',  'label' => 'Manage News & Notices',   'module' => 'News & Notices'],
            ['name' => 'admins.manage',     'label' => 'Manage Admins',           'module' => 'Admin Management'],
            ['name' => 'roles.manage',      'label' => 'Manage Roles & Permissions','module' => 'Admin Management'],
            ['name' => 'settings.manage',   'label' => 'Manage Settings',         'module' => 'Settings'],
        ];

        $permissionIds = [];

        foreach ($permissions as $permission) {
            $record = Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'label'  => $permission['label'],
                    'module' => $permission['module'],
                    'status' => 'active',
                ]
            );

            $permissionIds[] = $record->id;
        }

        $superRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'label' => 'Super Admin',
                'description' => 'Full access to all admin modules',
                'status' => 'active',
            ]
        );

        $superRole->permissions()->sync($permissionIds);

        $superAdmin = AdminUser::firstOrCreate(
            ['user_id' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'is_super_admin' => true,
            ]
        );

        $superAdmin->roles()->syncWithoutDetaching([$superRole->id]);
    }
}