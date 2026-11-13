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
        // 1. Fitur F: Kolom tambahan profil pelanggan yang relevan untuk perizinan
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'pic_position')) {
                $table->string('pic_position', 100)->nullable()->after('company_name');
            }
            if (! Schema::hasColumn('users', 'nib')) {
                $table->string('nib', 30)->nullable()->after('pic_position');
            }
            if (! Schema::hasColumn('users', 'npwp')) {
                $table->string('npwp', 30)->nullable()->after('nib');
            }
            if (! Schema::hasColumn('users', 'business_field')) {
                $table->string('business_field', 150)->nullable()->after('npwp');
            }
        });

        // 2. Fitur H: Minimum transaksi voucher & rincian order / anti-duplikasi checkout
        Schema::table('promos', function (Blueprint $table) {
            if (! Schema::hasColumn('promos', 'min_transaction')) {
                $table->decimal('min_transaction', 15, 2)->default(0)->after('discount_percentage');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable();
            }
            if (! Schema::hasColumn('orders', 'promo_code')) {
                $table->string('promo_code', 50)->nullable();
            }
            if (! Schema::hasColumn('orders', 'base_price')) {
                $table->decimal('base_price', 15, 2)->default(0);
            }
            if (! Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0);
            }
            if (! Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (! Schema::hasColumn('orders', 'checkout_token')) {
                $table->string('checkout_token', 64)->nullable()->unique();
            }
            if (! Schema::hasColumn('orders', 'current_stage')) {
                $table->string('current_stage', 100)->default('Pesanan Diterima');
            }
            if (! Schema::hasColumn('orders', 'progress_percentage')) {
                $table->unsignedTinyInteger('progress_percentage')->default(15);
            }
            if (! Schema::hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
        });

        // 3. Fitur I: Tabel invoices & payment_logs serta kolom tambahan payments
        if (! Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('invoice_number', 100)->unique();
                $table->timestamp('issued_at')->useCurrent();
                $table->date('due_date')->nullable();
                $table->string('customer_name');
                $table->string('customer_company')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone', 50)->nullable();
                $table->text('customer_address')->nullable();
                $table->string('service_name');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->string('payment_status', 30)->default('unpaid');
                $table->timestamps();
            });
        }

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
            if (! Schema::hasColumn('payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (! Schema::hasColumn('payments', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->nullable();
            }
            if (! Schema::hasColumn('payments', 'original_filename')) {
                $table->string('original_filename')->nullable();
            }
            if (! Schema::hasColumn('payments', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable();
            }
            if (! Schema::hasColumn('payments', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
            if (! Schema::hasColumn('payments', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable();
            }
            if (! Schema::hasColumn('payments', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            if (! Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
        });

        if (! Schema::hasTable('payment_logs')) {
            Schema::create('payment_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->unsignedBigInteger('payment_id')->nullable();
                $table->string('status', 30);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('changed_by')->nullable();
                $table->timestamps();
            });
        }

        // 4. Fitur J: Metadata persyaratan & riwayat revisi dokumen (file versioning)
        Schema::table('service_requirements', function (Blueprint $table) {
            if (! Schema::hasColumn('service_requirements', 'document_type')) {
                $table->string('document_type', 60)->default('Persyaratan Teknis');
            }
            if (! Schema::hasColumn('service_requirements', 'description')) {
                $table->text('description')->nullable();
            }
        });

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
            if (! Schema::hasColumn('client_documents', 'document_type')) {
                $table->string('document_type', 60)->nullable();
            }
            if (! Schema::hasColumn('client_documents', 'original_filename')) {
                $table->string('original_filename')->nullable();
            }
            if (! Schema::hasColumn('client_documents', 'mime_type')) {
                $table->string('mime_type', 120)->nullable();
            }
            if (! Schema::hasColumn('client_documents', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (! Schema::hasColumn('client_documents', 'revision_note')) {
                $table->text('revision_note')->nullable();
            }
            if (! Schema::hasColumn('client_documents', 'uploaded_by')) {
                $table->unsignedBigInteger('uploaded_by')->nullable();
            }
            if (! Schema::hasColumn('client_documents', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable();
            }
            if (! Schema::hasColumn('client_documents', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
        });

        if (! Schema::hasTable('client_document_versions')) {
            Schema::create('client_document_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_document_id')->constrained('client_documents')->onDelete('cascade');
                $table->unsignedInteger('version_number')->default(1);
                $table->string('file_path');
                $table->string('original_filename');
                $table->string('file_size', 50)->nullable();
                $table->string('mime_type', 120)->nullable();
                $table->text('revision_notes')->nullable();
                $table->string('review_status', 30)->default('uploaded');
                $table->text('reviewer_notes')->nullable();
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();
            });
        }

        // 5. Fitur K: Pelacakan progres proyek dengan kode tahap, status & persentase
        Schema::table('project_trackings', function (Blueprint $table) {
            if (! Schema::hasColumn('project_trackings', 'stage_code')) {
                $table->string('stage_code', 50)->nullable();
            }
            if (! Schema::hasColumn('project_trackings', 'stage_order')) {
                $table->unsignedTinyInteger('stage_order')->default(1);
            }
            if (! Schema::hasColumn('project_trackings', 'status')) {
                $table->string('status', 30)->default('completed');
            }
            if (! Schema::hasColumn('project_trackings', 'progress_percentage')) {
                $table->unsignedTinyInteger('progress_percentage')->default(0);
            }
            if (! Schema::hasColumn('project_trackings', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
        });

        // 6. Fitur L: Relasi service_id & keunikan 1 ulasan per pesanan
        Schema::table('reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('reviews', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable();
            }
        });

        $indexes = Schema::getIndexes('reviews');
        $hasOrderUnique = collect($indexes)->contains(function (array $idx): bool {
            return ($idx['unique'] ?? false) && ($idx['columns'] ?? []) === ['order_id'];
        });

        if (! $hasOrderUnique) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unique('order_id', 'reviews_order_id_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_document_versions');
        Schema::dropIfExists('payment_logs');
        Schema::dropIfExists('invoices');
    }
};
