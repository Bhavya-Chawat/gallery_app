<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Modern Image Gallery',
                'type' => 'string',
                'description' => 'The name of the gallery site',
                'group' => 'general',
            ],
            [
                'key' => 'site_description',
                'value' => 'A modern, responsive image gallery built with Laravel',
                'type' => 'text',
                'description' => 'Description of the gallery site',
                'group' => 'general',
            ],
            [
                'key' => 'default_privacy',
                'value' => 'unlisted',
                'type' => 'select',
                'description' => 'Default privacy setting for new uploads',
                'group' => 'uploads',
            ],
            [
                'key' => 'max_upload_size',
                'value' => '52428800',
                'type' => 'integer',
                'description' => 'Maximum upload size in bytes (50MB)',
                'group' => 'uploads',
            ],
            [
                'key' => 'allowed_mimes',
                'value' => 'jpg,jpeg,png,webp,avif',
                'type' => 'string',
                'description' => 'Allowed file types for upload',
                'group' => 'uploads',
            ],
            [
                'key' => 'enable_registration',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow new user registrations',
                'group' => 'users',
            ],
            [
                'key' => 'enable_comments',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable commenting on images',
                'group' => 'features',
            ],
            [
                'key' => 'enable_likes',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable liking images',
                'group' => 'features',
            ],
            [
                'key' => 'comment_moderation',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Require comment approval',
                'group' => 'moderation',
            ],
            [
                'key' => 'presign_ttl',
                'value' => '900',
                'type' => 'integer',
                'description' => 'Presigned URL TTL in seconds (15 minutes)',
                'group' => 'storage',
            ],
            [
                'key' => 'download_ttl',
                'value' => '300',
                'type' => 'integer',
                'description' => 'Download URL TTL in seconds (5 minutes)',
                'group' => 'storage',
            ],
            [
                'key' => 'thumbnail_sizes',
                'value' => '{"thumb":200,"small":640,"medium":1280,"large":1920}',
                'type' => 'json',
                'description' => 'Image thumbnail sizes',
                'group' => 'processing',
            ],
            [
                'key' => 'image_quality',
                'value' => '{"jpeg":85,"webp":80}',
                'type' => 'json',
                'description' => 'Image compression quality settings',
                'group' => 'processing',
            ],
            [
                'key' => 'default_role',
                'value' => 'visitor',
                'type' => 'string',
                'description' => 'Default role for new users',
                'group' => 'users',
            ],
            [
                'key' => 'storage_quota_default',
                'value' => '1073741824',
                'type' => 'integer',
                'description' => 'Default storage quota in bytes (1GB)',
                'group' => 'storage',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Settings seeded successfully.');
    }
}
