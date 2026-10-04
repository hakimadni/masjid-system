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
        Schema::create('finance_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->string('name');
            $table->string('code')->nullable()->index();
            $table->enum('account_type', ['cash', 'bank', 'petty_cash', 'digital'])->default('cash')->index();
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('finance_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->string('name');
            $table->enum('entry_type', ['income', 'expense'])->index();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('finance_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->foreignId('finance_account_id')->nullable()->constrained('finance_accounts')->nullOnDelete();
            $table->foreignId('finance_category_id')->nullable()->constrained('finance_categories')->nullOnDelete();
            $table->enum('entry_type', ['income', 'expense'])->index();
            $table->string('title');
            $table->date('transaction_date')->index();
            $table->decimal('amount', 14, 2);
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('pending')->index();
            $table->enum('payment_method', ['cash', 'transfer', 'qris', 'other'])->default('cash')->index();
            $table->string('reference_no')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('donors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->foreignId('donor_id')->nullable()->constrained('donors')->nullOnDelete();
            $table->foreignId('finance_transaction_id')->nullable()->constrained('finance_transactions')->nullOnDelete();
            $table->string('campaign')->default('Donasi Umum');
            $table->date('donation_date')->index();
            $table->decimal('amount', 14, 2);
            $table->enum('method', ['cash', 'transfer', 'qris', 'other'])->default('cash')->index();
            $table->enum('status', ['draft', 'pending', 'confirmed', 'rejected'])->default('pending')->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('prayer_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->date('schedule_date')->index();
            $table->string('prayer_name');
            $table->time('prayer_time')->nullable();
            $table->string('imam_name')->nullable();
            $table->string('muadzin_name')->nullable();
            $table->string('khatib_name')->nullable();
            $table->enum('status', ['draft', 'published', 'completed', 'archived'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('service_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->string('title');
            $table->enum('role_type', ['imam', 'muadzin', 'khatib', 'petugas', 'kegiatan'])->default('petugas')->index();
            $table->string('person_name');
            $table->string('location')->nullable();
            $table->timestamp('scheduled_at')->index();
            $table->enum('status', ['draft', 'published', 'completed', 'archived'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->string('title');
            $table->timestamp('start_at')->index();
            $table->timestamp('end_at')->nullable();
            $table->string('location')->nullable();
            $table->string('pic_name')->nullable();
            $table->enum('status', ['draft', 'published', 'completed', 'archived'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->string('title');
            $table->text('content');
            $table->timestamp('published_at')->nullable()->index();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('location')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('condition', ['baik', 'perlu_perbaikan', 'rusak'])->default('baik')->index();
            $table->enum('status', ['active', 'maintenance', 'archived'])->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('events');
        Schema::dropIfExists('service_schedules');
        Schema::dropIfExists('prayer_schedules');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('donors');
        Schema::dropIfExists('finance_transactions');
        Schema::dropIfExists('finance_categories');
        Schema::dropIfExists('finance_accounts');
    }
};
