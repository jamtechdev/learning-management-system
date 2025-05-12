<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Create Permissions
        $permissions = [
            'create_question_fill_blank',
            'view_question_fill_blank',
            'edit_question_fill_blank',
            'delete_question_fill_blank',
            'create_question_spelling',
            'view_question_spelling',
            'edit_question_spelling',
            'delete_question_spelling',
            'create_question_rearrange',
            'view_question_rearrange',
            'edit_question_rearrange',
            'delete_question_rearrange',
            'create_question_linking',
            'view_question_linking',
            'edit_question_linking',
            'delete_question_linking',
            'create_question_true_false',
            'view_question_true_false',
            'edit_question_true_false',
            'delete_question_true_false',
            'create_question_mcq',
            'view_question_mcq',
            'edit_question_mcq',
            'delete_question_mcq',
            'create_question_image_mcq',
            'view_question_image_mcq',
            'edit_question_image_mcq',
            'delete_question_image_mcq',
            'create_question_math',
            'view_question_math',
            'edit_question_math',
            'delete_question_math',
            'create_question_grouped',
            'view_question_grouped',
            'edit_question_grouped',
            'delete_question_grouped',
            'create_question_comprehension',
            'view_question_comprehension',
            'edit_question_comprehension',
            'delete_question_comprehension',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Step 2: Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);
        $childRole = Role::firstOrCreate(['name' => 'child']);

        // Step 3: Assign Permissions to Roles
        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // Parent gets create, view, edit, and delete permissions for all question types
        $parentRole->givePermissionTo([
            'create_question_fill_blank',
            'view_question_fill_blank',
            'edit_question_fill_blank',
            'delete_question_fill_blank',
            'create_question_spelling',
            'view_question_spelling',
            'edit_question_spelling',
            'delete_question_spelling',
            'create_question_rearrange',
            'view_question_rearrange',
            'edit_question_rearrange',
            'delete_question_rearrange',
            'create_question_linking',
            'view_question_linking',
            'edit_question_linking',
            'delete_question_linking',
            'create_question_true_false',
            'view_question_true_false',
            'edit_question_true_false',
            'delete_question_true_false',
            'create_question_mcq',
            'view_question_mcq',
            'edit_question_mcq',
            'delete_question_mcq',
            'create_question_image_mcq',
            'view_question_image_mcq',
            'edit_question_image_mcq',
            'delete_question_image_mcq',
            'create_question_math',
            'view_question_math',
            'edit_question_math',
            'delete_question_math',
            'create_question_grouped',
            'view_question_grouped',
            'edit_question_grouped',
            'delete_question_grouped',
            'create_question_comprehension',
            'view_question_comprehension',
            'edit_question_comprehension',
            'delete_question_comprehension',
        ]);

        // Child gets only view permissions for all question types
        $childRole->givePermissionTo([
            'view_question_fill_blank',
            'view_question_spelling',
            'view_question_rearrange',
            'view_question_linking',
            'view_question_true_false',
            'view_question_mcq',
            'view_question_image_mcq',
            'view_question_math',
            'view_question_grouped',
            'view_question_comprehension',
        ]);

        // Step 4: Create Users
        // Create Admin User and Assign Role
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password@123')
        ]);
        $admin->assignRole($adminRole);

        // Create Parent User and Assign Role
        $parent = User::create([
            'name' => 'Parent User',
            'email' => 'parent@gmail.com',
            'password' => bcrypt('password@123')
        ]);
        $parent->assignRole($parentRole);

        // Create Child User and Assign Role
        $child = User::create([
            'name' => 'Child User',
            'email' => 'child@gmail.com',
            'password' => bcrypt('password@123')
        ]);
        $child->assignRole($childRole);
    }
}
