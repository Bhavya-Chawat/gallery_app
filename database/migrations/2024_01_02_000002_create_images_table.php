<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('album_id')->nullable()->constrained('albums')->onDelete('set null');
            $table->string('title', 255)->nullable();
            $table->text('caption')->nullable();
            $table->string('alt_text', 500)->nullable();
            $table->string('slug', 255)->unique();
            $table->string('original_filename', 500);
            $table->string('storage_path', 1000); // S3/MinIO key path
            $table->string('mime_type', 100);
            $table->bigInteger('size_bytes');
            $table->integer('width');
            $table->integer('height');
            $table->float('aspect_ratio');
            $table->enum('privacy', ['public', 'unlisted', 'private'])->default('unlisted');
            $table->string('license', 100)->nullable();
            $table->json('exif_data')->nullable(); // EXIF/IPTC metadata
            $table->json('processing_status')->nullable(); // thumbnail generation status
            $table->timestamp('taken_at')->nullable(); // From EXIF date
            $table->string('camera_make', 100)->nullable();
            $table->string('camera_model', 100)->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_downloads')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['owner_id', 'privacy', 'is_published']);
            $table->index(['album_id', 'privacy']);
            $table->index(['privacy', 'is_published', 'created_at']);
            $table->index(['camera_make', 'camera_model']);
            $table->index('taken_at');
            $table->fullText(['title', 'caption', 'alt_text']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
