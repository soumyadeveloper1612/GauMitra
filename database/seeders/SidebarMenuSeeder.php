<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SidebarMenu;

class SidebarMenuSeeder extends Seeder
{
    public function run(): void
    {
        $dashboard = SidebarMenu::updateOrCreate(
            ['menu_key' => 'dashboard'],
            [
                'title' => 'Dashboard',
                'route_name' => 'admin.dashboard',
                'super_admin_route_name' => 'superadmin.dashboard',
                'route_pattern' => 'admin.dashboard,superadmin.dashboard',
                'icon' => 'bi bi-grid-fill',
                'parent_id' => null,
                'permission_name' => null,
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        $users = SidebarMenu::updateOrCreate(
            ['menu_key' => 'users'],
            [
                'title' => 'Users',
                'route_name' => 'admin.users.index',
                'super_admin_route_name' => null,
                'route_pattern' => 'admin.users.*',
                'icon' => 'bi bi-people-fill',
                'parent_id' => null,
                'permission_name' => 'users.view',
                'sort_order' => 2,
                'status' => 'active',
            ]
        );

        $gaushala = SidebarMenu::updateOrCreate(
            ['menu_key' => 'gaushala'],
            [
                'title' => 'Gaushala',
                'route_name' => null,
                'super_admin_route_name' => null,
                'route_pattern' => null,
                'icon' => 'bi bi-house-heart-fill',
                'parent_id' => null,
                'permission_name' => null,
                'sort_order' => 3,
                'status' => 'active',
            ]
        );

        SidebarMenu::updateOrCreate(
            ['menu_key' => 'gaushala_create'],
            [
                'title' => 'Create Gaushala',
                'route_name' => 'admin.gaushalas.create',
                'super_admin_route_name' => null,
                'route_pattern' => 'admin.gaushalas.create',
                'icon' => null,
                'parent_id' => $gaushala->id,
                'permission_name' => 'gaushala.create',
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        SidebarMenu::updateOrCreate(
            ['menu_key' => 'gaushala_manage'],
            [
                'title' => 'Manage Gaushala',
                'route_name' => 'admin.gaushalas.index',
                'super_admin_route_name' => null,
                'route_pattern' => 'admin.gaushalas.*',
                'icon' => null,
                'parent_id' => $gaushala->id,
                'permission_name' => 'gaushala.view',
                'sort_order' => 2,
                'status' => 'active',
            ]
        );

        $reports = SidebarMenu::updateOrCreate(
            ['menu_key' => 'reports'],
            [
                'title' => 'Reports',
                'route_name' => null,
                'super_admin_route_name' => null,
                'route_pattern' => null,
                'icon' => 'bi bi-clipboard-data-fill',
                'parent_id' => null,
                'permission_name' => null,
                'sort_order' => 4,
                'status' => 'active',
            ]
        );

        SidebarMenu::updateOrCreate(
            ['menu_key' => 'report_cases'],
            [
                'title' => 'Report Cases',
                'route_name' => 'admin.report-cases.index',
                'super_admin_route_name' => null,
                'route_pattern' => 'admin.report-cases.*',
                'icon' => null,
                'parent_id' => $reports->id,
                'permission_name' => 'report_cases.view',
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        $news = SidebarMenu::updateOrCreate(
            ['menu_key' => 'news_notice'],
            [
                'title' => 'News & Notices',
                'route_name' => null,
                'super_admin_route_name' => null,
                'route_pattern' => null,
                'icon' => 'bi bi-megaphone-fill',
                'parent_id' => null,
                'permission_name' => null,
                'sort_order' => 5,
                'status' => 'active',
            ]
        );

        SidebarMenu::updateOrCreate(
            ['menu_key' => 'news_notice_create'],
            [
                'title' => 'Create News & Notice',
                'route_name' => 'admin.news-notices.create',
                'super_admin_route_name' => null,
                'route_pattern' => 'admin.news-notices.create',
                'icon' => null,
                'parent_id' => $news->id,
                'permission_name' => 'news_notice.create',
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        SidebarMenu::updateOrCreate(
            ['menu_key' => 'news_notice_manage'],
            [
                'title' => 'Manage News & Notices',
                'route_name' => 'admin.news-notices.index',
                'super_admin_route_name' => null,
                'route_pattern' => 'admin.news-notices.*',
                'icon' => null,
                'parent_id' => $news->id,
                'permission_name' => 'news_notice.view',
                'sort_order' => 2,
                'status' => 'active',
            ]
        );

        $adminManagement = SidebarMenu::updateOrCreate(
            ['menu_key' => 'admin_management'],
            [
                'title' => 'Admin Management',
                'route_name' => null,
                'super_admin_route_name' => null,
                'route_pattern' => null,
                'icon' => 'bi bi-shield-lock-fill',
                'parent_id' => null,
                'permission_name' => null,
                'sort_order' => 6,
                'status' => 'active',
            ]
        );

        SidebarMenu::updateOrCreate(
            ['menu_key' => 'all_admins'],
            [
                'title' => 'All Admins',
                'route_name' => 'admin.admins.index',
                'super_admin_route_name' => null,
                'route_pattern' => 'admin.admins.*',
                'icon' => null,
                'parent_id' => $adminManagement->id,
                'permission_name' => null,
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        SidebarMenu::updateOrCreate(
            ['menu_key' => 'roles_permissions'],
            [
                'title' => 'Roles & Permissions',
                'route_name' => 'admin.roles.index',
                'super_admin_route_name' => null,
                'route_pattern' => 'admin.roles.*',
                'icon' => null,
                'parent_id' => $adminManagement->id,
                'permission_name' => null,
                'sort_order' => 2,
                'status' => 'active',
            ]
        );

        $sidebarSettings = SidebarMenu::updateOrCreate(
            ['menu_key' => 'sidebar_settings'],
            [
                'title' => 'Sidebar Settings',
                'route_name' => null,
                'super_admin_route_name' => null,
                'route_pattern' => null,
                'icon' => 'bi bi-layout-sidebar-inset',
                'parent_id' => null,
                'permission_name' => null,
                'sort_order' => 7,
                'status' => 'active',
            ]
        );

        SidebarMenu::updateOrCreate(
            ['menu_key' => 'sidebar_menus_manage'],
            [
                'title' => 'Sidebar Menus',
                'route_name' => 'admin.sidebar-menus.index',
                'super_admin_route_name' => null,
                'route_pattern' => 'admin.sidebar-menus.*',
                'icon' => null,
                'parent_id' => $sidebarSettings->id,
                'permission_name' => null,
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        SidebarMenu::updateOrCreate(
            ['menu_key' => 'menu_access_assign'],
            [
                'title' => 'Menu Access',
                'route_name' => 'admin.menu-access.index',
                'super_admin_route_name' => null,
                'route_pattern' => 'admin.menu-access.*',
                'icon' => null,
                'parent_id' => $sidebarSettings->id,
                'permission_name' => null,
                'sort_order' => 2,
                'status' => 'active',
            ]
        );
    }
}