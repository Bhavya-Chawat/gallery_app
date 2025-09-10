<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('view_counts', function (Blueprint $table) {
            $table->id();
            $table->string('viewable_type'); // Image, Album
            $table->uuid('viewable_id');
            $table->date('date');
            $table->integer('count')->default(1);
            $table->json('metadata')->nullable(); // Additional analytics data
            $table->timestamps();

            $table->unique(['viewable_type', 'viewable_id', 'date']);
            $table->index(['viewable_type', 'viewable_id', 'date']);
            $table->index(['date', 'count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('view_counts');
    }
};
