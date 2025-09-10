<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('collection_id')->constrained()->onDelete('cascade');
            $table->string('collectable_type'); // Album or Image
            $table->uuid('collectable_id');
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable(); // Optional item-specific description
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();

            $table->index(['collection_id', 'sort_order']);
            $table->index(['collectable_type', 'collectable_id']);
            $table->unique(['collection_id', 'collectable_type', 'collectable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_items');
    }
};
