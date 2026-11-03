<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom batas kuota dan status pada tabel promos
        if (Schema::hasTable('promos')) {
            Schema::table('promos', function (Blueprint $table) {
                if (!Schema::hasColumn('promos', 'quota')) {
                    $table->integer('quota')->default(50)->after('discount_percentage');
                }
                if (!Schema::hasColumn('promos', 'used_count')) {
                    $table->integer('used_count')->default(0)->after('quota');
                }
                if (!Schema::hasColumn('promos', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('used_count');
                }
            });
        }

        // 2. Tambah kolom moderasi status pada tabel reviews
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('reviews', 'status')) {
                    $table->string('status', 30)->default('published')->after('comment');
                }
                if (!Schema::hasColumn('reviews', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('status');
                }
            });
        }

        // 3. Buat tabel private_archives untuk manajemen direktori arsip privat server internal
        if (!Schema::hasTable('private_archives')) {
            Schema::create('private_archives', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->string('title');
                $table->string('category', 100)->default('Sertifikat Resmi');
                $table->string('file_path');
                $table->string('file_size', 50)->nullable();
                $table->string('uploaded_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('promos')) {
            Schema::table('promos', function (Blueprint $table) {
                if (Schema::hasColumn('promos', 'quota')) {
                    $table->dropColumn(['quota', 'used_count', 'is_active']);
                }
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (Schema::hasColumn('reviews', 'status')) {
                    $table->dropColumn(['status', 'is_featured']);
                }
            });
        }

        Schema::dropIfExists('private_archives');
    }
};
