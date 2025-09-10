<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('email_verified_at');
            $table->bigInteger('storage_used_bytes')->default(0)->after('avatar_path');
            $table->bigInteger('storage_quota_bytes')->default(1073741824)->after('storage_used_bytes'); // 1GB default
            $table->json('preferences')->nullable()->after('storage_quota_bytes');
            $table->string('timezone', 50)->default('UTC')->after('preferences');
            $table->timestamp('last_login_at')->nullable()->after('timezone');
            $table->boolean('is_active')->default(true)->after('last_login_at');
            $table->boolean('email_notifications')->default(true)->after('is_active');
            
            $table->index(['is_active', 'email_verified_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_path',
                'storage_used_bytes',
                'storage_quota_bytes',
                'preferences',
                'timezone',
                'last_login_at',
                'is_active',
                'email_notifications'
            ]);
        });
    }
};
