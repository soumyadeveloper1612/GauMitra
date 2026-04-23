<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SidebarMenu;

class SidebarMenuSeeder extends Seeder
{
    public function run(): void
    {
        SidebarMenu::truncate();

        $dashboard = SidebarMenu::create([
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'icon' => 'bi bi-grid-fill',
            'route_name' => 'admin.dashboard',
            'active_pattern' => 'admin.dashboard',
            'custom_url' => null,
            'parent_id' => null,
            'permission_name' => null,
            'sort_order' => 1,
            'status' => 'active',
        ]);

        $users = SidebarMenu::create([
            'title' => 'Users',
            'slug' => 'users',
            'icon' => 'bi bi-people-fill',
            'route_name' => 'admin.users.index',
            'active_pattern' => 'admin.users.*',
            'custom_url' => null,
            'parent_id' => null,
            'permission_name' => 'users.view',
            'sort_order' => 2,
            'status' => 'active',
        ]);

        $gaushala = SidebarMenu::create([
            'title' => 'Gaushala',
            'slug' => 'gaushala',
            'icon' => 'bi bi-house-heart-fill',
            'route_name' => null,
            'active_pattern' => 'admin.gaushalas.*',
            'custom_url' => null,
            'parent_id' => null,
            'permission_name' => null,
            'sort_order' => 3,
            'status' => 'active',
        ]);

        SidebarMenu::create([
            'title' => 'Create Gaushala',
            'slug' => 'gaushala-create',
            'icon' => null,
            'route_name' => 'admin.gaushalas.create',
            'active_pattern' => 'admin.gaushalas.create',
            'custom_url' => null,
            'parent_id' => $gaushala->id,
            'permission_name' => 'gaushala.create',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        SidebarMenu::create([
            'title' => 'Manage Gaushala',
            'slug' => 'gaushala-manage',
            'icon' => null,
            'route_name' => 'admin.gaushalas.index',
            'active_pattern' => 'admin.gaushalas.*',
            'custom_url' => null,
            'parent_id' => $gaushala->id,
            'permission_name' => 'gaushala.view',
            'sort_order' => 2,
            'status' => 'active',
        ]);

        $reports = SidebarMenu::create([
            'title' => 'Reports',
            'slug' => 'reports',
            'icon' => 'bi bi-clipboard-data-fill',
            'route_name' => null,
            'active_pattern' => 'admin.report-cases.*',
            'custom_url' => null,
            'parent_id' => null,
            'permission_name' => null,
            'sort_order' => 4,
            'status' => 'active',
        ]);

        SidebarMenu::create([
            'title' => 'Report Cases',
            'slug' => 'report-cases',
            'icon' => null,
            'route_name' => 'admin.report-cases.index',
            'active_pattern' => 'admin.report-cases.*',
            'custom_url' => null,
            'parent_id' => $reports->id,
            'permission_name' => 'report_cases.view',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        $news = SidebarMenu::create([
            'title' => 'News & Notices',
            'slug' => 'news-notices',
            'icon' => 'bi bi-megaphone-fill',
            'route_name' => null,
            'active_pattern' => 'admin.news-notices.*',
            'custom_url' => null,
            'parent_id' => null,
            'permission_name' => null,
            'sort_order' => 5,
            'status' => 'active',
        ]);

        SidebarMenu::create([
            'title' => 'Create News & Notice',
            'slug' => 'news-notices-create',
            'icon' => null,
            'route_name' => 'admin.news-notices.create',
            'active_pattern' => 'admin.news-notices.create',
            'custom_url' => null,
            'parent_id' => $news->id,
            'permission_name' => 'news_notice.create',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        SidebarMenu::create([
            'title' => 'Manage News & Notices',
            'slug' => 'news-notices-manage',
            'icon' => null,
            'route_name' => 'admin.news-notices.index',
            'active_pattern' => 'admin.news-notices.*',
            'custom_url' => null,
            'parent_id' => $news->id,
            'permission_name' => 'news_notice.view',
            'sort_order' => 2,
            'status' => 'active',
        ]);

        $adminManagement = SidebarMenu::create([
            'title' => 'Admin Management',
            'slug' => 'admin-management',
            'icon' => 'bi bi-shield-lock-fill',
            'route_name' => null,
            'active_pattern' => 'admin.admins.*',
            'custom_url' => null,
            'parent_id' => null,
            'permission_name' => null,
            'sort_order' => 6,
            'status' => 'active',
        ]);

        SidebarMenu::create([
            'title' => 'All Admins',
            'slug' => 'all-admins',
            'icon' => null,
            'route_name' => 'admin.admins.index',
            'active_pattern' => 'admin.admins.*',
            'custom_url' => null,
            'parent_id' => $adminManagement->id,
            'permission_name' => null,
            'sort_order' => 1,
            'status' => 'active',
        ]);

        SidebarMenu::create([
            'title' => 'Roles & Permissions',
            'slug' => 'roles-permissions',
            'icon' => null,
            'route_name' => 'admin.roles.index',
            'active_pattern' => 'admin.roles.*',
            'custom_url' => null,
            'parent_id' => $adminManagement->id,
            'permission_name' => null,
            'sort_order' => 2,
            'status' => 'active',
        ]);
    }
}