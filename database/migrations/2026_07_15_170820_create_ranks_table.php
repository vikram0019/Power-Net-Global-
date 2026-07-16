<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 60);
            $table->string('package_group', 30);
            $table->decimal('own_invest_required', 14, 2);
            $table->decimal('team_business_required', 16, 2);
            $table->decimal('reward_amount', 14, 2);
            $table->unsignedTinyInteger('legs_open');
            $table->unsignedTinyInteger('levels_unlocked');
            $table->unsignedTinyInteger('sort_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranks');
    }
};
