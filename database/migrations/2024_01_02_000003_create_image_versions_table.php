<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('image_id')->constrained()->onDelete('cascade');
            $table->enum('variant', ['original', 'thumb', 'small', 'medium', 'large']);
            $table->enum('format', ['jpeg', 'png', 'webp', 'avif']);
            $table->string('storage_path', 1000);
            $table->string('mime_type', 100);
            $table->integer('width');
            $table->integer('height');
            $table->bigInteger('size_bytes');
            $table->integer('quality')->nullable();
            $table->boolean('is_generated')->default(false);
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->unique(['image_id', 'variant', 'format']);
            $table->index(['image_id', 'variant']);
            $table->index(['format', 'is_generated']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_versions');
    }
};
