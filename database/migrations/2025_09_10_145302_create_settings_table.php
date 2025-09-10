<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();           // Setting key
            $table->text('value')->nullable();         // Current value
            $table->string('type')->default('string'); // Data type: string, int, bool, json etc.
            $table->string('description')->nullable(); // Description for admins
            $table->string('group')->nullable();       // Setting group/category
            $table->timestamps();                      // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
