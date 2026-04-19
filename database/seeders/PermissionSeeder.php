<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'dashboard.view',     'label' => 'View Dashboard',              'module' => 'Dashboard'],
            ['name' => 'users.view',         'label' => 'View Users',                  'module' => 'Users'],

            ['name' => 'gaushala.view',      'label' => 'View Gaushala',               'module' => 'Gaushala'],
            ['name' => 'gaushala.create',    'label' => 'Create Gaushala',             'module' => 'Gaushala'],

            ['name' => 'report_cases.view',  'label' => 'View Report Cases',           'module' => 'Report Cases'],
            ['name' => 'report_cases.update','label' => 'Update Report Case Status',   'module' => 'Report Cases'],
            ['name' => 'report_cases.assign','label' => 'Assign Report Case Handler',  'module' => 'Report Cases'],

            ['name' => 'news_notice.view',   'label' => 'View News & Notices',         'module' => 'News & Notices'],
            ['name' => 'news_notice.create', 'label' => 'Create News & Notices',       'module' => 'News & Notices'],
            ['name' => 'news_notice.edit',   'label' => 'Edit News & Notices',         'module' => 'News & Notices'],
            ['name' => 'news_notice.delete', 'label' => 'Delete News & Notices',       'module' => 'News & Notices'],

            ['name' => 'admins.manage',      'label' => 'Manage Admins',               'module' => 'Admin Management'],
            ['name' => 'roles.manage',       'label' => 'Manage Roles & Permissions',  'module' => 'Admin Management'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                [
                    'label'  => $permission['label'],
                    'module' => $permission['module'],
                    'status' => 'active',
                ]
            );
        }
    }
}