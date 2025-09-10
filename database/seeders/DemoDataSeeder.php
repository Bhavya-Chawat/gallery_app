<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Collection;
use App\Models\Image;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo users
        $users = $this->createDemoUsers();
        
        // Create demo tags
        $tags = $this->createDemoTags();
        
        // Create demo albums
        $albums = $this->createDemoAlbums($users);
        
        // Create demo images
        $images = $this->createDemoImages($users, $albums);
        
        // Attach tags to images
        $this->attachTagsToImages($images, $tags);
        
        // Create demo collections
        $this->createDemoCollections($users, $albums, $images);
        
        $this->command->info('Demo data seeded successfully.');
    }

    private function createDemoUsers(): array
    {
        $editorRole = Role::where('slug', 'editor')->first();
        $visitorRole = Role::where('slug', 'visitor')->first();

        $users = [];

        // Create demo editor
        $editor = User::firstOrCreate(
            ['email' => 'editor@gallery.local'],
            [
                'name' => 'Demo Editor',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'storage_quota_bytes' => 2147483648, // 2GB
                'timezone' => 'UTC',
            ]
        );
        
        if (!$editor->hasRole('editor')) {
            $editor->assignRole('editor');
        }
        $users['editor'] = $editor;

        // Create demo visitors
        for ($i = 1; $i <= 3; $i++) {
            $visitor = User::firstOrCreate(
                ['email' => "visitor{$i}@gallery.local"],
                [
                    'name' => "Demo Visitor {$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'storage_quota_bytes' => 1073741824, // 1GB
                    'timezone' => 'UTC',
                ]
            );
            
            if (!$visitor->hasRole('visitor')) {
                $visitor->assignRole('visitor');
            }
            $users["visitor{$i}"] = $visitor;
        }

        return $users;
    }

    private function createDemoTags(): array
    {
        $tagNames = [
            'Nature', 'Landscape', 'Portrait', 'Street Photography', 'Architecture',
            'Wildlife', 'Macro', 'Black & White', 'Vintage', 'Modern',
            'Travel', 'Food', 'Fashion', 'Documentary', 'Abstract'
        ];

        $tags = [];
        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "Images tagged with {$name}",
                    'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                    'is_featured' => rand(0, 1) === 1,
                ]
            );
            $tags[] = $tag;
        }

        return $tags;
    }

    private function createDemoAlbums(array $users): array
    {
        $albumData = [
            [
                'title' => 'Nature Collection',
                'description' => 'Beautiful nature photographs from around the world',
                'privacy' => 'public',
                'is_published' => true,
            ],
            [
                'title' => 'Urban Landscapes',
                'description' => 'City scenes and architectural photography',
                'privacy' => 'public',
                'is_published' => true,
            ],
            [
                'title' => 'Portrait Sessions',
                'description' => 'Professional portrait photography',
                'privacy' => 'unlisted',
                'is_published' => true,
            ],
            [
                'title' => 'Travel Memories',
                'description' => 'Personal travel photography collection',
                'privacy' => 'private',
                'is_published' => false,
            ],
        ];

        $albums = [];
        foreach ($albumData as $data) {
            $album = Album::create([
                'owner_id' => $users['editor']->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'privacy' => $data['privacy'],
                'is_published' => $data['is_published'],
                'published_at' => $data['is_published'] ? now() : null,
            ]);
            $albums[] = $album;
        }

        return $albums;
    }

    private function createDemoImages(array $users, array $albums): array
    {
        $imageData = [
            [
                'title' => 'Mountain Sunrise',
                'caption' => 'Beautiful sunrise over the mountain peaks',
                'privacy' => 'public',
                'filename' => 'mountain-sunrise.jpg',
            ],
            [
                'title' => 'City at Night',
                'caption' => 'Urban landscape with city lights',
                'privacy' => 'public',
                'filename' => 'city-night.jpg',
            ],
            [
                'title' => 'Forest Path',
                'caption' => 'Peaceful walk through the forest',
                'privacy' => 'unlisted',
                'filename' => 'forest-path.jpg',
            ],
            [
                'title' => 'Ocean Waves',
                'caption' => 'Powerful waves crashing on the shore',
                'privacy' => 'public',
                'filename' => 'ocean-waves.jpg',
            ],
        ];

        $images = [];
        foreach ($imageData as $index => $data) {
            $album = $albums[$index % count($albums)];
            
            $image = Image::create([
                'owner_id' => $users['editor']->id,
                'album_id' => $album->id,
                'title' => $data['title'],
                'caption' => $data['caption'],
                'alt_text' => $data['caption'],
                'original_filename' => $data['filename'],
                'storage_path' => 'demo/' . $data['filename'],
                'mime_type' => 'image/jpeg',
                'size_bytes' => rand(500000, 2000000), // Random size between 500KB-2MB
                'width' => rand(1920, 4000),
                'height' => rand(1080, 3000),
                'privacy' => $data['privacy'],
                'license' => 'CC BY 4.0',
                'is_published' => true,
                'published_at' => now(),
                'taken_at' => now()->subDays(rand(1, 365)),
                'camera_make' => ['Canon', 'Nikon', 'Sony', 'Fujifilm'][rand(0, 3)],
                'camera_model' => 'Demo Camera',
                'views_count' => rand(0, 1000),
                'likes_count' => rand(0, 100),
                'processing_status' => [
                    'thumbnails_generated' => true,
                    'metadata_extracted' => true,
                ],
            ]);
            
            $images[] = $image;
        }

        // Update album image counts
        foreach ($albums as $album) {
            $album->updateImageCount();
        }

        return $images;
    }

    private function attachTagsToImages(array $images, array $tags): void
    {
        foreach ($images as $image) {
            // Attach 2-5 random tags to each image
            $randomTags = collect($tags)->random(rand(2, 5));
            $image->tags()->attach($randomTags);
            
            // Update tag usage counts
            foreach ($randomTags as $tag) {
                $tag->incrementUsage();
            }
        }
    }

    private function createDemoCollections(array $users, array $albums, array $images): void
    {
        $collection = Collection::create([
            'curator_id' => $users['editor']->id,
            'title' => 'Featured Photography',
            'description' => 'A curated collection of the best photography',
            'privacy' => 'public',
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Add some albums and images to the collection
        foreach (array_slice($albums, 0, 2) as $index => $album) {
            $collection->addItem($album, "Featured album #{$index}", $index);
        }

        foreach (array_slice($images, 0, 3) as $index => $image) {
            $collection->addItem($image, "Featured image #{$index}", $index + 2);
        }
    }
}
