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
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                if (! Schema::hasColumn('services', 'requirements_summary')) {
                    $table->text('requirements_summary')->nullable()->after('price');
                }
                if (! Schema::hasColumn('services', 'estimation_time')) {
                    $table->string('estimation_time', 100)->nullable()->after('requirements_summary');
                }
                if (! Schema::hasColumn('services', 'cost')) {
                    $table->decimal('cost', 12, 2)->default(0)->after('estimation_time');
                }
            });
        }

        if (! Schema::hasTable('service_requirements')) {
            Schema::create('service_requirements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->string('document_name');
                $table->boolean('is_mandatory')->nullable()->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('promos')) {
            Schema::create('promos', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('title')->default('Voucher Diskon 10%');
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('discount_percentage', 5, 2)->nullable();
                $table->string('terms')->nullable()->default('Berlaku untuk seluruh layanan legalitas & perizinan');
                $table->string('badge_text', 100)->nullable()->default('Khusus 5 Orang Tercepat');
                $table->integer('quota')->default(50);
                $table->integer('used_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured_homepage')->default(true);
                $table->string('voucher_image')->nullable();
                $table->string('exhausted_image')->nullable();
                $table->date('expired_date')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('promo_claims')) {
            Schema::create('promo_claims', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promo_id')->constrained('promos')->onDelete('cascade');
                $table->string('name');
                $table->string('phone', 50);
                $table->string('email')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('claimed_at')->useCurrent();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number', 100)->unique();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('promo_id')->nullable()->constrained('promos')->onDelete('set null');
                $table->unsignedBigInteger('service_id')->nullable();
                $table->decimal('total_cost', 12, 2);
                $table->string('status', 30)->default('pending');
                $table->timestamps();
            });
        } else {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'service_id')) {
                    $table->unsignedBigInteger('service_id')->nullable();
                }
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->decimal('price', 12, 2);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->string('payment_method', 100);
                $table->string('bank_name', 100)->nullable();
                $table->string('sender_name', 150)->nullable();
                $table->decimal('amount', 12, 2);
                $table->string('payment_proof')->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 30)->default('waiting_confirmation');
                $table->timestamps();
            });
        } else {
            Schema::table('payments', function (Blueprint $table) {
                if (! Schema::hasColumn('payments', 'bank_name')) {
                    $table->string('bank_name', 100)->nullable();
                }
                if (! Schema::hasColumn('payments', 'sender_name')) {
                    $table->string('sender_name', 150)->nullable();
                }
                if (! Schema::hasColumn('payments', 'notes')) {
                    $table->text('notes')->nullable();
                }
            });
        }

        if (! Schema::hasTable('client_documents')) {
            Schema::create('client_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->string('document_name')->nullable();
                $table->foreignId('service_requirement_id')->constrained('service_requirements')->onDelete('cascade');
                $table->string('file_path');
                $table->integer('version')->default(1);
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('file_size', 50)->nullable();
                $table->string('status', 30)->default('uploaded');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('client_documents', function (Blueprint $table) {
                if (! Schema::hasColumn('client_documents', 'document_name')) {
                    $table->string('document_name')->nullable();
                }
                if (! Schema::hasColumn('client_documents', 'version')) {
                    $table->integer('version')->default(1);
                }
                if (! Schema::hasColumn('client_documents', 'parent_id')) {
                    $table->unsignedBigInteger('parent_id')->nullable();
                }
                if (! Schema::hasColumn('client_documents', 'file_size')) {
                    $table->string('file_size', 50)->nullable();
                }
                if (! Schema::hasColumn('client_documents', 'notes')) {
                    $table->text('notes')->nullable();
                }
            });
        }

        if (! Schema::hasTable('project_trackings')) {
            Schema::create('project_trackings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->string('stage_name');
                $table->text('description')->nullable();
                $table->timestamp('date_recorded')->nullable()->useCurrent();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->unsignedBigInteger('service_id')->nullable();
                $table->integer('rating');
                $table->text('comment')->nullable();
                $table->string('status', 30)->default('published');
                $table->boolean('is_featured')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('reviews', function (Blueprint $table) {
                if (! Schema::hasColumn('reviews', 'service_id')) {
                    $table->unsignedBigInteger('service_id')->nullable();
                }
            });
        }

        if (! Schema::hasTable('free_consultations')) {
            Schema::create('free_consultations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('subject');
                $table->text('message');
                $table->string('attachment')->nullable();
                $table->text('response')->nullable();
                $table->string('status', 30)->default('pending');
                $table->timestamps();
            });
        } else {
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

        if (! Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Baseline safeguard migration: do not drop existing production tables on rollback.
    }
};
