<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            SettingsSeeder::class,
        ]);

        // Only seed demo data in local environment
        if (app()->environment('local')) {
            $this->call([
                DemoDataSeeder::class,
            ]);
        }
    }
}
