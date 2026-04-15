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
        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'point_balance')) {
                $table->integer('point_balance')->default(0)->after('is_active');
            }
        });

        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'point_earned')) {
                $table->integer('point_earned')->default(0)->after('late_duration');
            }
            if (!Schema::hasColumn('attendances', 'is_token_used')) {
                $table->boolean('is_token_used')->default(false)->after('point_earned');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (Schema::hasColumn('teachers', 'point_balance')) {
                $table->dropColumn('point_balance');
            }
        });

        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'point_earned')) {
                $table->dropColumn(['point_earned', 'is_token_used']);
            }
        });
    }
};
