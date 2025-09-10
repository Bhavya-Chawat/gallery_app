<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@gallery.local');
        $adminPassword = env('ADMIN_PASSWORD', 'admin123');
        $adminName = env('ADMIN_NAME', 'Gallery Administrator');

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
                'is_active' => true,
                'storage_quota_bytes' => 10737418240, // 10GB for admin
                'timezone' => 'UTC',
            ]
        );

        // Assign admin role
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole && !$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $this->command->info("Admin user created successfully:");
        $this->command->info("Email: {$adminEmail}");
        $this->command->info("Password: {$adminPassword}");
        $this->command->warn("Please change the password after first login!");
    }
}
