<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full system access with all permissions',
                'permissions' => Role::getDefaultPermissions()['admin'],
                'is_active' => true,
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Can upload, edit, and publish images and albums',
                'permissions' => Role::getDefaultPermissions()['editor'],
                'is_active' => true,
            ],
            [
                'name' => 'Visitor',
                'slug' => 'visitor',
                'description' => 'Can view public content and interact with images',
                'permissions' => Role::getDefaultPermissions()['visitor'],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        $this->command->info('Roles seeded successfully.');
    }
}
