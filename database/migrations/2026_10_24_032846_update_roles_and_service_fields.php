<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah kolom role pada tabel users menjadi string fleksibel
        if (Schema::hasTable('users') && DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'klien'");
        }

        // 2. Pastikan tabel categories ada
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Pastikan tabel services ada dan memiliki kolom requirements_info, timeline_estimation, price
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->text('requirements_info')->nullable();
                $table->string('timeline_estimation')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->timestamps();
            });
        } else {
            Schema::table('services', function (Blueprint $table) {
                if (!Schema::hasColumn('services', 'requirements_info')) {
                    $table->text('requirements_info')->nullable()->after('description');
                }
                if (!Schema::hasColumn('services', 'timeline_estimation')) {
                    $table->string('timeline_estimation')->nullable()->after('requirements_info');
                }
                if (!Schema::hasColumn('services', 'price')) {
                    $table->decimal('price', 15, 2)->default(0)->after('timeline_estimation');
                }
            });

            // Salin data dari kolom lama jika ada
            if (Schema::hasColumn('services', 'requirements_summary')) {
                DB::statement("UPDATE services SET requirements_info = requirements_summary WHERE requirements_info IS NULL OR requirements_info = ''");
            }
            if (Schema::hasColumn('services', 'estimation_time')) {
                DB::statement("UPDATE services SET timeline_estimation = estimation_time WHERE timeline_estimation IS NULL OR timeline_estimation = ''");
            }
            if (Schema::hasColumn('services', 'cost')) {
                DB::statement("UPDATE services SET price = cost WHERE price = 0 OR price IS NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert role column to enum
        if (Schema::hasTable('users')) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','konsultan','klien') NOT NULL DEFAULT 'klien'");
        }
    }
};
