<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->string('likeable_type'); // Image, Album, Comment
            $table->uuid('likeable_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_ip', 45)->nullable();
            $table->timestamps();

            $table->unique(['likeable_type', 'likeable_id', 'user_id']);
            $table->index(['likeable_type', 'likeable_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
