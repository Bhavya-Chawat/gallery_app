<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->enum('privacy', ['public', 'unlisted', 'private'])->default('unlisted');
            $table->uuid('cover_image_id')->nullable();
            $table->json('image_order')->nullable(); // Ordered array of image IDs
            $table->integer('image_count')->default(0);
            $table->bigInteger('total_size_bytes')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();
            $table->softDeletes();

            $table->index(['owner_id', 'privacy', 'is_published']);
            $table->index(['slug', 'privacy']);
            $table->index(['created_at', 'privacy']);
            $table->fullText(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
