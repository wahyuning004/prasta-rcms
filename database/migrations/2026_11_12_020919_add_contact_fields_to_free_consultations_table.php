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
        if (Schema::hasTable('free_consultations')) {
            Schema::table('free_consultations', function (Blueprint $table) {
                if (! Schema::hasColumn('free_consultations', 'name')) {
                    $table->string('name')->nullable();
                }
                if (! Schema::hasColumn('free_consultations', 'email')) {
                    $table->string('email')->nullable();
                }
                if (! Schema::hasColumn('free_consultations', 'phone')) {
                    $table->string('phone', 50)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('free_consultations')) {
            Schema::table('free_consultations', function (Blueprint $table) {
                if (Schema::hasColumn('free_consultations', 'phone')) {
                    $table->dropColumn('phone');
                }
                if (Schema::hasColumn('free_consultations', 'email')) {
                    $table->dropColumn('email');
                }
                if (Schema::hasColumn('free_consultations', 'name')) {
                    $table->dropColumn('name');
                }
            });
        }
    }
};
