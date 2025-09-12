<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_image', function (Blueprint $table) {
            $table->id();
            $table->uuid('collection_id');
            $table->uuid('image_id');
            $table->timestamp('added_at')->useCurrent();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            
            $table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
            $table->unique(['collection_id', 'image_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_image');
    }
};
