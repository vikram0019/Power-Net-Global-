<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE withdrawals MODIFY COLUMN wallet_type ENUM('roi','working','rank_reward','deposit') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE withdrawals MODIFY COLUMN wallet_type ENUM('roi','working','rank_reward') NOT NULL");
    }
};
