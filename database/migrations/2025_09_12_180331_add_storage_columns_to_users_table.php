<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'storage_used_bytes')) {
                $table->bigInteger('storage_used_bytes')->default(0);
            }
            if (!Schema::hasColumn('users', 'storage_quota_bytes')) {
                $table->bigInteger('storage_quota_bytes')->default(1073741824); // 1GB default
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['storage_used_bytes', 'storage_quota_bytes']);
        });
    }
};
