<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending','approval_pending','active','suspended') NOT NULL DEFAULT 'pending'");

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_dummy')->default(false)->after('is_admin');
            $table->boolean('roi_enabled')->default(true)->after('is_dummy');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_dummy', 'roi_enabled']);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending','active','suspended') NOT NULL DEFAULT 'pending'");
    }
};
