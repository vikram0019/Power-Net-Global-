<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->decimal('fee_amount', 14, 2)->default(0)->after('amount');
            $table->decimal('net_amount', 14, 2)->nullable()->after('fee_amount');
        });

        // Backfill existing rows: no fee, net = full requested amount.
        DB::table('withdrawals')->update(['net_amount' => DB::raw('amount')]);
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['fee_amount', 'net_amount']);
        });
    }
};
