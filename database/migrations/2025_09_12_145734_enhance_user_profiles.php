<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('email_verified_at');
            $table->string('website')->nullable()->after('bio');
            $table->json('social_links')->nullable()->after('website');
            $table->enum('profile_visibility', ['public', 'private'])->default('public')->after('social_links');
            $table->boolean('show_email_publicly')->default(false)->after('profile_visibility');
            $table->boolean('show_stats_publicly')->default(true)->after('show_email_publicly');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio', 'website', 'social_links', 
                'profile_visibility', 'show_email_publicly', 'show_stats_publicly'
            ]);
        });
    }
};
