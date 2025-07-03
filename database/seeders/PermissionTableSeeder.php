<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions based on modules and actions
        $permissions = [
            // User management
            'manage_users',  // create/edit/delete/view users (for admin)

            // Parent Portal
            'parent_create_child',
            'parent_view_child',
            'parent_edit_child',
            'parent_delete_child',
            'parent_monitor_assessment',
            'parent_redeem_prizes',
            'parent_manage_subscription',

            // Child (student) permissions
            'child_view_assessment',
            'child_complete_assessment',
            'child_view_scores',
            'child_access_question_bank',

            // Assessment engine (admin)
            'create_question',
            'edit_question',
            'delete_question',
            'view_question',

            // Question bank (practice mode)
            'access_question_bank',

            // Gems and rewards (parent)
            'view_gems',
            'redeem_gems',

            // Subscription (parent)
            'view_subscription',
            'manage_subscription',

            // Prize shop (parent)
            'view_prizes',
            'redeem_prizes',
        ];

        // Create permissions
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);
        $childRole = Role::firstOrCreate(['name' => 'child']);

        // Assign all permissions to admin
        $adminRole->syncPermissions(Permission::all());

        // Assign selected permissions to parent
        $parentPerms = Permission::whereIn('name', [
            'parent_create_child',
            'parent_view_child',
            'parent_edit_child',
            'parent_delete_child',
            'parent_monitor_assessment',
            'parent_redeem_prizes',
            'parent_manage_subscription',
            'view_gems',
            'redeem_gems',
            'view_subscription',
            'manage_subscription',
            'view_prizes',
            'redeem_prizes',
            'access_question_bank',
        ])->get();

        $parentRole->syncPermissions($parentPerms);

        // Assign limited permissions to child
        $childPerms = Permission::whereIn('name', [
            'child_view_assessment',
            'child_complete_assessment',
            'child_view_scores',
            'access_question_bank',
        ])->get();

        $childRole->syncPermissions($childPerms);

        // Create gmail users and assign roles

        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password@123'),
                'address' => 'Admin Address',
                'phone' => '0000000000'
            ]
        );
        $admin->assignRole($adminRole);

        $parent = User::firstOrCreate(
            ['email' => 'parent@gmail.com'],
            [
                'first_name' => 'Parent',
                'last_name' => 'User',
                'password' => Hash::make('password@123'),
                'address' => 'Parent Address',
                'phone' => '1111111111',
                'avatar' => 'images/logo/default-avatar.png',
            ]
        );
        $parent->assignRole($parentRole);

        $child = User::firstOrCreate(
            ['email' => 'child@gmail.com'],
            [
                'first_name' => 'Child',
                'last_name' => 'User',
                'password' => Hash::make('password@123'),
                'parent_id' => $parent->id,
                'lock_code' => '123456',
                'lock_code_enabled' => true,
                'address' => 'Child Address',
                'phone' => '2222222222',
                'avatar' => 'images/logo/default-avatar.png',
            ]
        );
        $child->assignRole($childRole);
    }
}
