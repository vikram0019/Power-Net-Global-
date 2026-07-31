<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->decimal('rank_reward_balance', 14, 2)->default(0)->after('working_balance');
        });

        DB::statement("ALTER TABLE withdrawals MODIFY COLUMN wallet_type ENUM('roi','working','rank_reward') NOT NULL");
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN wallet_type ENUM('deposit','roi','working','rank_reward') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN wallet_type ENUM('deposit','roi','working') NOT NULL");
        DB::statement("ALTER TABLE withdrawals MODIFY COLUMN wallet_type ENUM('roi','working') NOT NULL");

        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('rank_reward_balance');
        });
    }
};
