<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('curator_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('privacy', ['public', 'unlisted', 'private'])->default('public');
            $table->uuid('cover_image_id')->nullable();
            $table->unsignedInteger('images_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['privacy', 'is_published']);
            $table->index('curator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
